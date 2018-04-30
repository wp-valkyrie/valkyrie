<?php

namespace Core\Admin;

/**
 * Checks if Plugins are available in Wordpress
 * @package Core\Admin
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
     */
    public function addPlugin(string $name, string $dir, string $version = '0'): void{
        array_push($this->dependencies, [
            'name' => $name,
            'dir' => $dir,
            'version' => $version
        ]);
    }

    /**
     * Checks all Plugins and writes an Admin-Notice
     * if the Plugin is inactive, missing or to old
     */
    public function checkPlugins(): void{
        $missings = $this->getMissingPlugins();
        if (count($missings) > 0){
            foreach ($missings as $missing){
                $string = '<p>';
                $string .= 'Plugin named: ' . $missing['plugin']['name'] . ' ';
                if ($missing['error'] === self::ERROR_MISSING){
                    $string .= 'needs to be installed.';
                    $type = Notice::ERROR;
                }
                elseif($missing['error'] === self::ERROR_VERSION){
                    $string .= 'needs to get updated to at least ' . $missing['plugin']['version'] . '.';
                    $type = Notice::WARNING;
                }
                else{
                    $string .= 'is installed but needs to be activated.';
                    $type = Notice::ERROR;
                }
                $string .= '</p>';
                \Core::addNotice(new Notice($string, $type));
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
        $activePlugins = get_option( 'active_plugins' );
        $inactive = [];
        foreach ($this->dependencies as $dependency){
            $passed = false;
            foreach ($plugins as $plugin){
                if ($plugin['Name'] === $dependency['name']){
                    $passed = true;
                    if (version_compare($plugin['Version'], $dependency['version']) === -1){
                        array_push($inactive, [
                            'plugin' => $dependency,
                            'error' => self::ERROR_VERSION
                        ]);
                    }
                    elseif(!in_array($dependency['dir'],$activePlugins)){
                        array_push($inactive, [
                            'plugin' => $dependency,
                            'error' => self::ERROR_INACTIVE
                        ]);
                    }
                }
            }
            if (!$passed){
                array_push($inactive, [
                    'plugin' => $dependency,
                    'error' => self::ERROR_MISSING
                ]);
            }
        }
        return $inactive;
    }
}