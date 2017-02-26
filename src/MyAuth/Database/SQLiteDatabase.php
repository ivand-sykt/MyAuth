<?php

namespace MyAuth\Database;

use MyAuth\MyAuth;
use MyAuth\Database\BaseDatabase;

use pocketmine\Player;

class SQLiteDatabase implements BaseDatabase {
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
		$this->data = $this->plugin->config;
		$this->db_init();
	}
	
	public function db_init(){
		$this->plugin->getLogger()->info($this->lang->getMessage('db_init', ['{type}'], ['SQLite']));
		$this->database = new \SQLite3($this->plugin->getDataFolder() . $this->data->get('database'). '.db');

		$this->database->query("
					CREATE TABLE IF NOT EXISTS `{$this->data->get('table_prefix')}pass` (
						`nickname` varchar(16) NOT NULL PRIMARY KEY,
						`firstlogin` bigint(20) NOT NULL,
						`lastlogin` bigint(20) NOT NULL,
						`password_hash` varchar(255) NOT NULL,
						`ip` varchar(16) NOT NULL,
						`cid` text NOT NULL
					);
		");
	}
	
	public function authorizePlayer(Player $player){		
		$nickname = strtolower($player->getName());
				
		(int) $loginTime = time();
		(string) $ip = $player->getAddress();
		(string) $cid = $player->getClientId();
		
		$this->database->query("
		UPDATE `{$this->data->get('table_prefix')}pass`
		SET ip='$ip', lastlogin=$loginTime, cid='$cid'
		WHERE nickname='$nickname';
		");
		
		return;
	}
	
	public function getPlayerData(Player $player){
		(string) $nickname = strtolower($player->getName());
		
		$data = $this->database->query("SELECT * FROM `{$this->data->get('table_prefix')}pass` WHERE nickname='$nickname'");
		return $data->fetchArray(SQLITE3_ASSOC);
	}
	
	public function getPlayerDataByName(string $nickname){
		(string) $nickname = strtolower($nickname);
		
		$data = $this->database->query("SELECT * FROM `{$this->data->get('table_prefix')}pass` WHERE nickname='$nickname'");
		return $data->fetchArray(SQLITE3_ASSOC);
	}
	
	public function setPassword(Player $player, $password){
		(string) $nickname = strtolower($player->getName());
		(string) $newpassword = password_hash($password, PASSWORD_DEFAULT);
		
		$this->database->query("
		UPDATE `{$this->data->get('table_prefix')}pass`
		SET password_hash='$newpassword' WHERE nickname='$nickname';
		");
		
		return;
	}
	
	public function setPasswordByName(string $nickname, $password){
		(string) $nickname = strtolower($nickname);
		(string) $newpassword = password_hash($password, PASSWORD_DEFAULT);
		
		$this->database->query("
		UPDATE `{$this->data->get('table_prefix')}pass`
		SET password_hash='$newpassword' WHERE nickname='$nickname';
		");
	
		return;
	}
	
	public function deletePlayer(Player $player){		
		(string) $nickname = strtolower($player->getName());
		$this->database->query("DELETE FROM `{$this->data->get('table_prefix')}pass` WHERE nickname='$nickname'");
		return;
	}
	
	public function registerPlayer(Player $player, $password){
		(string) $nickname = strtolower($player->getName());
		(int) $time = time();
		(string) $ip = $player->getAddress();
		(int) $cid = $player->getClientId();
		(string) $password = password_hash($password, PASSWORD_DEFAULT);
		
		$this->database->query(
				"INSERT INTO `{$this->data->get('table_prefix')}pass` 
				(nickname, firstlogin, lastlogin, password_hash, ip, cid) 
				VALUES 
				('$nickname', $time, $time, '$password', '$ip', '$cid');"
			);
		
		return;
	}
	
	public function close(){
		$this->database->close();
	}
}
