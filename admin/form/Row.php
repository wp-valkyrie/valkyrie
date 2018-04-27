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
     */
    public function process(): void{
        foreach ($this->columns as $column){
            $column->process();
        }
    }

    /**
     * Renders the Row objects with all its Column objects
     */
    public function render(): void{
        echo '<div class="row">';
        foreach ($this->columns as $column){
            $column->render();
        }
        echo '</div>';
    }
}