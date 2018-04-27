<?php

namespace Core\Admin\Form;

/**
 * Interface Element for dynamic integration into the Form class
 * @package Core\Wordpress\Form
 */
interface Element{

    /**
     * Renders the individual Element
     */
    public function render(): void;

    /**
     * Processes the individual Element on form submit
     */
    public function process(): void;
}