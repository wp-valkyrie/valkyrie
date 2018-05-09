<?php

namespace Core\Admin\Form;


class Box extends Element{

    /**
     * List of all child Element objects
     * @var Element[]
     */
    private $elements = [];

    /**
     * List of classes for the Box
     * @var string[]
     */
    private $classes;

    /**
     * The boxes title-text
     * @var string
     */
    private $title;

    /**
     * Box constructor.
     * @param string $title The boxes title-text
     * @param array $classes List of classes for the Box
     */
    public function __construct(string $title = '', array $classes = []){
        parent::__construct('');
        $this->title = $title;
        $this->classes = array_unique($classes);
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
     * Adds an Element object to the current Box
     * @param Element $element The new Element to add to the Box
     */
    public function addElement(Element $element): void{
        array_push($this->elements, $element);
    }

    /**
     * Renders the Box with all its child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void {
        echo '<div class="core-box ' . implode(' ', $this->classes) . ' / js-core-target" data-name="'.$this->name.'">';
        if (!empty($this->title)){
            echo '<div class="core-box__title"><h2>' . $this->title . '</h2></div>';
        }
        echo '<div class="core-box__inner">';
        foreach ($this->elements as $element){
            $element->render($dispatcher);
        }
        echo '</div>';
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