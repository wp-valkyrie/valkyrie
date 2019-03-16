<?php

namespace Valkyrie\Admin\Form\Structure;

use Valkyrie\Admin\Form\Condition;
use Valkyrie\Form\Dispatcher;
use Valkyrie\Form\Element;

/**
 * Row handler, wraps multiple Column objects
 */
class Row extends Element{

    /**
     * List of child Column objects
     * @var Column[]
     */
    private $columns = [];

    /**
     * List of classes for the Row
     * @var string[]
     */
    private $classes = [];

    /**
     * Row constructor.
     * @param array classes List of classes for the Row
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
        foreach ($this->columns as $column) {
            $logic = array_merge($logic, $column->getLogic());
        }
        return $logic;
    }

    /**
     * Pass the name prefix to the columns
     * @param string $prefix the name prefix
     */
    public function prefixName(string $prefix): void{
        foreach ($this->columns as $column) {
            $column->prefixName($prefix);
        }
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
        foreach ($this->columns as $column) {
            $column->process($dispatcher);
        }
    }

    /**
     * Renders the Row objects with all its Column objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        echo '<div class="core-row / js-core-target" data-name="' . $this->name . '">';
        foreach ($this->columns as $column) {
            $column->render($dispatcher);
        }
        echo '</div>';
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        foreach ($this->columns as $column) {
            $column->setValue($dispatcher);
        }
    }
}