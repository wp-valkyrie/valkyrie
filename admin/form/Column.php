<?php

namespace Core\Admin\Form;

/**
 * Row Column Handler, wraps multiple Element Objects
 * @package Core\Admin\Form
 */
class Column extends Element{

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
        parent::__construct('');
        $this->classes = array_unique($classes);
    }

    /**
     * Returns the conditions array of all children
     * @return Condition[]
     */
    public function getLogic(): array{
        $logic = parent::getLogic();
        foreach ($this->elements as $element){
            $logic = array_merge($logic, $element->getLogic());
        }
        return $logic;
    }

    /**
     * Pass the name prefix to the Child Elements
     * @param string $prefix the name prefix
     */
    public function prefixName(string $prefix): void{
        foreach ($this->elements as $element){
            $element->prefixName($prefix);
        }
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
        echo '<div class="core-row__item ' . implode(' ', $this->classes) . ' / js-core-target" data-name="'.$this->name.'">';
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