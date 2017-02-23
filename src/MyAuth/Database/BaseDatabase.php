<?php
namespace MyAuth\Database;

use pocketmine\Player;

interface BaseDatabase {
	public function db_init();
	
	public function authorizePlayer(Player $player, $ip, $loginTime, $cid);
	
	public function getPlayerData(Player $player);
	
	public function setPassword(Player $player, $password);
	
	public function deletePlayer(Player $player);
	
	public function registerPlayer(Player $player, $password);
	
	public function close();
}