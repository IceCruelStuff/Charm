<?php
declare(strict_types = 1);

namespace JackMD\Charm\Command\Defaults;

use JackMD\Charm\Charm;
use JackMD\Charm\Command\BaseCommand;
use JackMD\Charm\Utils\PlayerUtils;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use function strtolower;

class Fly extends BaseCommand{

	/**
	 * Heal constructor.
	 *
	 * @param Charm $plugin
	 */
	public function __construct(Charm $plugin){
		parent::__construct(
			$plugin,

			"fly",
			"charm.command.fly.use",
			"Enable fly mode for yourself or another person.",
			"/fly [string:player]"
		);
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $label
	 * @param array         $args
	 */
	public function onCommand(CommandSender $sender, string $label, array $args): void{
		$target = $sender;

		if(isset($args[0])){
			$targetName = strtolower($args[0]);
			$target = $this->getServer()->getPlayer($targetName);

			if(!PlayerUtils::isOnline($target)){
				$this->sendError($sender, "Player §4$targetName §cis not online or doesn't exist.");

				return;
			}
		}

		if($target instanceof ConsoleCommandSender){
			$this->sendError($sender, "Please provide a player to heal.");
			$this->sendError($sender, "Usage: " . $this->getUsage());

			return;
		}

		if($target === $sender){
			if(!$this->hasPermission($sender, "charm.command.fly.self")){
				return;
			}
		}else{
			if(!$this->hasPermission($sender, "charm.command.fly.other")){
				return;
			}

			$this->sendMessage($sender, "Flying mode " . (!$target->getAllowFlight() ? "§2enabled" : "§cdisabled") . " §afor §2{$target->getName()}");
		}

		$this->sendMessage($target, "Flying mode " . (!$target->getAllowFlight() ? "§2enabled." : "§cdisabled."));
		$target->setAllowFlight(!$target->getAllowFlight());
		$target->setFlying(!$target->isFlying());
	}
}