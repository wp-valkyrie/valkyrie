<?php

use Core\Module;
use Core\System;


System::addModule(new class('_CORE_FORM_', PHP_INT_MAX) extends Module{

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
        $handler->addFile(__DIR__ . '/../Core/Form/*.php');
        $handler->addFile(__DIR__ . '/../Core/Form/Structure/*.php');
        $handler->addFile(__DIR__ . '/../Core/Form/Grouping/*.php');
        $handler->addFile(__DIR__ . '/../Core/Form/Element/*.php');
        $handler->addFile(__DIR__ . '/../Core/Form/Element/Selectable/*.php');
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
     * @todo Enqueue form logic once it is done
     */
    public function adminEnqueue(): void{
        wp_enqueue_media();
        wp_enqueue_editor();

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('core-admin-form-logic', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-logic.js', ['jquery'], '1.0', true);
        wp_enqueue_script('core-admin-form-file', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-file.js', ['jquery'], '1.0', true);
        wp_enqueue_script('core-admin-form-handler', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-handler.js', ['jquery'], '1.0', true);

        wp_enqueue_style('core-admin-form-css', System::pathToUrl(System::$dir) . '/Modules/assets/css/form.css', [], '1.0', 'all');
    }
});