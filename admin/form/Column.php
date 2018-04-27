<?php

namespace Core\Admin\Form;


class Column implements Element{

    /**
     * List of all child Element objects
     * @var array Array of Element Objects
     */
    private $elements = [];

    /**
     * List of classes for the Column
     * @var array List of strings
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
     */
    public function render(): void {
        echo '<div class="' . implode(' ', $this->classes) . '">';
        foreach ($this->elements as $column){
            $column->render();
        }
        echo '</div>';
    }

    /**
     * Processes all child Element objects
     */
    public function process(): void {
        foreach ($this->elements as $column){
            $column->process();
        }
    }
}