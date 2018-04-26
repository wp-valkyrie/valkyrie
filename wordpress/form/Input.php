<?php

namespace Core\Wordpress\Form;

/**
 * Basic Text-Input field
 * @package Core\Wordpress\Form
 */
class Input implements Element{

    /**
     * Input-Field name-attribute
     * @var string
     */
    private $name;

    /**
     * Input-Field type-attribute
     * @var string
     */
    private $type;

    /**
     * Input-Field label
     * @var string
     */
    private $label;

    /**
     * Input-Field tag-attributes
     * @var array
     */
    private $args;

    /**
     * Input constructor.
     * @param string $name Input-Field name-attribute
     * @param string $label Input-Field label
     * @param string $type Input-Field type-attribute
     * @param array $args key, value pair of all attributes for the input tag
     */
    public function __construct(string $name, string $label, string $type = 'text', array $args = []){
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->args = wp_parse_args($args, [
            'type' => $this->type,
            'name' => $this->name,
            'id' => uniqid()
        ]);
    }

    /**
     * Renders the Input-Field
     */
    public function render(): void{
        $argsString = '';
        foreach ($this->args as $attribute => $value){
            $argsString .= ' ' . $attribute . '="'. addslashes($value) .'"';
        }
        $label = '<label for="' . $this->args['id'] . '">' . $this->label .  '</label>';
        $input = '<input ' . $argsString .  ' /> ';
        echo $label . ' ' . $input;
    }

    /**
     * Saves the Input-Field on Form-Submit
     */
    public function process(): void{

    }

}