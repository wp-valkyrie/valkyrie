<?php

namespace Valkyrie;

/**
 * Handles one partial Component of a single Module
 * @package Valkyrie
 */
final class Component
{
    /**
     * Name of this submodule
     * @var string
     */
    private $name;

    /**
     * Pipeline handler for the current submodule
     * @var Pipeline
     */
    private $pipeline;

    /**
     * Constructor
     * @param string $name Name of this submodule
     * @param Pipeline $pipeline Pipeline handler for the current submodule
     */
    function __construct(string $name, Pipeline $pipeline)
    {
        $this->name = $name;
        $this->pipeline = $pipeline;
    }

    /**
     * Returns the component name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the component Pipeline handler
     * @return Pipeline
     */
    public function getPipeline(): Pipeline
    {
        return $this->pipeline;
    }
}