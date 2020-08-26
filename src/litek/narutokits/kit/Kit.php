<?php

namespace litek\narutokits\kit;

use Exception;
use litek\narutokits\NarutoKits;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\permission\Permission;
use pocketmine\Player;

class Kit implements Wearable
{
    /** @var string */
    private $name;
    /** @var array */
    private $data;
    /** @var EffectInstance[] */
    private $effects = [];
    /** @var Item */
    private $helmet;
    /** @var Item */
    private $chestplate;
    /** @var Item */
    private $boots;
    /** @var Item */
    private $leggings;
    /** @var Item[] */
    private $items = [];

    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
        try {
            $this->load();
        } catch (Exception $e) {
            $this->getPlugin()->getLogger()->warning($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function load(): void
    {
        if (isset($this->data['permission'])) {
            Permission::loadPermission($this->data['permission'], ['default' => 'op']);
        }
        if (isset($this->data['helmet'])) {
            $this->helmet = Item::get($this->data['helmet']['id']);
            if (isset($this->data['helmet']['enchantments'])) {
                foreach ($this->data['helmet']['enchantments'] as $enchantment => $level) {
                    $this->helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['chestplate'])) {
            $this->chestplate = Item::get($this->data['chestplate']['id']);
            if (isset($this->data['chestplate']['enchantments'])) {
                foreach ($this->data['chestplate']['enchantments'] as $enchantment => $level) {
                    $this->chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['leggings'])) {
            $this->leggings = Item::get($this->data['leggings']['id']);
            if (isset($this->data['leggings']['enchantments'])) {
                foreach ($this->data['leggings']['enchantments'] as $enchantment => $level) {
                    $this->leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['boots'])) {
            $this->boots = Item::get($this->data['boots']['id']);
            if (isset($this->data['boots']['enchantments'])) {
                foreach ($this->data['boots']['enchantments'] as $enchantment => $level) {
                    $this->boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['items'])) {
            foreach ($this->data['items'] as $item => $properties) {
                $item = Item::get($item, $properties['meta'], $properties['count']);
                if (isset($properties['enchantments'])) {
                    foreach ($properties['enchantments'] as $enchantment => $level) {
                        $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                    }
                }
                $this->items[] = $item;
            }
        }
        if (isset($this->data['effects'])) {
            foreach ($this->data['effects'] as $effect => $properties) {
                $this->effects[$effect] = new EffectInstance(Effect::getEffectByName($effect), $properties['duration'], $properties['level']);
            }
        }
    }

    public function applyToPlayer(Player $player): void
    {
        if (!$player->hasPermission($this->data['permission'])) {
            $player->sendMessage("§cVocê não tem permissão para usar este kit.");
            return;
        }
        if ($this->helmet instanceof Item) {
            $player->getArmorInventory()->setHelmet($this->helmet);
        }
        if ($this->chestplate instanceof Item) {
            $player->getArmorInventory()->setChestplate($this->chestplate);
        }
        if ($this->leggings instanceof Item) {
            $player->getArmorInventory()->setLeggings($this->leggings);
        }
        if ($this->boots instanceof Item) {
            $player->getArmorInventory()->setBoots($this->boots);
        }
        foreach ($this->effects as $effect) {
            $player->addEffect($effect);
        }
        foreach ($this->items as $item) {
            if ($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
            }
        }
    }

    public function getArmorItems(): array
    {
        return [
            $this->helmet,
            $this->chestplate,
            $this->leggings,
            $this->boots
        ];
    }

    /**
     * @return NarutoKits
     */
    public function getPlugin(): NarutoKits
    {
        return NarutoKits::getInstance();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}