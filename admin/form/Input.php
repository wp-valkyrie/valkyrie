<?php

namespace Core\Admin\Form;

/**
 * Basic Text-Input field
 * @package Core\Wordpress\Form
 */
class Input extends Element{

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
     * @var string[] key value pair
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
        parent::__construct($name);
        $this->type = $type;
        $this->label = $label;
        $this->args = wp_parse_args($args, [
            'type' => $this->type,
            'id' => uniqid()
        ]);
    }

    /**
     * Renders the Input-Field
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $this->args['name'] = $this->name;
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
    public function setValue(Dispatcher $dispatcher): void {
        $this->args['value'] = htmlspecialchars($dispatcher->get($this->name));
    }
}