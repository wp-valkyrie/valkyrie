<?php

namespace Core\Admin\Form;

/**
 * Selectbox Selectable
 * @package Core\Wordpress\Form
 */
class Select extends Selectable{

    /**
     * Selectbox label
     * @var string
     */
    private $label;

    /**
     * Select constructor.
     * @param string $label The Selectbox label
     * @param string $name The Selectboxes name-attribute
     */
    public function __construct(string $label, string $name){
        parent::__construct($name);
        $this->label = $label;
    }

    /**
     * Renders the Selectbox
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $id = uniqid();
        $label = '<label for="' . $id . '">' . $this->label .  '</label>';
        $select = '<select id="' . $id . '" name="' . $this->getName() . '">';
        foreach ($this->getOptions() as $option){
            $select .= '<option value="'.$option->value.'" ' . (($option->checked)?'selected':'') . '>' . $option->label . '</option>';
        }
        $select .= '</select>';

        echo $label . $select;
    }

    /**
     * Saves the Selectbox on Form-Submit
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