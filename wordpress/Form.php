<?php

namespace Core\Wordpress;

use Core\Wordpress\Form\Element;

/**
 * Form-Builder for the WP Backend
 * @package Core\Wordpress
 */
class Form{

    /**
     * List of all Form Elements
     * @var array Array of Element objects
     */
    private $items = [];

    /**
     * Adds a new Element to the Form-Builder
     * @param Element $element The new Element
     */
    public function addElement(Element $element): void{
        array_push($this->items, $element);
    }

    /**
     * Calls the process method of all registered Element objects
     */
    private function process(): void{
        foreach ($this->items as $item){
            $item->process();
        }
    }

    /**
     * Calls the render method of all registered Element objects
     */
    private function render(): void{
        foreach ($this->items as $item){
            echo '<div>';
            $item->render();
            echo '</div>';
        }
    }

    /**
     * Processes the Form and renders the Elements
     */
    public function dispatch(): void{
        $this->process();
        $this->render();
    }
}