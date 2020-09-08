<?php /** @noinspection MkdirRaceConditionInspection */

namespace litek\narutokits;

use litek\narutokits\command\UserCommand;
use litek\narutokits\cooldown\CooldownManager;
use litek\narutokits\form\FormManager;
use litek\narutokits\kit\KitManager;
use pocketmine\plugin\PluginBase;

class NarutoKits extends PluginBase
{
    /** @var NarutoKits */
    private static $instance;
    /** @var FormManager */
    private $formManager;
    /** @var KitManager */
    private $kitManager;
    /** @var CooldownManager */
    private $cooldownManager;

    public function onEnable(): void
    {
        self::$instance = $this;
        @mkdir($this->getDataFolder() . 'kits');
        $this->saveDefaultKits();
        $this->getServer()->getCommandMap()->register('nkit', new UserCommand($this));
        $this->initManagers();
    }

    public function onDisable(): void
    {
        $this->cooldownManager->saveAll();
    }

    private function initManagers(): void
    {
        $this->formManager = new FormManager($this);
        $this->kitManager = new KitManager($this);
        $this->cooldownManager = new CooldownManager($this);
    }

    private function saveDefaultKits(): void
    {
        $kits = ['sabio6', 'rinnegan', 'rinnesharingan'];
        foreach ($kits as $kit) {
            $this->saveResource("kits/$kit.yml");
        }
        $this->saveResource('cooldown.yml');
    }

    /**
     * @return NarutoKits
     */
    public static function getInstance(): NarutoKits
    {
        return self::$instance;
    }

    /**
     * @return FormManager
     */
    public function getFormManager(): FormManager
    {
        return $this->formManager;
    }

    /**
     * @return KitManager
     */
    public function getKitManager(): KitManager
    {
        return $this->kitManager;
    }

    /**
     * @return CooldownManager
     */
    public function getCooldownManager(): CooldownManager
    {
        return $this->cooldownManager;
    }

}