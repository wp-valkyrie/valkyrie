<?php

use Core\Module;
use Core\System;

// List of all Core Modules
// This allows this module to be a main dependency for all user generated
// modules

$dependencies = [
    '_CORE_ADMIN_',
    '_CORE_FORM_'
];

System::addModule(new class('_CORE_', PHP_INT_MIN, $dependencies) extends Module{

    /**
     * Initializes the Module
     */
    public function init(): void{
        // This Modules does nothing other than load the copyright notice
        if (apply_filters('wp-core_copyright', true)){
            add_action('wp_footer', [$this, 'printCopyRightNotice']);
        }
        if (apply_filters('wp-core_copyright-admin', true)) {
            add_action('admin_footer', [$this, 'printCopyRightNotice']);
        }
    }

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @param \Core\RequireHandler $handler A fresh RequireHandler to add files to
     * @return \Core\RequireHandler The combined RequireHandler
     */
    public function require(\Core\RequireHandler $handler): \Core\RequireHandler{
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

    /**
     * Prints the CopyRight Notice as a comment in the front- and backend footer
     * This can be deactivated by adding a filter to 'wp-core_copyright'
     * and 'wp-core_copyright-admin' and return false
     * @see https://github.com/jschaefer-io/wp-core/blob/master/LICENSE
     */
    public function printCopyRightNotice(){
        $string = '<!-- ' . PHP_EOL;
        $string .= "\t" . 'This Page uses the WP-Core (https://github.com/jschaefer-io/wp-core)' . PHP_EOL;
        $string .= "\t" . 'Copyright ' . date('Y') . ' Jannik Schaefer' . PHP_EOL;
        $string .= ' -->' . PHP_EOL;
        echo PHP_EOL . $string . PHP_EOL;
    }
});