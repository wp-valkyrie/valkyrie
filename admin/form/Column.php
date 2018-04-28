<?php

namespace Core\Admin\Form;

/**
 * Row Column Handler, wraps multiple Element Objects
 * @package Core\Admin\Form
 */
class Column implements Element{

    /**
     * List of all child Element objects
     * @var Element[]
     */
    private $elements = [];

    /**
     * List of classes for the Column
     * @var string[]
     */
    private $classes;

    /**
     * Column constructor.
     * @param array $classes List of classes for the Column
     */
    public function __construct(array $classes = []){
        $this->classes = array_unique(array_merge($classes, [
            'column',
            'auto'
        ]));
    }

    /**
     * Adds an Element object to the current Column
     * @param Element $element The new Element to add to the Column
     */
    public function addElement(Element $element): void{
        array_push($this->elements, $element);
    }

    /**
     * Renders the Column with all its child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void {
        echo '<div class="' . implode(' ', $this->classes) . '">';
        foreach ($this->elements as $element){
            $element->render($dispatcher);
        }
        echo '</div>';
    }

    /**
     * Processes all child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void {
        foreach ($this->elements as $element){
            $element->process($dispatcher);
        }
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        foreach ($this->elements as $element){
            $element->setValue($dispatcher);
        }
    }
}