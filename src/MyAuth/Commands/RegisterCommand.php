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
		
		$database = $this->plugin->getDatabase();
		(string) $nickname = strtolower($sender->getName());
			
		$info = $database->getPlayerData();
		
		if($info->num_rows == 0){
			$database->registerPlayer($sender, $args[0]);
			$this->plugin->authorize($sender);
			$sender->sendMessage($this->lang->getMessage('register_success'));
			return true;
			
		} else {
			$sender->sendMessage($this->lang->getMessage('register_already'));
			return false;
		}
	}
}