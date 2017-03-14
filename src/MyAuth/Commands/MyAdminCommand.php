<?php

namespace MyAuth\Commands;

use MyAuth\MyAuth;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class MyAdminCommand implements CommandExecutor {

	public function __construct(MyAuth $plugin) {
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		if($sender->hasPermission('myauth') or $sender->hasPermission('myauth.myadmin') 
			or ($sender instanceof ConsoleCommandSender)) 
		{
			$database = $this->plugin->getDatabase();
			
			$subcommand = array_shift($args);
			switch(strtolower($subcommand)){
				case 'chp':
					if(!isset($args[0])) {
						$sender->sendMessage($this->lang->getMessage('myadmin_nonick'));
						return;
					}
					
					if(!isset($args[1])) {
						$sender->sendMessage($this->lang->getMessage('myadmin_nopass'));
						return;
					}
					
					$database->setPassword($args[0], $args[1]);
					$sender->sendMessage($this->lang->getMessage('myadmin_chp', ['{nickname}'], [$args[0]]));
				break;
				
				case 'info':
					if(!isset($args[0])) {
						$sender->sendMessage($this->lang->getMessage('myadmin_nonick'));
						return;
					}
					
					$data = $database->getPlayerData((string) $args[0]);
					
					if($data == null){
						$sender->sendMessage($this->lang->getMessage('myadmin_noplayer', ['{nickname}'], [$args[0]]));
						return;
					}
					
					$format = $this->plugin->config->get('time_format');
					$firstLogin = date($format, $data['firstlogin']);
					$lastLogin = date($format, $data['lastlogin']);
					$cid = $data['cid'];
					$ip = $data['ip'];
					
					$sender->sendMessage($this->lang->getMessage('myadmin_info', 
						['{nickname}', '{ip}', '{cid}', '{first_login}', '{last_login}'],
						[$args[0], $ip, $cid, $firstLogin, $lastLogin] ));
				break;
				
				default:
					$sender->sendMessage($this->lang->getMessage('myadmin_help'));
			}
			
			return;
		} else {
			$sender->sendMessage($this->lang->getMessage('no_permission'));
		}
	}
	
}