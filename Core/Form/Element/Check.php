<?php

namespace Core\Form\Element;

use Core\Form\Dispatcher;
use Core\Form\Element;

/**
 * Basic Checkbox Element
 * @package Core\Admin\Form
 */
class Check extends Element{

    const NO_VALUE = '----no-value----';

    /**
     * The checkboxes label
     * @var string
     */
    private $label;

    /**
     * The checkboxes value attribute
     * @var string
     */
    private $value;

    /**
     * True if no value is given to the checkbox so it falls back to the basic html value
     * @var bool
     */
    private $onlyBool = false;

    /**
     * True if the checkbox is checked
     * @var bool
     */
    private $checked;

    /**
     * Checkbox constructor.
     * @param string $name The checkboxes name attribute
     * @param string $label The checkboxes label
     * @param bool $checked True if the checkbox is checked by default
     * @param string $value The checkboxes value attribute
     */
    public function __construct(string $name, string $label, bool $checked = false, string $value = self::NO_VALUE){
        parent::__construct($name);
        $this->label = $label;
        $this->value = $value;
        $this->checked = $checked;
        if ($this->value = self::NO_VALUE) {
            $this->onlyBool = true;
        }
    }

    /**
     * Renders the Checkbox
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        $id = uniqid();

        $value = '';
        if (!$this->onlyBool) {
            $value = 'value="' . $this->value . '"';
        }

        $label = '<label for="' . $id . '">' . $this->label . '</label>';
        $field = '<input type="checkbox" name="' . $this->name . '" id="' . $id . '" ' . $value . ' ' . (($this->checked) ? 'checked' : '') . ' />';
        echo self::getRenderedField($label, $field, ['core-field--check']);
    }

    /**
     * Processes the checkbox on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void{
        $value = $dispatcher->getValue($this->name);
        $dispatcher->save($this->name, $value);
    }

    /**
     * Checks or unchecks the Checkbox based on the Form-Data
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $value = $dispatcher->get($this->name);
        if ($dispatcher->isset($this->name)) {
            $checked = false;
            if ($this->onlyBool && $value === 'on') {
                $checked = true;
            } elseif ($value === $this->value) {
                $checked = true;
            }
            $this->checked = $checked;
        }
    }
}