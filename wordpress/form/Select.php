<?php

namespace Core\Wordpress\Form;

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
     */
    public function render(): void{
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
     */
    public function process(): void{

    }
}