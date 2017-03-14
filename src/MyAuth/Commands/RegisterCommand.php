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
			return;
		} 
		
		$database = $this->plugin->getDatabase();
			
		$info = $database->getPlayerData($sender);
		
		if($info == null){
			$database->registerPlayer($sender, $args[0]);
			$this->plugin->authorize($sender);
			$sender->sendMessage($this->lang->getMessage('register_success'));
			return;
			
		} else {
			$sender->sendMessage($this->lang->getMessage('register_already'));
			return;
		}
	}
}