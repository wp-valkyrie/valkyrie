<?php

use Core\Module;
use Core\System;

System::addModule(new class('_CORE_ADMIN_', PHP_INT_MIN) extends Module{

    /**
     * Initializes the Module
     */
    public function init(): void{
        $this->requireGroup();
    }

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @param \Core\RequireHandler $handler A fresh RequireHandler to add files to
     * @return \Core\RequireHandler The combined RequireHandler
     */
    public function require(\Core\RequireHandler $handler): \Core\RequireHandler{
        $handler->addFile(__DIR__ . '/../Core/Admin/*.php');
        return $handler;
    }

    /**
     * Includes the Module-Assets on the enqueue script-hook
     */
    public function enqueue(): void{
        // No Frontend Enqueues
    }

    /**
     * Includes the Module-Assets on the admin enqueue script-hook
     */
    public function adminEnqueue(): void{
        // No Backend Enqueues
    }
});