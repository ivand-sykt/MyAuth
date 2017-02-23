<?php

namespace MyAuth;

use MyAuth\EventListener;
use MyAuth\Language;

use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandExecutor;

use pocketmine\Player;

use pocketmine\utils\Config;

/*
 - [ ] caching
 - [ ] class for db
 - [ ] more configs 
 - [ ] enable/disable autoauth
 - [ ] count failed auths
 - [ ] info about player
 - [ ] console commands
 - [ ] TRY TO GET APPROVED BY poggit-ci
 */

class MyAuth extends PluginBase {
		
	public $database;
	public $lang;
	
	public $authorized = array();
	
	public function onEnable(){
		$this->getLogger()->info('§ePlugin initialization...');
		
		@mkdir($this->getDataFolder());
		
		if(!is_file($this->getDataFolder(). 'config.yml')){
			$this->saveResource('config.yml'); 
		}
		
		$this->config = new Config($this->getDataFolder(). 'config.yml', Config::YAML);
		
		$this->lang = new Language($this);
		$this->lang->lang_init($this->config->get('language'));
		
		switch(strtolower($this->config->get('type'))){
			case 'mysql':
			case 'mysqli':
				$this->database = new Database\MySQLDatabase($this, 
				['ip' => $this->config->get('ip'), 
				'username' => $this->config->get('username'), 
				'password' => $this->config->get('password')
				]);
				break;
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->getCommand("register")->setExecutor(new Commands\RegisterCommand($this));
		$this->getCommand("login")->setExecutor(new Commands\LoginCommand($this));
		$this->getCommand("unregister")->setExecutor(new Commands\UnregisterCommand($this));
		$this->getCommand("changepassword")->setExecutor(new Commands\ChangePasswordCommand($this));
		
	}
	
	
	public function onDisable(){
		$this->database->close();
	}
	
	public function getLanguage(){
		return $this->lang;
	}
	
	public function getDatabase(){
		return $this->database;
	}
	
	public function isAuthorized(Player $player){
		return isset($this->authorized[strtolower($player->getName())]);
	}
	
	public function authorize(Player $player){
		$nick = strtolower($player->getName());
		
		$this->authorized[$nick] = true;
		
		(int) $time = time();
		(string) $ip = $player->getAddress();
		(string) $cid = $player->getClientId();
		
		$this->database->authorizePlayer($player, $ip, $time, $cid);
	}
	
	public function deauthorize(Player $player){
		unset($this->authorized[strtolower($player->getName())]);
	}
}
