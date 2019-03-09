<?php


namespace Core;


abstract class Pipeline{
    /**
     * Main entry point for the Pipeline
     * @param string $name function to call
     * @return mixed requested function return value
     * @throws \Exception throws an exception if the pipeline does not provide a method with the given name
     */
    public final function access(string $name) {
        if (!method_exists($this, $name)){
            throw new \Exception('Pipeline Method ' . $name . ' does not exist.');
        }
        return call_user_func_array([$this, $name], array_slice(func_get_args(), 1));
    }
}