<?php

use Core\Module as Module;

class Core{

    /**
     * List of all registered Modules
     * @var Module[]
     */
    private static $modules = [];

    public static $dir = __DIR__;

    public static function prepare(){
        add_action('after_setup_theme', [self::class, 'load']);
    }

    public static function addModule(Module $module): void{
        array_push(self::$modules, $module);
    }

    public static function load(): void{
        self::orderModules();
        foreach (self::$modules as $module){
            $module->init();
        }
    }

    private static function orderModules(): void{
        uasort(self::$modules, function($a, $b){
            /* @var Module $a */
            /* @var Module $b */
            $a = $a->getPriority();
            $b = $b->getPriority();
            if ($a == $b){
                return 0;
            }
            return ($a < $b)? -1 : 1;
        });
    }
}