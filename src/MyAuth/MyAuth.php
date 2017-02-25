<?php

namespace MyAuth;

use MyAuth\EventListener;
use MyAuth\Language;

use pocketmine\entity\Effect;

use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandExecutor;

use pocketmine\Player;

use pocketmine\utils\Config;

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
				$this->database = new Database\MySQLDatabase($this);
				break;
				
			case 'yaml':
			case 'yml':
				$this->database = new Database\YAMLDatabase($this);
				break;
			
			case 'json':
				$this->database = new Database\JSONDatabase($this);
				break;
				
			default:
				$this->database = new Database\YAMLDatabase($this);
		}
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->getCommand("register")->setExecutor(new Commands\RegisterCommand($this));
		$this->getCommand("login")->setExecutor(new Commands\LoginCommand($this));
		$this->getCommand("unregister")->setExecutor(new Commands\UnregisterCommand($this));
		$this->getCommand("changepassword")->setExecutor(new Commands\ChangePasswordCommand($this));
		$this->getCommand("myadmin")->setExecutor(new Commands\MyAdminCommand($this));
		$this->getCommand("logout")->setExecutor(new Commands\LogoutCommand($this));
		
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
		$this->setVisible($player);
		
		$this->authorized[$nick] = true;

		$this->database->authorizePlayer($player);
		return;
	}
	
	public function deauthorize(Player $player){
		$this->setInvisible($player);
		unset($this->authorized[strtolower($player->getName())]);
		return;
	}
	
	public function setInvisible(Player $player){
		if($this->config->get('hide_players')) {
			$effect = Effect::getEffect(Effect::INVISIBILITY);
			$effect->setDuration(PHP_INT_MAX);
			$effect->setVisible(false);
			$effect->setAmplifier(1);

			$player->addEffect($effect);
		}
	}
	
	public function setVisible($player) {
		if($this->config->get('hide_players')) {
			$player->removeEffect(Effect::INVISIBILITY);
		}
	}
	
}
