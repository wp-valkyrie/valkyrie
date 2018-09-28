<?php

namespace Core\Admin;

use Core\Admin\Form\Dispatcher;

/**
 * Handles Meta-Boxes for the WP-Admin-Panel
 * @package Core\Admin
 */
class Meta{

    /**
     * The Meta-Box ID-String
     * @var string
     */
    private $id;

    /**
     * The Meta-Box Title
     * @var string
     */
    private $title;

    /**
     * The screen or screens on which to show the box
     * @see https://developer.wordpress.org/reference/functions/add_meta_box/
     * @var array|null|string|\WP_Screen
     */
    private $screen;

    /**
     * The main Form Object to be executed inside the Meta-Box
     * @var Form
     */
    private $form;

    /**
     * Meta constructor.
     * @param string $id The Meta-Box ID-String
     * @param string $title The Meta-Box Title
     * @param Form $form The main Form Object to be executed inside the Meta-Box
     * @param string|array|\WP_Screen|null $screen The screen or screens on which to show the box
     * @see https://developer.wordpress.org/reference/functions/add_meta_box/
     */
    public function __construct(string $id, string $title, Form $form, $screen = null){
        $this->id = $id;
        $this->title = $title;
        $this->screen = $screen;
        $this->form = $form;
    }

    /**
     * Renders the MetaBox with its Form to the defined screen
     */
    public function render(): void{
        $form = $this->form;
        add_meta_box($this->id, $this->title, function ($test) use ($form){
            $form->dispatch(null, false, true);
        }, $this->screen);
    }

    /**
     * Processses the Form on Form-Submit
     */
    public function dispatch(): void{
        $this->form->dispatch(Dispatcher::META, true, false);
    }
}