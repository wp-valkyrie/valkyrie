<?php

namespace Core;

use Core\Admin\Meta;
use Core\Admin\Notice;
use Core\Admin\Page;
use Core\Admin\Widget;

/**
 * Main API to interact with the Core Workflow
 */
class System{

    /**
     * List of all registered Modules
     * @var Module[]
     */
    private static $modules = [];

    /**
     * List of registered admin Pages
     * @var Page[]
     */
    private static $pages = [];

    /**
     * List of registered Meta boxes
     * @var Meta[]
     */
    private static $metas = [];

    /**
     * List of registered Admin Notices
     * @var Notice[]
     */
    private static $notices = [];

    /**
     * List of registered Widgets
     * @var Widget[]
     */
    private static $widgets = [];

    /**
     * Main Core Directory
     * @var string
     */
    public static $dir = __DIR__ . '/../';

    /**
     * Converts the given path string to a WP-URL
     * @param string $path The path which will be converted
     * @return string the url representation of the given path
     */
    public static function pathToUrl(string $path): string{
        $root = wp_normalize_path(ABSPATH);
        $path = wp_normalize_path($path);
        return esc_url_raw(str_replace($root, get_home_url() . '/', $path));
    }

    /**
     * Hooks the Core-Modules to the after_setup_theme hook
     */
    public static function prepare(){

        // Load Modules
        add_action('after_setup_theme', [self::class, 'load'], PHP_INT_MAX);

        // Load Meta Boxes
        add_action('add_meta_boxes', [self::class, 'renderMetas'], PHP_INT_MAX);
        add_action('save_post', [self::class, 'saveMetas'], PHP_INT_MAX);


        // Load Admin Pages
        add_action('admin_menu', [self::class, 'dispatchPages'], PHP_INT_MAX); // Normal Pages
        add_action('network_admin_menu', [self::class, 'dispatchNetworkPages'], PHP_INT_MAX); // Network admin pages

        // Load Notices
        add_action('admin_notices', [self::class, 'renderNotices'], PHP_INT_MAX);

        // Load Widgets
        add_action('widgets_init', [self::class, 'registerWidgets'], 99); // 99 is max priority possible for this hook
    }

    /**
     * Renders the MetaBoxes in the admin-panel
     */
    public static function renderMetas(): void{
        foreach (self::$metas as $meta) {
            $meta->render();
        }
    }

    /**
     * Saves the MetaBoxes on Form-Submit
     */
    public static function saveMetas(): void{
        foreach (self::$metas as $meta) {
            $meta->dispatch();
        }
    }

    /**
     * Renders the AdminPages in the admin-panel
     */
    public static function dispatchPages(): void{
        foreach (self::$pages as $page) {
            if (!$page->isMultisite()) {
                $page->dispatch();
            }
        }
    }

    /**
     * Renders the NetworkAdminPages in the admin-panel
     */
    public static function dispatchNetworkPages(): void{
        foreach (self::$pages as $page) {
            if ($page->isMultisite()) {
                $page->dispatch();
            }
        }
    }

    /**
     * Renders all Admin Notices in the admin-panel
     */
    public static function renderNotices(): void{
        foreach (self::$notices as $notice) {
            $notice->render();
        }
    }

    /**
     * Registers all Widgets in the admin-panel
     */
    public static function registerWidgets(): void{
        foreach (self::$widgets as $widget) {
            register_widget($widget->getWidget());
        }
    }

    /**
     * Adds a new Module to the core, which gets loaded automatically
     * @param Module $module New Module to add to the Core
     */
    public static function addModule(Module $module): void{
        array_push(self::$modules, $module);
    }

    /**
     * Adds a new admin Page to the core, which gets loaded automatically
     * @param Page $page The new admin Page
     * @param string|bool $parent The parent page this page is attached to
     * @param bool $multisite True if this page is only rendered on the
     */
    public static function addPage(Page $page, $parent = false, $multisite = false): void{
        if ($parent) {
            $page->pushWpParent($parent);
        }
        if ($multisite) {
            $page->setMultisite(true);
        }
        array_push(self::$pages, $page);
    }

    /**
     * Adds a Meta box to the core, which gets loaded automatically
     * @param Meta $meta The new meta box
     */
    public static function addMeta(Meta $meta): void{
        array_push(self::$metas, $meta);
    }

    /**
     * Adds a admin Notice to the core, which gets loaded in the admin panel
     * @param Notice $notice
     */
    public static function addNotice(Notice $notice): void{
        array_push(self::$notices, $notice);
    }

    /**
     * Adds a Widget to the core, which gets loaded in the admin panel
     * @param Widget $widget
     */
    public static function addWidget(Widget $widget): void{
        array_push(self::$widgets, $widget);
    }

    /**
     * Loads all Core-Modules
     * ignoring modules which do not have all their dependencies
     */
    public static function load(): void{
        $modules = self::getOrderModules();
        $activates = [];
        /** @var Module[] $queue */
        $queue = [];
        $notDone = true;
        while ($notDone) {
            $notDone = false;
            foreach ($modules as $module) {
                if ($module->isLoaded()) {
                    continue;
                }
                // Check if dependencies are met
                if (Module::checkDependencyStatus($module, $activates)) {
                    array_push($activates, $module->getName());
                    $module->boot();

                    // Check if any queued modules can be loaded
                    foreach ($queue as $key => $queuedModule) {
                        if (!$queuedModule->isLoaded() && Module::checkDependencyStatus($queuedModule, $activates)) {
                            $queuedModule->boot();
                            array_push($activates, $queuedModule->getName());
                            unset($queue[$key]);
                        }
                    }
                    $notDone = true;
                } else {
                    // Add to the queue
                    array_push($queue, $module);
                }
            }
        }
    }

    /**
     * Returns an ordered List of modules
     * @return Module[]
     */
    private static function getOrderModules(): array{
        $modules = array_values(self::$modules);
        uasort($modules, function (Module $a, Module $b){
            $a = $a->getPriority();
            $b = $b->getPriority();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        return $modules;
    }
}