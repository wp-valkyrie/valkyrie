<?php

namespace Core\Admin\Form;

/**
 * Tick-Box Selectable, checkbox or radio
 * @package Core\Wordpress\Form
 */
class Tick extends Selectable{

    /**
     * Tick-Box type 'checkbox' or 'radio'
     * @var string
     */
    private $type;

    /**
     * Tick constructor.
     * @param string $type Tick-Box type 'checkbox' or 'radio'
     * @param string $name Tick-Box name-attribute
     */
    public function __construct(string $type, string $name){
        parent::__construct($name);
        $this->type = $type;
    }

    /**
     * Renders the Tick-Box
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        foreach ($this->getOptions() as $option){
            $id = uniqid();
            $label = '<label for="' . $id . '">' . $option->label  . '</label>';
            $radio = '<input id="' . $id . '" type="' . $this->type . '" name="' . $this->getName() . '" value="' . $option->value . '" ' . (($option->checked)?'checked':'') . ' />';
            echo $radio . $label;
        }
    }

    /**
     * Saves the Tick-Box on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void{
         $value = $dispatcher->getValue($this->getName());
         $dispatcher->save($this->getName(),$value);
     }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $value = $dispatcher->get($this->getName());
        if ($value){
            foreach ($this->getOptions() as $option){
                if ($option->value === $value){
                    $option->checked = true;
                }
                else{
                    $option->checked = false;
                }
            }
        }
    }
}