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
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		/* если авторизирован */
		if($this->plugin->isAuthorized($sender)){
			$sender->sendMessage($this->lang->getMessage('login_already'));
			return;
		} 
		
		$database = $this->plugin->getDatabase();
		
		$data = $database->getPlayerData($sender);
		
		if ($data == null) {
			/* data равно нулю, значит не зарегестрирован */
			$sender->sendMessage($this->lang->getMessage('login_noregister'));
			return;
		} 
		
		if(!isset($args[0])) {
			 $sender->sendMessage($this->lang->getMessage('login_nopass'));
			 return;
		}
			
		$password = $args[0];
			
		if(password_verify($password, $data['password_hash'])){
			/* если пароль подошёл */
			$this->plugin->authorize($sender);
			$sender->sendMessage($this->lang->getMessage('login_success'));
			return;
		} else {
			/* если пароль не подошёл */
			$sender->sendMessage($this->lang->getMessage('login_wrongpass'));
			return;
		}
	}
}
