<?php

namespace Core\Form\Element;

use Core\Form\Dispatcher;
use Core\Form\Element;

/**
 * Basic Textarea-Field
 * @package Core\Wordpress\Form
 */
class Area extends Element{

    /**
     * Textarea-Field label
     * @var string
     */
    private $label;

    /**
     * Textarea-Field tag-attributes
     * @var string[]
     */
    private $args;

    /**
     * Textarea-Field default value
     * @var string
     */
    private $value;

    /**
     * Area constructor.
     * @param string $name Textarea-Field name-attribute
     * @param string $label Textarea-Field label
     * @param array $args Textarea-Field tag-attributes
     * @param string $value Textarea-Field default value
     */
    public function __construct(string $name, string $label, array $args = [], string $value = ''){
        parent::__construct($name);
        $this->value = $value;
        $this->label = $label;
        $this->args = wp_parse_args($args, [
            'id' => uniqid()
        ]);
    }

    /**
     * Renders the Textarea-Field
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $this->args['name'] = $this->name;
        $argsString = '';
        foreach ($this->args as $attribute => $value) {
            $argsString .= ' ' . $attribute . '="' . addslashes($value) . '"';
        }
        $label = '<label for="' . $this->args['id'] . '">' . $this->label . '</label>';
        $input = '<textarea ' . $argsString . '>' . $this->value . '</textarea> ';

        echo self::getRenderedField($label, $input);
    }

    /**
     * Saves the Textarea-Field on Form-Submit
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
        $this->value = $dispatcher->get($this->name);
    }
}