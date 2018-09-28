<?php

namespace Core;

/**
 * Main Module to enable all boilerplate integrations
 * @package Core
 * @abstract
 */
abstract class Module{

    /**
     * The Module Name
     * @var string
     */
    private $name;

    /**
     * The priority of inclusion into the boilerplate
     * @var int
     */
    private $priority;

    /**
     * Handler for required files
     * @var RequireHandler
     */
    private $requireHandler;

    /**
     * List of Module names which need to get loaded before this module gets initialised
     * @var string[]
     */
    private $dependencies;

    /**
     * Module constructor.
     * @param string $name Name of the Module
     * @param int $priority Priority of inclusion into the boilerplate
     * @param string[] $dependencies List of Module names which need to get loaded before this module gets initialised
     */
    public function __construct(string $name, int $priority = 1, array $dependencies = []){
        $this->name = $name;
        $this->priority = $priority;
        $this->dependencies = $dependencies;
        $this->requireFiles();

        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueue']);
    }

    /**
     * Initializes the Module
     * @abstract
     */
    abstract public function init(): void;

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @param RequireHandler $handler A fresh RequireHandler to add files to
     * @return RequireHandler The combined RequireHandler
     * @abstract
     */
    abstract public function require(RequireHandler $handler): RequireHandler;

    /**
     * Includes the Module-Assets on the enqueue script-hook
     * @abstract
     */
    abstract public function enqueue(): void;

    /**
     * Includes the Module-Assets on the admin enqueue script-hook
     * @abstract
     */
    abstract public function adminEnqueue(): void;

    /**
     * Adds the combined RequireHandler to the Module
     */
    private final function requireFiles(): void{
        $this->requireHandler = $this->require(new RequireHandler());
    }

    /**
     * Requires all files from the given group.
     * @param string $group group-name to require from the RequireHandler 'default' is the default value
     * @throws \Exception  If the requested group does not exist.
     */
    public final function requireGroup(string $group = 'default'): void{
        $this->requireHandler->dispatch($group);
    }

    /**
     * Returns the Priority of the current Module
     * @return int
     */
    public function getPriority(): int{
        return $this->priority;
    }

    /**
     * Returns this modules dependencies
     * @return string[]
     */
    public function getDependencies(): array{
        return $this->dependencies;
    }

    /**
     * Returns the Modules name
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * Checks if the given activated module names contain all dependencies for the given module
     * @param Module $module Module to check the depdency  status
     * @param array $activates list of currently active modules
     * @return bool
     */
    public static function checkDependencyStatus(Module $module, array $activates): bool {
        foreach ($module->getDependencies() as $dep){
            if (!in_array($dep, $activates)){
                return false;
            }
        }
        return true;
    }
}
