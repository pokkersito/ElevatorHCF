<?php

namespace pOkKeR;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase {

    protected function onEnable(): void {
        // Cargar o generar config.yml si no existe
        $this->saveDefaultConfig();
        
        // Registrar eventos
        $this->getLogger()->info("Plugin enabled!");
        $this->getServer()->getPluginManager()->registerEvents(new events\EventListener($this), $this);
    }

    protected function onDisable(): void {
        $this->getLogger()->info("Plugin disabled!");
    }
    
    public function getConfig(): \pocketmine\utils\Config {
        return parent::getConfig();
    }
}
