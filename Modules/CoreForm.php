<?php

use Valkyrie\Module;
use Valkyrie\System;


System::addModule(new class('_CORE_FORM_', PHP_INT_MAX) extends Module{

    /**
     * Initializes the Module
     */
    public function init(): void{
        $this->requireGroup();
    }

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @param \Valkyrie\RequireHandler $handler A fresh RequireHandler to add files to
     * @return \Valkyrie\RequireHandler The combined RequireHandler
     */
    public function require(\Valkyrie\RequireHandler $handler): \Valkyrie\RequireHandler{
        $handler->addFile(__DIR__ . '/../Valkyrie/Form/*.php');
        $handler->addFile(__DIR__ . '/../Valkyrie/Form/Structure/*.php');
        $handler->addFile(__DIR__ . '/../Valkyrie/Form/Grouping/*.php');
        $handler->addFile(__DIR__ . '/../Valkyrie/Form/Element/*.php');
        $handler->addFile(__DIR__ . '/../Valkyrie/Form/Element/Selectable/*.php');
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
        wp_enqueue_script('wp-link');
        wp_enqueue_style('editor-buttons');

        wp_enqueue_script('jquery-ui-sortable');
//        wp_enqueue_script('core-admin-form-logic', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-logic.js', ['jquery'], '1.0', true);
        wp_enqueue_script('core-admin-form-file', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-file.js', ['jquery'], '1.0', true);
        wp_enqueue_script('core-admin-form-links', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-links.js', ['jquery'], '1.0', true);
        wp_enqueue_script('core-admin-form-handler', System::pathToUrl(System::$dir) . '/Modules/assets/js/form-handler.js', ['jquery'], '1.0', true);

        wp_enqueue_style('core-admin-form-css', System::pathToUrl(System::$dir) . '/Modules/assets/css/form.css', [], '1.0', 'all');
    }
});