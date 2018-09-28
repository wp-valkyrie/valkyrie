<?php

namespace Core\Admin\Form;

use Core\Form\Dispatcher;
use Core\Form\ParentElement;

class Box extends ParentElement{


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
     * Renders the Box with all its child Element objects
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        echo '<div class="core-box ' . implode(' ', $this->classes) . ' / js-core-target" data-name="' . $this->name . '">';
        if (!empty($this->title)) {
            echo '<div class="core-box__title"><h2>' . $this->title . '</h2></div>';
        }
        echo '<div class="core-box__inner">';
        parent::render($dispatcher);
        echo '</div>';
        echo '</div>';
    }
}