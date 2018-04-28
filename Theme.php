<?php
namespace Core;
use Core\Module;
use Core\RequireHandler;

/**
 * Main Theme Object
 * @package IMA
 */
class Theme extends Module {

    /**
     * Initializes the Theme Object
     */
    public function __construct(){
        parent::__construct('_THEME_', 0);
    }

    /**
     * Loads all dependency-files and starts the initialisation process
     */
    public function init(): void{
        $this->requireGroup('types');
        echo 'theme loaded';
    }

    /**
     * Prepare all Includes for the theme
     * @param RequireHandler $handler A fresh RequireHandler to add files to
     * @return RequireHandler All theme required-files
     */
    public function require(RequireHandler $handler): RequireHandler{
        $handler->addFile(get_stylesheet_directory() . '/types/[!_]*.php', 'types');
        return $handler;
    }

    /**
     * Enqueues the main Theme assets
     */
    public function enqueue(): void{
        wp_enqueue_script( 'app-js', get_stylesheet_directory_uri() . '/assets/dist/js/app.js', [], '1.0', true );
        wp_enqueue_style( 'app-css', get_stylesheet_directory_uri() . '/assets/dist/css/app.css', [], '1.0', 'all');
    }

    /**
     * Enqueues the main Theme admin assets
     */
    public function adminEnqueue(): void{
        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
        wp_enqueue_script( 'app-js', get_stylesheet_directory_uri() . '/wp-core/admin/assets/file.js', ['jquery'], '1.0', true );
    }
}