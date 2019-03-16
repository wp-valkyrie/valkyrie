<?php

namespace Valkyrie\Admin\Form\Structure;

use Valkyrie\Form\Dispatcher;
use Valkyrie\Form\ParentElement;

/**
 * Row Column Handler, wraps multiple Element Objects
 */
class Column extends ParentElement{


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
     * Renders the Column with all its child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        echo '<div class="core-row__item ' . implode(' ', $this->classes) . ' / js-core-target" data-name="' . $this->name . '">';
        parent::render($dispatcher);
        echo '</div>';
    }
}