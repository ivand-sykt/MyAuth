<?php
namespace MyAuth\Database;

use pocketmine\Player;

interface BaseDatabase {
	public function db_init();
	
	public function authorizePlayer(Player $player);
	
	public function getPlayerData(Player $player);
	
	public function getPlayerDataByName(string $nickname);
	
	public function setPassword(Player $player, $password);
	
	public function setPasswordByName(string $nickname, $password);
	
	public function deletePlayer(Player $player);
	
	public function registerPlayer(Player $player, $password);
	
	public function close();
}