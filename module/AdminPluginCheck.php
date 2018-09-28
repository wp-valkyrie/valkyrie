<?php

namespace Core\Module;

use Core\Module;
use Core\RequireHandler;

class AdminPluginCheck extends Module{

    /**
     * Initializes the Theme Object
     */
    public function __construct(){
        parent::__construct('_CORE_PLUGIN_CHECKER', -1);
    }

    /**
     * Initializes the Module
     */
    public function init(): void{
        $this->requireGroup();
    }

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @param RequireHandler $handler A fresh RequireHandler to add files to
     * @return RequireHandler The combined RequireHandler
     */
    public function require(RequireHandler $handler): RequireHandler{
        $handler->addFile(\Core::$dir . '/admin/PluginChecker.php');
        return $handler;
    }

    /**
     * Includes the Module-Assets on the enqueue script-hook
     */
    public function enqueue(): void{
    }

    /**
     * Includes the Module-Assets on the adminenqueue script-hook
     */
    public function adminEnqueue(): void{
    }
}