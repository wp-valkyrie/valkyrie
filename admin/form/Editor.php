<?php

namespace Core\Admin\Form;

/**
 * Wrapper for the WP-Editor
 * @package Core\admin\form
 */
class Editor implements Element{

    /**
     * The editors name-attribute
     * @var string
     */
    private $name;

    /**
     * The wp_editor Options array
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/wp_editor
     */
    private $options;

    /**
     * The editors default content
     * @var string
     */
    private $content;

    /**
     * Editor constructor.
     * @param string $name The editors name-attribute
     * @param array $options The wp_editor Options array
     * @param string $content The editors default content
     * @see https://codex.wordpress.org/Function_Reference/wp_editor
     */
    public function __construct(string $name, array $options = [], string $content = ''){
        $this->name = $name;
        $this->content = $content;
        $this->options = wp_parse_args($options, []);
    }

    /**
     * Renders the WYSIWYG Editor
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        wp_editor($this->content,$this->name, $this->options);
    }

    /**
     * Saves the Editors content on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void{
         $value = $dispatcher->getValue($this->name);
         $dispatcher->save($this->name,$value);
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $this->content = $dispatcher->get($this->name);
    }
}