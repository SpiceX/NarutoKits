<?php

namespace litek\narutokits\kit;

use Exception;
use litek\narutokits\command\Cooldown;
use litek\narutokits\NarutoKits;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\utils\Color;

class Kit implements Wearable, Listener
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
    /** @var int */
    private $cooldown;

    public function __construct(string $name, array $data)
    {
        $this->name = $name;
        $this->data = $data;
        try {
            $this->load();
        } catch (Exception $e) {
            $this->getPlugin()->getLogger()->warning($e->getMessage());
        }
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
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
            $this->helmet->setCustomName("Capacete " . $this->name);
            if ($this->name === 'rinnegan') {
                $this->helmet->getNamedTag()->setInt("customColor", Color::fromARGB(0xffffff)->toARGB());
            }
            if (isset($this->data['helmet']['enchantments'])) {
                foreach ($this->data['helmet']['enchantments'] as $enchantment => $level) {
                    $this->helmet->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['chestplate'])) {
            $this->chestplate = Item::get($this->data['chestplate']['id']);
            $this->chestplate->setCustomName("Frente " . $this->name);
            if (isset($this->data['chestplate']['enchantments'])) {
                foreach ($this->data['chestplate']['enchantments'] as $enchantment => $level) {
                    $this->chestplate->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['leggings'])) {
            $this->leggings = Item::get($this->data['leggings']['id']);
            $this->leggings->setCustomName("Calça " . $this->name);
            if (isset($this->data['leggings']['enchantments'])) {
                foreach ($this->data['leggings']['enchantments'] as $enchantment => $level) {
                    $this->leggings->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['boots'])) {
            $this->boots = Item::get($this->data['boots']['id']);
            $this->boots->setCustomName("Chuteiras " . $this->name);
            if (isset($this->data['boots']['enchantments'])) {
                foreach ($this->data['boots']['enchantments'] as $enchantment => $level) {
                    $this->boots->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($enchantment), $level));
                }
            }
        }
        if (isset($this->data['items'])) {
            foreach ($this->data['items'] as $item => $properties) {
                $item = Item::get($item, $properties['meta'], $properties['count']);
                $item->setCustomName("{$item->getName()} {$this->name}");
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
        if (isset($this->data['cooldown']) && is_string($this->data['cooldown'])) {
            $this->cooldown = $this->data['cooldown'];
        }
    }

    public function onPickItem(InventoryPickupItemEvent $event)
    {
        $item = $event->getItem();
        if ($this->helmet->getCustomName() === $item->getItem()->getCustomName()) {
            $inv = $event->getInventory();
            if ($inv instanceof PlayerInventory) {
                $player = $inv->getHolder();
                if (!$player->hasPermission($this->data['permission'])) {
                    $event->setCancelled();
                }
            }
        }
        if ($this->chestplate->getCustomName() === $item->getItem()->getCustomName()) {
            $inv = $event->getInventory();
            if ($inv instanceof PlayerInventory) {
                $player = $inv->getHolder();
                if (!$player->hasPermission($this->data['permission'])) {
                    $event->setCancelled();
                }
            }
        }
        if ($this->leggings->getCustomName() === $item->getItem()->getCustomName()) {
            $inv = $event->getInventory();
            if ($inv instanceof PlayerInventory) {
                $player = $inv->getHolder();
                if (!$player->hasPermission($this->data['permission'])) {
                    $event->setCancelled();
                }
            }
        }
        if ($this->boots->getCustomName() === $item->getItem()->getCustomName()) {
            $inv = $event->getInventory();
            if ($inv instanceof PlayerInventory) {
                $player = $inv->getHolder();
                if (!$player->hasPermission($this->data['permission'])) {
                    $event->setCancelled();
                }
            }
        }
    }

    public function applyToPlayer(Player $player): void
    {
        if (!$player->hasPermission($this->data['permission'])) {
            $player->sendMessage("§cVocê não tem permissão para usar este kit.");
            return;
        }
        if ($this->getPlugin()->getCooldownManager()->isExpired($player, $this)) {
            $timeleft = $this->getPlugin()->getCooldownManager()->getTimeLeft($player, $this);
            if ($timeleft !== null) {
                $player->sendMessage("§cEste kit está em espera: " . $timeleft);
            }

            return;
        }

        $this->getPlugin()->getCooldownManager()->removeCooldown($player, $this);
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
        $player->sendMessage("§a> Kit {$this->name} recebido.");
        if (is_string($this->cooldown) && !$this->getPlugin()->getCooldownManager()->isExpired($player, $this)) {
            $this->getPlugin()->getCooldownManager()->addCooldown($player, $this, Cooldown::parseDuration($this->cooldown));
        }
    }

    public function onChangeArmor(EntityArmorChangeEvent $event): void
    {
        $player = $event->getEntity();
        $new = $event->getNewItem();
        if ($player instanceof Player) {
            if ($player->hasPermission($this->data['permission']) && (bool)strpos($new->getCustomName(), $this->name)) {
                foreach ($this->effects as $effect) {
                    $player->addEffect($effect);
                }
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

    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return $this->effects;
    }

    /**
     * @return int
     */
    public function getCooldown(): int
    {
        return $this->cooldown;
    }
}