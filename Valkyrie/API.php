<?php


namespace Valkyrie;

/**
 * This interface is meant to be applied to
 * a Module Object, allowing for cross Module communication
 */
interface API{
    /**
     * Returns the current Modules API-Pipeline instance
     * @return Pipeline
     */
    public function getPipeline(): Pipeline;
}