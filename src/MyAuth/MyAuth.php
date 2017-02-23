<?php

namespace MyAuth;

use MyAuth\EventListener;
use MyAuth\Language;

use pocketmine\plugin\PluginBase;

use pocketmine\command\CommandExecutor;

use pocketmine\Player;

use pocketmine\utils\Config;
//use pocketmine\utils\MainLogger;

/* AUTH by SuperPuperSteve
** Changelog:
** v0.1 - First release
** v0.1.1 - changing language from Russian to English (not for comments)
** v0.2 multilanguage 
** v0.2.1 - changing namespace Auth ---> MyAuth (equals to plugin name), repo name as well

** v0.3 TODOs - chpwd, unregister

** v0.4 TODOs - improve code, more data storing, poggit description, (?) caching, class for db, encryption method

** v1.1 TODOs - info about player, console commands

** v1.2 TODOs - more configs, count failed auths

** v1.3 TODOs - SimpleAuth to MyAuth?

** v2.0 TODOs - ?, something crazy
*/

class MyAuth extends PluginBase {
		
	public $db;
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
		
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		
		$this->getCommand("register")->setExecutor(new Commands\RegisterCommand($this));
		$this->getCommand("login")->setExecutor(new Commands\LoginCommand($this));
		
		
		$this->db = @new \mysqli($this->config->get('ip'), $this->config->get('username'), $this->config->get('password'));
		
		if($this->db->connect_errno) {
			$this->getLogger()->info($this->lang->getMessage('mysql_conn_error', ['{mysql_error}'], [$this->db->connect_error]));
		} else {
			$this->db_init($this->db);
			$this->getLogger()->info($this->lang->getMessage('mysql_success'));
		}
		
	}
		
	public function onDisable(){
		$this->getLogger()->info($this->lang->getMessage('mysql_disconnect'));
		@$this->db->close();
	}
	
	private function db_init($conn){
		$this->getLogger()->info($this->lang->getMessage('mysql_init'));
		$info = $conn->query("CREATE DATABASE IF NOT EXISTS {$this->config->get('database')} ");
		$conn->select_db($this->config->get('database'));
		$conn->query("
					CREATE TABLE IF NOT EXISTS `{$this->config->get('table_prefix')}pass` (
						`nickname` varchar(16) NOT NULL,
						`firstlogin` bigint(20) NOT NULL,
						`lastlogin` bigint(20) NOT NULL,
						`password_hash` text NOT NULL,
						`ip` varchar(16) NOT NULL,
						`cid` text NOT NULL,
						PRIMARY KEY (`nickname`)
					);
		");
	}
	
	public function getDB(){
		return $this->db;
	}
	
	public function getLanguage(){
		return $this->lang;
	}
	
	public function authorize(Player $player){
		$nick = strtolower($player->getName());
		
		$this->authorized[$nick] = true;
		$time = time();
		$ip = $player->getAddress();
		$cid = $player->getClientId();
		
		$this->db->query(
			"UPDATE `{$this->config->get('table_prefix')}pass`
			SET lastlogin=$time, ip='$ip', cid='$cid'
			WHERE nickname='$nick'"
			);
	}
	
	public function deauthorize(Player $player){
		unset($this->authorized[strtolower($player->getName())]);
	}
	
	public function isAuthorized(Player $player){
		return isset($this->authorized[strtolower($player->getName())]);
	}
}