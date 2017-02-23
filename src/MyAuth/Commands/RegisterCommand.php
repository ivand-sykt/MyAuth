<?php
namespace MyAuth\Commands;

use MyAuth\MyAuth;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class RegisterCommand implements CommandExecutor {
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		
		if(!isset($args[0])){
			$sender->sendMessage(($this->lang->getMessage('register_nopass')));
			return false;
		} 
		
		$db = $this->plugin->getDB();
		(string) $nickname = strtolower($sender->getName());
			
		$info = $db->query("SELECT * FROM `{$this->plugin->config->get('table_prefix')}pass` WHERE nickname='$nickname'");
		
		if($info->num_rows == 0){
			(int) $time = time();
			(string) $ip = $sender->getAddress();
			(int) $cid = $sender->getClientId();
			
			(string) $password = password_hash($args[0], PASSWORD_DEFAULT);
			
			$db->query(
				"INSERT INTO `{$this->plugin->config->get('table_prefix')}pass` 
				(nickname, firstlogin, lastlogin, password_hash, ip, cid) 
				VALUES 
				('$nickname', $time, $time, '$password', '$ip', '$cid');"
			);
			$this->plugin->authorize($sender);
			$sender->sendMessage($this->lang->getMessage('register_success'));
			return true;
			
		} else {
			$sender->sendMessage($this->lang->getMessage('register_already'));
			return false;
		}
	}
}