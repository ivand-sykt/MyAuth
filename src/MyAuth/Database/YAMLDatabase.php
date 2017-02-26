<?php

namespace MyAuth\Database;

use MyAuth\Database\BaseDatabase;
use MyAuth\MyAuth;

use pocketmine\utils\Config;

use pocketmine\Player;

class YAMLDatabase implements BaseDatabase {
	
	private $path;
	
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
		$this->data = $this->plugin->config;
		$this->db_init();
	}
	
	public function db_init(){
		$this->plugin->getLogger()->info($this->lang->getMessage('db_init', ['{type}'], ['YAML']));
		$this->path = $this->plugin->getDataFolder() . $this->data->get('table_prefix') . $this->data->get('database') . DIRECTORY_SEPARATOR;
		
		@mkdir($this->path);
	}
	
	public function authorizePlayer(Player $player){
		$data = new Config($this->path . strtolower($player->getName()) . '.yml' , Config::YAML);
				
		$loginTime = time();
		$ip = $player->getAddress();
		$cid = $player->getClientId();
		
		$data->set('ip', $ip);
		$data->set('lastlogin', $loginTime);
		$data->set('cid', $cid);
		$data->save(true);
		
		unset($data);
		return;
	}
	
	public function getPlayerData(Player $player){
		$nickname = strtolower($player->getName());
		$profile = new Config($this->path . strtolower($nickname) . '.yml', Config::YAML);
		$data = $profile->getAll();
		
		unset($profile);
		return $data;
	}
	
	public function getPlayerDataByName(string $nickname){
		$nickname = strtolower($nickname);
		$profile = new Config($this->path . strtolower($nickname) . '.yml', Config::YAML);
		$data = $profile->getAll();
		
		unset($profile);
		return $data;
	}
	
	public function setPassword(Player $player, $password){
		$data = new Config($this->path . strtolower($player->getName()) . '.yml', Config::YAML);
		$password = password_hash($password, PASSWORD_DEFAULT);
		
		$data->set('password_hash', $password);
		$data->save(true);
		
		unset($data);
		return;
	}
	
	public function setPasswordByName(string $nickname, $password){
		$data = new Config($this->path . strtolower($nickname) . '.yml', Config::YAML);
		$password = password_hash($password, PASSWORD_DEFAULT);
		
		$data->set('password_hash', $password);
		$data->save(true);
		
		unset($data);
		return;
	}
	
	public function deletePlayer(Player $player){
		$nickname = strtolower($player->getName());
		
		unlink($this->path . "$nickname.yml");
		return;
	}
	
	public function registerPlayer(Player $player, $password){
		$nickname = strtolower($player->getName());
		$ip = $player->getAddress();
		$cid = $player->getClientId();
		$loginTime = time();
		$password = password_hash($password, PASSWORD_DEFAULT);
		
		$profile = new Config($this->path . strtolower($nickname) . '.yml', Config::YAML);

		$profile->set('nickname', $nickname); // for easy conerting from YAML to MySQL in future
		$profile->set('firstlogin', $loginTime);
		$profile->set('lastlogin', $loginTime);
		$profile->set('password_hash', $password);
		$profile->set('ip', $ip);
		$profile->set('cid', $cid);
		
		$profile->save(true);
		
		unset($profile);
		return;
	}
	
	public function close(){
		
	}
}