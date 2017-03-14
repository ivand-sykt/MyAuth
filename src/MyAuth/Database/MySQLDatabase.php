<?php
namespace MyAuth\Database;

use MyAuth\Database\BaseDatabase;
use MyAuth\MyAuth;

use pocketmine\Player;

class MySQLDatabase implements BaseDatabase {
	
	private $cache;
		
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
		$this->data = $this->plugin->config;
		$this->db_init();
	}
	
	public function db_init(){
		$this->plugin->getLogger()->info($this->lang->getMessage('db_init', ['{type}'], ['MySQL']));
		
		$this->database = @new \mysqli($this->data->get('ip'), $this->data->get('username'), $this->data->get('password'));
		
		if($this->database->connect_errno){
			$this->plugin->getLogger()->info($this->lang->getMessage('db_conn_error', ['{error}'], [$this->database->connect_error]));
			return;
		}
		
		$this->plugin->getLogger()->info($this->lang->getMessage('db_success'));
		
		$this->database->query("CREATE DATABASE IF NOT EXISTS {$this->data->get('database')}");
		$this->database->select_db($this->data->get('database'));
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
	
	public function getPlayerData($nickname){
		if($nickname  instanceof Player){
			$nickname = $nickname->getName();
		}
		
		(string) $nickname = strtolower($nickname);
		
		$data = $this->database->query("SELECT * FROM `{$this->data->get('table_prefix')}pass` WHERE nickname='$nickname'");
		return $data->fetch_assoc();
	}
	
	public function setPassword($nickname, $password){
		if($nickname instanceof Player){
			$nickname = $nickname->getName();
		}

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
		@$this->database->close();
		$this->plugin->getLogger()->info($this->lang->getMessage('db_disconnect'));
	}

}  