<?php

namespace Valkyrie\Admin;

use Valkyrie\System;

/**
 * Checks if Plugins are available in Wordpress
 */
class PluginChecker{

    const ERROR_VERSION = 401;
    const ERROR_MISSING = 402;
    const ERROR_INACTIVE = 403;

    /**
     * List of Plugins
     * @var array
     */
    private $dependencies = [];

    /**
     * Adds a Plugin to the checker
     * @param string $name Plugin Name
     * @param string $dir Plugin-Directory
     * @param string $version Minimal Plugin-Version
     * @param string|null $uri Plugin-Site
     */
    public function addPlugin(string $name, string $dir, string $version = '0', string $uri = null): void{
        array_push($this->dependencies, [
            'name' => $name,
            'dir' => $dir,
            'version' => $version,
            'uri' => $uri
        ]);
    }

    /**
     * Checks all Plugins and writes an Admin-Notice
     * if the Plugin is inactive, missing or to old
     */
    public function checkPlugins(): void{
        $missings = $this->getMissingPlugins();
        if (count($missings) > 0) {
            foreach ($missings as $missing) {
                $pluginName = '<strong>' . $missing['plugin']['name'] . '</strong>';

                $stringInstall = sprintf(__('Plugin named: %s needs to be installed.'), $pluginName);
                $stringUpdate = sprintf(__('Plugin named: %s needs to get updated to at least %s.'), $pluginName, '<strong>' . $missing['plugin']['version'] . '</strong>');
                $stringInactive = sprintf(__('Plugin named: %s is installed but needs to be activated.'), $pluginName);

                $string = '<p>';
                if ($missing['error'] === self::ERROR_MISSING) {
                    $string .= $stringInstall;
                    $type = Notice::ERROR;
                    if (!is_null($missing['plugin']['uri'])) {
                        $string .= sprintf(' <a href="%s">%s</a>', esc_url($missing['plugin']['uri']), __('Visit plugin site'));
                    }
                } elseif ($missing['error'] === self::ERROR_VERSION) {
                    $string .= $stringUpdate;
                    $type = Notice::WARNING;
                } else {
                    $string .= $stringInactive;
                    $type = Notice::ERROR;
                    if (current_user_can('activate_plugins')) {
                        $string .= sprintf(' <a href="%s">%s</a>', wp_nonce_url(network_admin_url('plugins.php?action=activate&plugin=' . $missing['plugin']['dir']), 'activate-plugin_' . $missing['plugin']['dir']), __('Activate Plugin'));
                    }
                }
                $string .= '</p>';
                System::addNotice(new Notice($string, $type));
            }
        }
    }

    /**
     * Creates a List of Missing Plugins with
     * an error status code, which allows to pinpoint the problem
     * @return array
     */
    private function getMissingPlugins(): array{
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $plugins = get_plugins();
        $activePlugins = get_option('active_plugins');
        $inactive = [];
        foreach ($this->dependencies as $dependency) {
            $passed = false;
            foreach ($plugins as $plugin) {
                if ($plugin['Name'] === $dependency['name']) {
                    $passed = true;
                    if (version_compare($plugin['Version'], $dependency['version']) === -1) {
                        array_push($inactive, [
                            'plugin' => $dependency,
                            'error' => self::ERROR_VERSION
                        ]);
                    } elseif (!in_array($dependency['dir'], $activePlugins)) {
                        array_push($inactive, [
                            'plugin' => $dependency,
                            'error' => self::ERROR_INACTIVE
                        ]);
                    }
                }
            }
            if (!$passed) {
                array_push($inactive, [
                    'plugin' => $dependency,
                    'error' => self::ERROR_MISSING
                ]);
            }
        }
        return $inactive;
    }
}