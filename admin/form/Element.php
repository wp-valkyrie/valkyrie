<?php

namespace Core\Admin\Form;

/**
 * Interface Element for dynamic integration into the Form class
 * @package Core\Wordpress\Form
 */
interface Element{

    /**
     * Renders the individual Element
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void;

    /**
     * Processes the individual Element on form submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void;

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void;
}