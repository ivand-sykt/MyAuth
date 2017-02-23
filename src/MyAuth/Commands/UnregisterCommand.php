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
		
		/* может использовать оператор '??' ?*/
		if (!isset($args[0])){
			$sender->sendMessage($this->lang->getMessage('unregister_nopass'));
			return false;
		}
		
		if (!isset($args[1])){
			$sender->sendMessage($this->lang->getMessage('unregister_nopass'));
			return false;
		}
		
		if($args[0] !== $args[1]){
			$sender->sendMessage($this->lang->getMessage('unregister_mismatch'));
			return false;
		}
	
		$database = $this->plugin->getDatabase();
		$data = $database->getPlayerData($sender);
		
		if(password_verify($args[0], $data['password_hash'])){
			$database->deletePlayer($sender);
			$this->plugin->deauthorize($sender);
			$sender->sendMessage($this->lang->getMessage('unregister_success'));
			return true;
		} else {
			$sender->sendMessage($this->lang->getMessage('unregister_wrong'));
			return false;
		}
	}
}
