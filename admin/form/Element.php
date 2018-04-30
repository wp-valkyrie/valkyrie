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
        $this->name = $name;
    }

    /**
     * Returns the full conditions array
     * @return Condition[]
     */
    public final function getLogic(): array{
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
}