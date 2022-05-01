<?php

namespace DXM_Hip\AfkKick\task;

use pocketmine\scheduler\Task;
use DXM_Hip\AfkKick\Main;

class afkKick extends Task {
    
    public function __construct(Main $plugin) {
         $this->plugin = $plugin;
    }
    
    public function onRun($currentTick) 
    {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
           if(!isset($this->plugin->time[$player->getName()]))
           {
           $this->plugin->setTime($player);    
           }
           
           if(!isset($this->plugin->pos[$player->getName()]))
           {
           $this->plugin->setPos($player);
           }
           
           if(!$this->plugin->hasMoved($player)){
           $this->plugin->removePlayer($player);
           
           }elseif(!$this->plugin->isPlayerSet($player))
           {
           $this->plugin->setPlayer($player);
           }
        }
        $this->plugin->checkTime();
        
    }
}
