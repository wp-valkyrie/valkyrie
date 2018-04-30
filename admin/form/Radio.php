<?php

namespace Core\Admin\Form;

/**
 * Tick-Box Selectable, checkbox or radio
 * @package Core\Wordpress\Form
 */
class Radio extends Selectable{


    /**
     * Tick constructor.
     * @param string $name Tick-Box name-attribute
     */
    public function __construct(string $name){
        parent::__construct($name);
    }

    /**
     * Renders the Tick-Box
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        foreach ($this->getOptions() as $option){
            $id = uniqid();
            $label = '<label for="' . $id . '">' . $option->label  . '</label>';
            $radio = '<input id="' . $id . '" type="radio" name="' . $this->name . '" value="' . $option->value . '" ' . (($option->checked)?'checked':'') . ' />';
            echo $radio . $label;
        }
    }

    /**
     * Saves the Tick-Box on Form-Submit
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
        $value = $dispatcher->get($this->name);
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