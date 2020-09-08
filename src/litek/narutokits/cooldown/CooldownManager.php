<?php

namespace litek\narutokits\cooldown;

use litek\narutokits\kit\Kit;
use litek\narutokits\NarutoKits;
use pocketmine\Player;
use pocketmine\utils\Config;

class CooldownManager
{
    /** @var array */
    public $cooldown = [];
    /** @var NarutoKits */
    private $plugin;
    /** @var Config */
    private $config;

    /**
     * CooldownManager constructor.
     * @param NarutoKits $plugin
     */
    public function __construct(NarutoKits $plugin)
    {
        $this->plugin = $plugin;
        $this->config = new Config($plugin->getDataFolder() . 'cooldown.yml');
        $this->loadAll();
    }

    /**
     * @param Player $player
     * @param Kit $kit
     * @param int $cooldown
     */
    public function addCooldown(Player $player, Kit $kit, int $cooldown): void
    {
        $this->cooldown[$player->getName()][$kit->getName()] = $cooldown;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasCooldown(Player $player): bool
    {
        return isset($this->cooldown[$player->getName()]);
    }

    /**
     * @param Player $player
     * @param Kit $kit
     */
    public function removeCooldown(Player $player, Kit $kit): void
    {
        if ($this->hasCooldown($player) && isset($this->cooldown[$player->getName()][$kit->getName()])) {
            unset($this->cooldown[$player->getName()][$kit->getName()]);
        }
    }

    /**
     * @param Player $player
     * @param Kit $kit
     * @return mixed|null
     */
    public function getKitCooldown(Player $player, Kit $kit)
    {
        return $this->cooldown[$player->getName()][$kit->getName()] ?? null;
    }

    /**
     * @param Player $player
     * @return mixed|null
     */
    public function getCooldown(Player $player)
    {
        return $this->cooldown[$player->getName()] ?? null;
    }

    /**
     * @param Player $player
     * @param Kit $kit
     * @return false|string|null
     */
    public function getTimeLeft(Player $player, Kit $kit)
    {
        if (($cooldown = $this->getKitCooldown($player, $kit)) !== null) {
            return date("H:i:s", $cooldown  - time());
        }
        return null;
    }

    /**
     * @param Player $player
     * @param Kit $kit
     * @return bool
     */
    public function isExpired(Player $player, Kit $kit): bool
    {
        if (($cooldown = $this->getKitCooldown($player, $kit)) !== null) {
            return ($cooldown > time());
        }
        return false;
    }

    public function loadAll(): void
    {
        $this->cooldown = $this->getConfig()->getAll();
    }

    public function saveAll(): void
    {
        $this->getConfig()->setAll($this->cooldown);
        $this->getConfig()->save();
    }

    /**
     * @return NarutoKits
     */
    public function getPlugin(): NarutoKits
    {
        return $this->plugin;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}