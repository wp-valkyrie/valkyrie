<?php

namespace Valkyrie\Form;

/**
 * Dispatches the Form-Elements save and get interaction
 */
class GroupDispatcher extends Dispatcher{

    /**
     * GroupDispatcher constructor.
     * @param Dispatcher $dispatcher Parent constructor
     * @param mixed $value new values to push to the child elements
     * @throws \Exception
     */
    public function __construct(Dispatcher $dispatcher, $value){
        parent::__construct($dispatcher->getType(), $dispatcher->getPrefix(), $dispatcher->getForm());
        if (!$value || !is_array($value)) {
            $value = [];
        }
        $this->setPost($value);
    }

    /**
     * Grouped Elements will be saved on the top level
     * @param string $name field name
     * @param mixed $value value to save
     * @return bool|int
     */
    public function save(string $name, $value){
        return true;
    }

    /**
     * Group Elements  can fetch the data from the current post array
     * @param string $name field name
     * @return mixed|null
     */
    public function get(string $name){
        $name = $this->cleanName($name);
        $post = $this->getPost();
        if (isset($post[$name])) {
            $data = $post[$name];
            if (is_string($data)){
                $data = stripslashes_deep($data);
            }
            return $data;
        }
        return null;
    }

    /**
     * Replaces all except the last array key
     * @param string $name field name
     * @return string
     */
    public function cleanName(string $name): string{
        $name = preg_replace('/.*\[/', '', $name);
        $name = trim($name, ']');
        return $name;
    }

    /**
     * Checks if the given key is present in this group
     * @param string $key
     * @return bool
     */
    public function isset(string $key): bool{
        $name = $this->cleanName($key);
        $post = $this->getPost();
        return isset($post[$name]);
    }
}
