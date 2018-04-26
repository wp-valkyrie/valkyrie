<?php

namespace Core\Wordpress\Form;
use Core\Wordpress\Form\Selectable\Option;

/**
 * Base template for input types, with multiple options
 * @package Core\Wordpress\Form
 * @abstract
 */
abstract class Selectable implements Element {

    /**
     * Array of Option objects
     * @var array
     */
    private $options = [];

    /**
     * The name attribute for this Element
     * @var string
     */
    private $name;

    /**
     * Selectable constructor.
     * @param string $name The name attribute for this Element
     */
    public function __construct(string $name){
        $this->name = $name;
    }

    /**
     * Adds a new Option to the Selectable option-list
     * @param string $value The option value
     * @param string $label The displayed label associated with the option value
     * @param bool $checked True to select/tick this option in the Selectable
     */
    public function addOption(string $value, string $label, bool $checked = false): void{
        array_push($this->options, new Option($value, $label, $checked));
    }

    /**
     * Returns a list of Selectable Options for this Element
     * @return array Array of Option
     */
    public function getOptions(): array{
        return $this->options;
    }

    /**
     * The name attribute for this Element
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }
}