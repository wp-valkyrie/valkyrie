<?php

namespace Core;

/**
 * Main CoreItem to enable all boilerplate integrations
 * @package Core
 * @abstract
 */
abstract class CoreItem{

    /**
     * @var string Core-Item Name
     */
    private $name;

    /**
     * @var int priority of inclusion into the boilerplate
     */
    private $priority;

    /**
     * @var RequireHandler Handler for required files
     */
    private $requireHandler;

    /**
     * CoreItem constructor.
     * @param string $name Name of the Core-Item
     * @param int $priority Priority of inclusion into the boilerplate
     */
    public function __construct(string $name, int $priority = 1){
        $this->name = $name;
        $this->priority = $priority;
        $this->requireFiles();

        add_action( 'wp_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Initializes the Core-Item
     * @abstract
     */
    abstract public function init(): void;

    /**
     * Builds up a RequireHandler for later usage within this CoreItem
     * @return RequireHandler The combined RequireHandler
     * @abstract
     */
    abstract public function require(): RequireHandler;

    /**
     * Includes the CoreItem-Assets on the enqueue script-hook
     * @abstract
     */
    abstract public function enqueue(): void;

    /**
     * Adds the combined RequireHandler to the CoreItem
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
