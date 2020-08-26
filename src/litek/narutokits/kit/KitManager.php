<?php


namespace litek\narutokits\kit;


use litek\narutokits\NarutoKits;
use pocketmine\utils\Config;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class KitManager
{
    /** @var NarutoKits */
    private $plugin;

    /** @var Kit[] */
    private $kits = [];

    /**
     * YamlProvider constructor.
     * @param NarutoKits $plugin
     */
    public function __construct(NarutoKits $plugin)
    {
        $this->plugin = $plugin;
        $this->loadKits();
    }

    public function loadKits(): void
    {
        $kitPath = $this->getPlugin()->getDataFolder() . 'kits';
        $kitDir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($kitPath)), RecursiveIteratorIterator::LEAVES_ONLY);

        /** @var SplFileInfo $kit */
        foreach ($kitDir as $kit) {
            if ($kit->isFile()) {
                $kitConfig = new Config($kit->getPath() . DIRECTORY_SEPARATOR . $kit->getBasename(), Config::YAML);
                $this->kits[$kit->getBasename('.yml')] = new Kit($kit->getBasename('.yml'), $kitConfig->getAll());
            }
        }
    }

    /**
     * @return NarutoKits
     */
    public function getPlugin(): NarutoKits
    {
        return $this->plugin;
    }
}