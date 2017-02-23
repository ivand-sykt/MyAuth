<?php
namespace MyAuth\Commands;

use MyAuth\MyAuth;

use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class LoginCommand implements CommandExecutor {
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onCommand(CommandSender $sender, Command $command, $label, array $args){
		/* если авторизирован */
		if($this->plugin->isAuthorized($sender)){
			$sender->sendMessage($this->lang->getMessage('login_already'));
			return false;
		} 
		
		$db = $this->plugin->getDB();
		(string) $nickname = strtolower($sender->getName());
		
		$info = $db->query("SELECT * FROM `{$this->plugin->config->get('table_prefix')}pass` WHERE nickname='$nickname'");
		$data = $info->fetch_assoc();
		
		if ($data == null) {
			/* data равно нулю, значит не зарегестрирован */
			$sender->sendMessage($this->lang->getMessage('login_noregister'));
			return false;
		} 
		
		if(!isset($args[0])) {
			 $sender->sendMessage($this->lang->getMessage('login_nopass'));
			 return false;
		}
			
		$password = $args[0];
			
		if(password_verify($password, $data['password_hash'])){
			/* если пароль подошёл */
			$this->plugin->authorize($sender);
			$sender->sendMessage($this->lang->getMessage('login_success'));
		} else {
			/* если пароль не подошёл */
			$sender->sendMessage($this->lang->getMessage('login_wrongpass'));
		}
	}
}