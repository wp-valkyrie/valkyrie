<?php

namespace Core\Admin\Form;

/**
 * Row handler, wraps multiple Column objects
 * @package Core\Admin\Form
 */
class Row implements Element{

    /**
     * List of child Column objects
     * @var array Array of Column objects
     */
    private $columns = [];

    /**
     * List of classes for the Row
     * @var array list of strings
     */
    private $classes = [];

    /**
     * Row constructor.
     * @param array classes List of classes for the Row
     */
    public function __construct(array $classes = []){
        $this->classes = array_unique(array_merge($classes, [
            'row'
        ]));
    }

    /**
     * Adds a new Column to the current Row
     * @param Column $column The new Column object
     */
    public function addColumn(Column $column){
        array_push($this->columns, $column);
    }

    /**
     * Processes the Row with all its Column objects on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void{
        foreach ($this->columns as $column){
            $column->process($dispatcher);
        }
    }

    /**
     * Renders the Row objects with all its Column objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        echo '<div class="row">';
        foreach ($this->columns as $column){
            $column->render($dispatcher);
        }
        echo '</div>';
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        foreach ($this->columns as $column){
            $column->setValue($dispatcher);
        }
    }
}