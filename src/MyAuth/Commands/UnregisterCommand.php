<?php

namespace MyAuth\Commands;

use MyAuth\MyAuth;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class UnregisterCommand implements CommandExecutor {
	
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		/* игрок заведомо зарегестрированный => вошедший и под управлением */
		
		if (!isset($args[0])){
			$sender->sendMessage($this->lang->getMessage('unregister_nopass'));
			return;
		}
		
		if (!isset($args[1])){
			$sender->sendMessage($this->lang->getMessage('unregister_nopass'));
			return;
		}
		
		if($args[0] !== $args[1]){
			$sender->sendMessage($this->lang->getMessage('unregister_mismatch'));
			return;
		}
	
		$database = $this->plugin->getDatabase();
		$data = $database->getPlayerData($sender);
		
		if(password_verify($args[0], $data['password_hash'])){
			$database->deletePlayer($sender);
			$this->plugin->deauthorize($sender);
			$sender->sendMessage($this->lang->getMessage('unregister_success'));
			return;
		} else {
			$sender->sendMessage($this->lang->getMessage('unregister_wrong'));
			return;
		}
	}
}
