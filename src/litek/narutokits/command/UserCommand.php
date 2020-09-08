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
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $this->plugin->getFormManager()->sendKitPanel($sender);
            } else {
                switch ($args[0]) {
                    case 'effect':
                        if (isset($args[1]) && $args[1] === 'clear') {
                            $sender->removeAllEffects();
                            $sender->sendMessage("§a> Efeitos removidos.");
                        }
                        break;
                    default:
                        $sender->sendMessage("§c/nkit effect clear");
                }
            }
        }
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}