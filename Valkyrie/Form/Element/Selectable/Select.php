<?php

namespace Valkyrie\Form\Element\Selectable;

use Valkyrie\Form\Dispatcher;
use Valkyrie\Form\Element\Selectable;

/**
 * Selectbox Selectable
 */
class Select extends Selectable{

    /**
     * Selectbox label
     * @var string
     */
    private $label;

    /**
     * Select constructor.
     * @param string $name The Selectboxes name-attribute
     * @param string $label The Selectbox label
     */
    public function __construct(string $name, string $label){
        parent::__construct($name);
        $this->label = $label;
    }

    /**
     * Renders the Selectbox
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $id = uniqid();
        $label = '<label for="' . $id . '">' . $this->label . '</label>';
        $select = '<select id="' . $id . '" name="' . $this->name . '">';
        foreach ($this->getOptions() as $option) {
            $select .= '<option value="' . $option->value . '" ' . (($option->checked) ? 'selected' : '') . '>' . $option->label . '</option>';
        }
        $select .= '</select>';

        echo $label . $select;
    }

    /**
     * Saves the Selectbox on Form-Submit
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
        $value = $dispatcher->get($this->name);
        if ($value) {
            foreach ($this->getOptions() as $option) {
                if ($option->value === $value) {
                    $option->checked = true;
                } else {
                    $option->checked = false;
                }
            }
        }
    }
}