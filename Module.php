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
     * Module constructor.
     * @param string $name Name of the Module
     * @param int $priority Priority of inclusion into the boilerplate
     */
    public function __construct(string $name, int $priority = 1){
        $this->name = $name;
        $this->priority = $priority;
        $this->requireFiles();

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Initializes the Module
     * @abstract
     */
    abstract public function init(): void;

    /**
     * Builds up a RequireHandler for later usage within this Module
     * @return RequireHandler The combined RequireHandler
     * @abstract
     */
    abstract public function require(): RequireHandler;

    /**
     * Includes the Module-Assets on the enqueue script-hook
     * @abstract
     */
    abstract public function enqueue(): void;

    /**
     * Adds the combined RequireHandler to the Module
     */
    private final function requireFiles(): void{
        $this->requireHandler = $this->require();
    }

    /**
     * Requires all files from the given group.
     * @param string $group group-name to require from the RequireHandler 'default' is the default value
     * @throws \Exception  If the requested group does not exist.
     */
    public final function requireGroup(string $group = 'default'): void{
        $this->requireHandler->dispatch($group);
    }
}
