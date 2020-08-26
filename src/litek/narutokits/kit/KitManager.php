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
                $kitName = $kit->getBasename('.yml');
                $kitConfig = new Config($kit->getPath() . DIRECTORY_SEPARATOR . $kit->getBasename(), Config::YAML);
                $this->kits[$kitName] = new Kit($kitName, $kitConfig->get($kitName));
            }
        }
    }

    /**
     * @param string $name
     * @return Kit|null
     */
    public function getKit(string $name): ?Kit
    {
        return $this->kits[$name] ?? null;
    }

    /**
     * @return NarutoKits
     */
    public function getPlugin(): NarutoKits
    {
        return $this->plugin;
    }

    /**
     * @return Kit[]
     */
    public function getKits(): array
    {
        return $this->kits;
    }
}