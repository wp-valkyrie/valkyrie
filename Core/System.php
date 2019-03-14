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
        // Catches create cpt-page early action trigger
        if (!empty($_POST)) {
            foreach (self::$metas as $meta) {
                $meta->dispatch();
            }
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
     * @throws \Exception throws an exception if a module with the given modules name already exists
     */
    public static function addModule(Module $module): void{
        if (isset(self::$modules[$module->getName()])) {
            throw new \Exception("Module with the name " . $module->getName() . " already exists");
        }
        self::$modules[$module->getName()] = $module;
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
                    $module->boot();
                    array_push($activates, $module->getName());
                    do_action('core_module_loaded', $module->getName());

                    // Check if any queued modules can be loaded
                    foreach ($queue as $key => $queuedModule) {
                        if (!$queuedModule->isLoaded() && Module::checkDependencyStatus($queuedModule, $activates)) {
                            $queuedModule->boot();
                            array_push($activates, $queuedModule->getName());
                            do_action('core_module_loaded', $queuedModule->getName());
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
        do_action('core_modules_loaded', $activates);
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

    /**
     * Establishes a connection to the given modules public API if possible
     * @param string $module The name of the module to connect an API-Pipeline to
     * @return Pipeline
     * @throws \Exception throws an exception if the module does not exists, does not  provide an api or something else went wrong
     */
    public static function API(string $module){
        /** @var Pipeline[] $pipelines */
        static $pipelines;

        // Create the array if it does not exist yet
        if (!isset($pipelines)) {
            $pipelines = [];
        }

        // Returns cached pipelines if possible
        if (isset($pipelines[$module])) {
            return $pipelines[$module];
        }

        // Validate the requested module
        if (!isset(self::$modules[$module])) {
            throw new \Exception('Module with the name ' . $module . ' does not exist and can therefore not be accessed with System::API');
        }
        if (!in_array('Core\API', class_implements(get_class(self::$modules[$module])))) {
            throw new \Exception('Module with the name ' . $module . ' does not implements the Core\API interface and therefore does not provide a public API');
        }

        // sets and returns the module pipeline
        /** @var API $apiModule */
        $apiModule = self::$modules[$module];
        $pipeline = $apiModule->getPipeline();

        $pipelines[$module] = $pipeline;
        return $pipeline;
    }
}