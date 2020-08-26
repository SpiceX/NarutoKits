<?php


namespace litek\narutokits\command;


use litek\narutokits\NarutoKits;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class UserCommand extends Command implements PluginIdentifiableCommand
{
    /**
     * @var NarutoKits
     */
    private $plugin;

    public function __construct(NarutoKits $plugin)
    {
        parent::__construct("nkit", "nkit command help", "", ['nkit', 'nkits']);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            $this->plugin->getFormManager()->sendKitPanel($sender);
        }
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}