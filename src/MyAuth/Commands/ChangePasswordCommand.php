<?php

namespace MyAuth\Commands;

use MyAuth\MyAuth;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class ChangePasswordCommand implements CommandExecutor {
	
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if(!isset($args[0])){
			$sender->sendMessage($this->lang->getMessage('passwd_nopass'));
			return false;
		}
		
		if(!isset($args[1])){
			$sender->sendMessage($this->lang->getMessage('passwd_nonewpass'));
			return false;
		}
		
		if(!isset($args[2])){
			$sender->sendMessage($this->lang->getMessage('passwd_noconfirm'));
			return false;
		}
		
		if($args[1] !== $args[2]){
			$sender->sendMessage($this->lang->getMessage('passwd_mismatch'));
			return false;
		}
		
		(string) $newpassword = password_hash($args[1], PASSWORD_DEFAULT);
		(string) $nickname = strtolower($sender->getName());
		$db = $this->plugin->getDB();
		$info = $db->query("UPDATE `{$this->plugin->config->get('table_prefix')}pass`
						SET password_hash='$newpassword' WHERE nickname='$nickname'
						");
						
		$sender->sendMessage($this->lang->getMessage('passwd_success', ['{new_password}'], [$args[1]]));
		$this->plugin->deauthorize($sender);
	}
}