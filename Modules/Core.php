<?php

use Core\Admin\Notice;
use Core\Module;
use Core\System;

// List of all Core Modules
$dependencies = [
    '_CORE_ADMIN_',
    '_CORE_FORM_'
];

/**
 * This module wraps all core-modules and can serve as the
 * main dependency for all user-generated modules
 * which require the CORE API
 */
System::addModule(new class('_CORE_', PHP_INT_MIN, $dependencies) extends Module{

    /**
     * Initializes the Module
     */
    public function init(): void{
        if (apply_filters('wp-core_copyright', false)) {
            add_action('wp_footer', [$this, 'printCopyRightNotice']);
        }
        if (apply_filters('wp-core_copyright-admin', false)) {
            add_action('admin_footer', [$this, 'printCopyRightNotice']);
        }
        add_action('core_modules_not_loaded', [$this, 'displayMissingModules'], 10, 2);
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
     * Displays an admin notice informing the user about
     * modues, which could not been loaded because of missing
     * dependencies
     * @param Module[] $modules
     * @param string[] $activates
     */
    public function displayMissingModules(array $modules, array $activates): void{
        // Skip if all registered modules have been loaded
        if (empty($modules)) {
            return;
        }

        // Display one admin warning notice containg all missing modules and the specific missing dependencies
        $content = __('The Following WP-Core Module(s) could not be loaded because one or more dependency is missing: ') . '<br />';
        foreach ($modules as $module) {
            $deps = array_filter($module->getDependencies(), function (string $d) use ($activates): bool{
                return !in_array($d, $activates);
            });
            $content .= '<strong>' . $module->getName() . '</strong>' . ' ← ' . '<small>' . implode(' , ', $deps) . '</small><br />';
        }
        System::addNotice(new Notice($content, Notice::WARNING));
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