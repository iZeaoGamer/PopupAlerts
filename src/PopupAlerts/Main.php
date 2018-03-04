<?php

/*
 * PopupAlerts (v1.3) by EvolSoft
 * Developer: EvolSoft (Flavius12)
 * Website: http://www.evolsoft.tk
 * Date: 14/07/2015 02:44 PM (UTC)
 * Copyright & License: (C) 2015 EvolSoft
 * Licensed under MIT (https://github.com/EvolSoft/PopupAlerts/blob/master/LICENSE)
 */

namespace PopupAlerts;

use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

//CustomAlerts API
use CustomAlerts\CustomAlerts;
use CustomAlerts\Events\CustomAlertsDeathEvent;
use CustomAlerts\Events\CustomAlertsJoinEvent;
use CustomAlerts\Events\CustomAlertsQuitEvent;
use CustomAlerts\Events\CustomAlertsWorldChangeEvent;

class Main extends PluginBase implements Listener{

	//About Plugin Const

	/** @var string PRODUCER Plugin producer */
	const PRODUCER = "EvolSoft";

	/** @var string VERSION Plugin version */
	const VERSION = "1.4.1-2";

	/** @var string MAIN_WEBSITE Plugin producer website */
	const MAIN_WEBSITE = "http://www.evolsoft.tk";

	//Other Const

	/** @var string PREFIX Plugin prefix */
	const PREFIX = "&1[&bPopup&aAlerts&1] ";

	/**
	 * Translate Minecraft colors
	 *
	 * @param string $symbol Color symbol
	 * @param string $message The message to be translated
	 *
	 * @return string The translated message
	 */
	public function translateColors($symbol, $message){

		$message = str_replace($symbol . "0", TextFormat::BLACK, $message);
		$message = str_replace($symbol . "1", TextFormat::DARK_BLUE, $message);
		$message = str_replace($symbol . "2", TextFormat::DARK_GREEN, $message);
		$message = str_replace($symbol . "3", TextFormat::DARK_AQUA, $message);
		$message = str_replace($symbol . "4", TextFormat::DARK_RED, $message);
		$message = str_replace($symbol . "5", TextFormat::DARK_PURPLE, $message);
		$message = str_replace($symbol . "6", TextFormat::GOLD, $message);
		$message = str_replace($symbol . "7", TextFormat::GRAY, $message);
		$message = str_replace($symbol . "8", TextFormat::DARK_GRAY, $message);
		$message = str_replace($symbol . "9", TextFormat::BLUE, $message);
		$message = str_replace($symbol . "a", TextFormat::GREEN, $message);
		$message = str_replace($symbol . "b", TextFormat::AQUA, $message);
		$message = str_replace($symbol . "c", TextFormat::RED, $message);
		$message = str_replace($symbol . "d", TextFormat::LIGHT_PURPLE, $message);
		$message = str_replace($symbol . "e", TextFormat::YELLOW, $message);
		$message = str_replace($symbol . "f", TextFormat::WHITE, $message);

		$message = str_replace($symbol . "k", TextFormat::OBFUSCATED, $message);
		$message = str_replace($symbol . "l", TextFormat::BOLD, $message);
		$message = str_replace($symbol . "m", TextFormat::STRIKETHROUGH, $message);
		$message = str_replace($symbol . "n", TextFormat::UNDERLINE, $message);
		$message = str_replace($symbol . "o", TextFormat::ITALIC, $message);
		$message = str_replace($symbol . "r", TextFormat::RESET, $message);

		return $message;
	}

	public function onEnable(){
		if($this->getServer()->getPluginManager()->getPlugin("CustomAlerts")){
			if(CustomAlerts::getAPI()->getAPIVersion() == "3.0.0-ALPHA10"){
				@mkdir($this->getDataFolder());
				$this->saveDefaultConfig();
				$this->getServer()->getPluginManager()->registerEvents($this, $this);
				$this->getLogger()->info($this->translateColors("&", Main::PREFIX . "&ePopupAlerts &9v" . Main::VERSION . " &adeveloped by&9 " . Main::PRODUCER));
				$this->getLogger()->info($this->translateColors("&", Main::PREFIX . "&eWebsite &9" . Main::MAIN_WEBSITE));
			}else{
				$this->getLogger()->error($this->translateColors("&", Main::PREFIX . "&cPlease update CustomAlerts to API 3.0.0-ALPHA10 Plugin disabled"));
				$this->getServer()->getPluginManager()->disablePlugin($this);
			}
		}else{
			$this->getLogger()->error($this->translateColors("&", Main::PREFIX . "&cYou need to install CustomAlerts (API 1.2). Plugin disabled"));
		}
	}

	public function onCAJoin(CustomAlertsJoinEvent $event){
		$cfg = $this->getConfig()->getAll();
		$player = $event->getPlayer();
		if($cfg["Join"]["show-popup"] == true){
			$msg = CustomAlerts::getAPI()->getJoinMessage();
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $msg, $cfg["Join"]["duration"]), 10);
			if($cfg["Join"]["hide-default"] == true){
				CustomAlerts::getAPI()->setJoinMessage("");
			}
		}
	}

	public function onCAQuit(CustomAlertsQuitEvent $event){
		$cfg = $this->getConfig()->getAll();
		$player = $event->getPlayer();
		if($cfg["Quit"]["show-popup"] == true){
			$msg = CustomAlerts::getAPI()->getQuitMessage();
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $msg, $cfg["Quit"]["duration"]), 10);
			if($cfg["Quit"]["hide-default"] == true){
				CustomAlerts::getAPI()->setQuitMessage("");
			}
		}
	}

	public function onCAWorldChange(CustomAlertsWorldChangeEvent $event){
		if(CustomAlerts::getAPI()->isDefaultWorldChangeMessageEnabled()){
			$cfg = $this->getConfig()->getAll();
			$player = $event->getPlayer();
			if($cfg["WorldChange"]["show-popup"] == true){
				$msg = CustomAlerts::getAPI()->getWorldChangeMessage();
				$this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $msg, $cfg["WorldChange"]["duration"]), 10);
				if($cfg["WorldChange"]["hide-default"] == true){
					CustomAlerts::getAPI()->setWorldChangeMessage("");
				}
			}
		}
	}

	public function onCADeath(CustomAlertsDeathEvent $event){
		$cfg = $this->getConfig()->getAll();
		$player = $event->getPlayer();
		if($cfg["Death"]["show-popup"] == true){
			$msg = CustomAlerts::getAPI()->getDeathMessage();
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new MessageTask($this, $msg, $cfg["Death"]["duration"]), 10);
			if($cfg["Death"]["hide-default"] == true){
				CustomAlerts::getAPI()->setDeathMessage("");
			}
		}
	}
}
