<?php

namespace MyAuth\Commands;

use MyAuth\MyAuth;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use pocketmine\Player;

class LogoutCommand implements CommandExecutor {

	public function __construct(MyAuth $plugin) {
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($sender instanceof Player){
			$this->plugin->deauthorize($sender);
			$sender->sendMessage($this->lang->getMessage('logout_success'));
		} else {
			$sender->sendMessage($this->lang->getMessage('ingame_only'));
		}
	}
}
