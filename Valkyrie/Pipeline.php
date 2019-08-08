<?php


namespace Valkyrie;


abstract class Pipeline{

    /**
     * @var Module
     */
    private $module;

    /**
     * Pipeline constructor.
     * @param Module $module
     */
    public function __construct(Module $module){
        $this->module = $module;
    }

    /**
     * Main entry point for the Pipeline
     * @param string $name function to call
     * @return mixed requested function return value
     * @throws \Exception throws an exception if the pipeline does not provide a method with the given name
     */
    public final function access(string $name){
        if (!method_exists($this, $name) || !(new \ReflectionMethod($this, $name))->isPublic()) {
            throw new \Exception('Pipeline Method ' . $name . ' does not exist, or is not public.');
        }
        return call_user_func_array([$this, $name], array_slice(func_get_args(), 1));
    }

    /**
     * Returns a list of all methods from the current pipeline
     * @return array
     */
    public final function getPipelineMethods(){
        try {
            $reflection = new \ReflectionClass($this);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            $methods = array_filter($methods, function(\ReflectionMethod $method){
               return $method->class !== self::class;
            });
            $methods = array_map(function(\ReflectionMethod $method){
                return $method->getName();
            }, $methods);
            return $methods;
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * Returns the parent module
     * @return Module
     */
    protected final function getModule(): Module{
        return $this->module;
    }
}