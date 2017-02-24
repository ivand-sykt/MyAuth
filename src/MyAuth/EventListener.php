<?php

namespace MyAuth;

use MyAuth\MyAuth;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerItemDropEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;

class EventListener implements Listener {
	
	public function __construct(MyAuth $plugin){
		$this->plugin = $plugin;
		$this->lang = $this->plugin->getLanguage();
	}
	
	public function onPlayerLogin(PlayerJoinEvent $event){
		$database = $this->plugin->getDatabase();
		$player = $event->getPlayer();
		
		$data = $database->getPlayerData($player);
		
		/* если не зарегестрирован */
		if($data == null){
			$player->sendMessage($this->lang->getMessage('register'));
			return false;
		} 

		/* в противном случае пытаемся авторизировать автоматически */
		if(
		($this->plugin->config->get('enable_authlogin')) &&
		($data['ip'] == $player->getAddress()) &&
		($data['cid'] == $player->getClientId())
		){
			$this->plugin->authorize($player);
			$player->sendMessage($this->lang->getMessage('login_auto'));
			return true;
		}
		
		$player->sendMessage($this->lang->getMessage('login'));
		return true;
	} /* конец */
	
	public function onQuit(PlayerQuitEvent $event){
		$this->plugin->deauthorize($event->getPlayer());
	}
	
	public function onChat(PlayerCommandPreprocessEvent $event){
		/* если игрок не авторизован */
		if(!($this->plugin->isAuthorized($event->getPlayer()))){
			$command = explode(' ', $event->getMessage());
			$allowed = ['/login', '/l', '/register', '/reg'];
			
			if(!in_array($command[0], $allowed)) $event->setCancelled();
		}
	}
	
	public function onBreak(BlockBreakEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_blocks'))) $event->setCancelled();
	}
	
	public function onPlace(BlockPlaceEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_blocks'))) $event->setCancelled();
	}
	
	public function onDrop(PlayerDropItemEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_drop'))) $event->setCancelled();
	}

	public function onInteract(PlayerInteractEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_interacting'))) $event->setCancelled();
	}
	
	public function onBucketEmpty(PlayerBucketEmptyEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_buckets'))) $event->setCancelled();
	}

	public function onBucketFill(PlayerBucketFillEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_buckets'))) $event->setCancelled();
	}

	public function onMove(PlayerMoveEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_moving'))) $event->setCancelled();
	}
	
	public function onConsume(PlayerItemConsumeEvent $event){
		if((!$this->plugin->isAuthorized($event->getPlayer())) and ($this->plugin->config->get('cancel_eating'))) $event->setCancelled();
	}
	
}