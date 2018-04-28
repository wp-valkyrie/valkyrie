<?php
namespace Core\Admin\Form;

/**
 * Basic Textarea-Field
 * @package Core\Wordpress\Form
 */
class Area implements Element{

    /**
     * Textarea-Field name-attribute
     * @var string
     */
    private $name;

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
        $this->value = $value;
        $this->name = $name;
        $this->label = $label;
        $this->args = wp_parse_args($args, [
            'name' => $this->name,
            'id' => uniqid()
        ]);
    }

    /**
     * Renders the Textarea-Field
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $argsString = '';
        foreach ($this->args as $attribute => $value){
            $argsString .= ' ' . $attribute . '="'. addslashes($value) .'"';
        }
        $label = '<label for="' . $this->args['id'] . '">' . $this->label .  '</label>';
        $input = '<textarea ' . $argsString .  '>' . $this->value . '</textarea> ';
        echo $label . ' ' . $input;
    }

    /**
     * Saves the Textarea-Field on Form-Submit
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
        $this->value = $dispatcher->get($this->name);
    }
}