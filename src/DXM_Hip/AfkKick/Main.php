<?php

namespace DXM_Hip\AfkKick;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\scheduler\Task;
use pocketmine\Player;

use DXM_Hip\AfkKick\task\afkTimer;

class Main extends PluginBase implements Listener {
    
    public $time;
    public $players;
    public $pos;

    public function onEnable()
    {
      $this->checkConfig();
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      $this->startAfkTimer();
    }
   /**
    * 
    * @param Player $player
    */
    public function RemovePlayer(Player $player)
    {
      unset($this->players[$player->getName()]);
      unset($this->time[$player->getName()]);
      unset($this->pos[$player->getName()]);
    }
   /**
    * 
    * @param Player $player
    */
    public function setTime(Player $player)
    {
      $this->time[$player->getName()] = time();    
    }
   /**
    * 
    * @param Player $player
    */
    public function setPlayer(Player $player)
    {
      $this->players[$player->getName()] = $player;
    }
   /**
    * 
    * @param PlayerQuitEvent $ev
    */
    public function onQuit(PlayerQuitEvent $ev)
    {
      $this->RemovePlayer($ev->getPlayer());
    }
   /**
    * 
    * @param PlayerKickEvent $ev
    */
    public function onKick(PlayerKickEvent $ev)
    {
      $this->RemovePlayer($ev->getPlayer());
    }
   /**
    * 
    * @param Player $player
    * @return boolean
    */
    public function hasMoved(Player $player)
    {
      if($this->pos[$player->getName()] != $this->getPos($player))
      {
      $o = false;
      }else{
      $o = true;
      }
      
    return $o;
    }
   /**
    * 
    * @param Player $player
    * @return boolean
    */
    public function isPlayerSet(Player $player)
    {
      return isset($this->players[$player->getName()]);
    }
   /**
    * 
    * @param Player $player
    * @return boolean
    */
    public function getPos(Player $player)
    {
      return [round($player->x),round($player->y),round($player->z),$player->getLevel()];
    }
   /**
    * 
    * @param Player $player
    */
    public function setPos(Player $player)
    {
      $this->pos[$player->getName()] = [round($player->x),round($player->y),round($player->z),$player->getLevel()];
    }
    
    public function startAfkTimer()
    {
        if(!is_numeric($this->getScanInterval()) or !in_array($this->getScanInterval(), range(1, 30)))
        {
            $time = 5;
        }else{
            $time = $this->getScanInterval();
        }
        $this->getScheduler()->scheduleRepeatingTask(new afkTimer($this), $time);
        
    }
    
    public function checkConfig()
    {
        if(!file_exists($this->getDataFolder() . "config.yml"))
        {
            @mkdir($this->getDataFolder());
            file_put_contents($this->getDataFolder() . "config.yml",$this->getResource("config.yml"));
            return;
        }
        if($this->getConfig()->get("Version") == null || $this->getConfig()->get("Version") != "1.1")
        {
            unlink($this->getDataFolder() . "config.yml");
            file_put_contents($this->getDataFolder() . "config.yml",$this->getResource("config.yml"));
        }
    }
    
    public function checkTime()
    {
        if($this->players != NULL){
        
            foreach($this->players as $player){
               
                if(isset($this->time[$player->getName()]))
                {
                   $time = $this->time[$player->getName()];
                   if(time() - $time >= ($this->getKickTime() * 60) and !$player->hasPermission("afk.bypass"))
                   {
                   $player->kick($this->getKickMsg());
                   $this->RemovePlayer($player);
                   }
                }
                
            }
        }
    }
   /**
    * 
    * @return int
    */
    public function getScanInterval()
    {
      return $this->getConfig()->get("Scan-Interval");
    }
   /**
    * 
    * @return string
    */
    public function getKickMsg()
    {
      return $this->getConfig()->get("Kick-Message");
    }
   /**
    * 
    * @return int
    */
    public function getKickTime()
    {
      return $this->getConfig()->get("Time");
    }
}
