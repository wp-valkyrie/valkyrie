<?php

namespace Core\Form;

/**
 * Interface Element for dynamic integration into the Form class
 * @package Core\Wordpress\Form
 */
abstract class ParentElement extends Element{
    /**
     * List of all child Element objects
     * @var Element[]
     */
    public $elements = [];

    /**
     * Adds an Element object to the current Element
     * @param Element $element The new Element to add to the Element
     */
    public function addElement(Element $element): void{
        array_push($this->elements, $element);
    }

    /**
     * Renders the Element with all its child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        foreach ($this->elements as $element) {
            $element->render($dispatcher);
        }
    }

    /**
     * Returns the conditions array of all children
     * @return Condition[]
     */
    public function getLogic(): array{
        $logic = parent::getLogic();
        foreach ($this->elements as $element) {
            $logic = array_merge($logic, $element->getLogic());
        }
        return $logic;
    }

    /**
     * Pass the name prefix to the Child Elements
     * @param string $prefix the name prefix
     */
    public function prefixName(string $prefix): void{
        foreach ($this->elements as $element) {
            $element->prefixName($prefix);
        }
    }

    /**
     * Pass the name suffix to the Child Elements
     * @param string $suffix the name suffix
     */
    public function suffixName(string $suffix): void{
        foreach ($this->elements as $element) {
            $element->suffixName($suffix);
        }
    }

    /**
     * Processes all child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void{
        foreach ($this->elements as $element) {
            $element->process($dispatcher);
        }
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        foreach ($this->elements as $element) {
            $element->setValue($dispatcher);
        }
    }
}