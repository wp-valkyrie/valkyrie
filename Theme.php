<?php
namespace IMA;
use Core\CoreItem;
use Core\RequireHandler;

/**
 * Main Theme Object
 * @package IMA
 */
class Theme extends CoreItem {

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
    }

    /**
     * Prepare all Includes for the theme
     * @return RequireHandler All theme required-files
     */
    public function require(): RequireHandler{
        $handler = new RequireHandler();
        $handler->addFile(__DIR__ . '/types/[!_]*.php', 'types');
        return $handler;
    }

    public function enqueue(): void{
        wp_enqueue_script( 'app-js', get_stylesheet_directory_uri() . '/assets/dist/js/app.js', [], '1.0', true );
        wp_enqueue_style( 'app-css', get_stylesheet_directory_uri() . '/assets/dist/css/app.css', [], '1.0', 'all');
    }
}