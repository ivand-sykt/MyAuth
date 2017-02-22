<?php
namespace Auth\Commands;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class RegisterCommand implements CommandExecutor {
	public function __construct(\Auth\Auth $plugin){
		$this->plugin = $plugin;
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		
		if(isset($args[0])){
			$db = $this->plugin->getDB();
			(string) $nickname = strtolower($sender->getName());
			
			$info = $db->query("SELECT * FROM `{$this->plugin->config['table_prefix']}pass` WHERE nickname='$nickname'");
		
			if($info->num_rows == 0){
				(int) $time = time();
				(string) $ip = $sender->getAddress();
				(int) $cid = $sender->getClientId();
				
				(string) $password = md5($args[0]);
				
				$db->query(
					"INSERT INTO `{$this->plugin->config['table_prefix']}pass` 
					(nickname, firstlogin, lastlogin, password_hash, ip, cid) 
					VALUES 
					('$nickname', $time, $time, '$password', '$ip', '$cid');"
				);
				$this->plugin->authorize($sender);
				$sender->sendMessage('§aВы успешно зарегестрировались!');
				
			} else {
				$sender->sendMessage('§cВы уже зарегестрированы!');
			}
			
		} else {
			$sender->sendMessage('§cВы не указали пароль!');
		}	
	}
}