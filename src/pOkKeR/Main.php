<?php

namespace pOkKeR;

use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginManager;

class Main extends PluginBase {

    protected function onEnable(): void {
        $this->getLogger()->info("ElevatorPlugin enabled!");
        $this->getServer()->getPluginManager()->registerEvents(new events\EventListener($this), $this);
    }

    protected function onDisable(): void {
        $this->getLogger()->info("ElevatorPlugin disabled!");
    }
}