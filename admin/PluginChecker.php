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
    public function addPlugin(string $name, string $dir, string $version = '0', string $uri = ''): void{
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
        if (count($missings) > 0){
            foreach ($missings as $missing){
                $string = '<p>';
                if ($missing['error'] === self::ERROR_MISSING){
                    $string .= sprintf( __( '%s needs to be installed.' ), '<strong>' . $missing['plugin']['name'] . '</strong>' );
                    if ( ! empty( $missing['plugin']['uri'] ) ) {
                        $string .= sprintf( ' <a href="%s">%s</a>', esc_url( $missing['plugin']['uri'] ), __( 'Visit plugin site' ) );
                    }
                    $type = Notice::ERROR;
                }
                elseif($missing['error'] === self::ERROR_VERSION){
                    $string .= sprintf( __( '%s needs to get updated to at least %s.' ), '<strong>' . $missing['plugin']['name'] . '</strong>', $missing['plugin']['version'] );
                    $type = Notice::WARNING;
                }
                else{
                    $string .= sprintf( __( '%s is installed but needs to be activated.' ), '<strong>' . $missing['plugin']['name'] . '</strong>' );
                    if ( current_user_can( 'activate_plugins' ) ) {
                        $string .= sprintf( ' <a href="%s">%s</a>',
                            wp_nonce_url( network_admin_url( 'plugins.php?action=activate&plugin=' . $missing['plugin']['dir'] ), 'activate-plugin_'.$missing['plugin']['dir'] ),
                            __( 'Activate Plugin' )
                        );
                    }
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
