<?php

namespace Valkyrie\Admin\Form;

/**
 * Condition Class to allow conditional Rendering in the Form-Objects
 */
class Condition{

    /**
     *  The name-field to watch
     * @var string
     */
    public $field;

    /**
     * The value to react upon
     * @var string
     */
    public $value;

    /**
     * True reverses the condition
     * @var bool
     */
    public $not;

    /**
     * The Element this Condition is bound to
     * @var Element
     */
    private $element;

    /**
     * The Name-Attribute of the bound Element
     * @var String
     */
    public $elementName;

    /**
     * Condition constructor.
     * @param string $field The name-field to watch
     * @param string $value The value to react upon
     * @param bool $not Reverse the condition
     */
    public function __construct(string $field, string $value, bool $not = false){
        $this->field = $field;
        $this->value = $value;
        $this->not = $not;
    }

    /**
     * Binds the Condition to the given Element
     * @param Element $element The Element this Condition belongs to
     */
    public function bindTo(Element $element): void{
        $this->element = $element;
        $this->elementName = $element->name;
    }
}