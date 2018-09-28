<?php

namespace Core\Admin\Form;

/**
 * Wrapper for the WP-Editor
 * @package Core\admin\form
 */
class Editor extends Element{

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
     * The editors label
     * @var string
     */
    private $label;

    /**
     * Editor constructor.
     * @param string $name The editors name-attribute
     * @param string $label The editors label-string
     * @param array $options The wp_editor Options array
     * @param string $content The editors default content
     * @see https://codex.wordpress.org/Function_Reference/wp_editor
     */
    public function __construct(string $name, string $label = '', array $options = [], string $content = ''){
        parent::__construct($name);
        $this->content = $content;
        $this->label = $label;
        $this->options = wp_parse_args($options, []);
    }

    /**
     * Renders the WYSIWYG Editor
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        ob_start();
        wp_editor($this->content, $this->name, $this->options);
        $input = ob_get_clean();
        $label = '<label>' . $this->label . '</label>';
        echo self::getRenderedField($label, $input);
    }

    /**
     * Saves the Editors content on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void{
        $value = $dispatcher->getValue($this->name);
        $dispatcher->save($this->name, $value);
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $this->content = $dispatcher->get($this->name);
    }
}