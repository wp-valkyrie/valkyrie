<?php

namespace Core\Admin\Form;

/**
 * Interface Element for dynamic integration into the Form class
 * @package Core\Wordpress\Form
 */
abstract class Element{

    /**
     * The elements public name attribute
     * @var string
     */
    public $name;

    /**
     * List of all Conditions for this Element
     * @var Condition[]
     */
    private $conditions = [];


    /**
     * Element constructor.
     * @param string $name The Elements Name
     */
    public function __construct(string $name){
        if (empty($name) && $name !== '0') {
            $name = uniqid();
        }
        $this->name = $name;
    }

    /**
     * Returns the full conditions array
     * @return Condition[]
     */
    public function getLogic(): array{
        return $this->conditions;
    }

    /**
     * Adds a new Condition to this Element
     * @param Condition $condition New condition for this Element
     */
    public final function addCondition(Condition $condition): void{
        $condition->bindTo($this);
        array_push($this->conditions, $condition);
    }

    /**
     * Adds a prefix to the elements name
     * @param string $prefix The Prefix, which will be added to the elements name
     */
    public function prefixName(string $prefix): void{
        $this->name = $prefix . $this->name;
    }

    /**
     * Adds a suffix to the elements name
     * @param string $suffix The Suffix, which will be added to the elements name
     */
    public function suffixName(string $suffix): void{
        $this->name .= $suffix;
    }

    /**
     * Renders the individual Element
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public abstract function render(Dispatcher $dispatcher): void;

    /**
     * Processes the individual Element on form submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public abstract function process(Dispatcher $dispatcher): void;

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public abstract function setValue(Dispatcher $dispatcher): void;

    /**
     * Renders a core-field with the given field-lael and input-field
     * @param string $label The label-string (includes label tag)
     * @param string $field The field-string (full field-string)
     * @param array $classes List of classes for the core-field
     * @return string The full core-field HTML
     */
    public static function getRenderedField(string $label, string $field, array $classes = []): string{
        $out = '<div class="core-field ' . implode(' ', $classes) . ' / js-core-target">';
        if ($label) {
            $out .= '<div class="core-field__label">' . $label . '</div>';
        }
        if ($field) {
            $out .= '<div class="core-field__field">' . $field . '</div>';
        }
        $out .= '</div>';
        return $out;
    }
}