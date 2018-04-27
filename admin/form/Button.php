<?php

namespace Core\admin\form;

/**
 * Basic button
 * @package Core\admin\form
 */
class Button implements Element{

    /**
     * The Button Text
     * @var string
     */
    private $text;

    /**
     * The Button-Classes
     * @var array List of strings
     */
    private $classes;

    /**
     * The Button-ID
     * @var string
     */
    private $id;

    /**
     * The buttons type attribute
     * @var string
     */
    private $type;

    /**
     * Button constructor.
     * @param string $text the button Text
     * @param array $classes the buttons classes
     * @param string $id the button id
     * @param string $type The buttons type attribute
     */
    public function __construct(string $text, array $classes = [],  string $id = '', string $type = 'submit'){
        $this->text = $text;
        $this->classes = array_unique(array_merge($classes, [
            'button'
        ]));
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * Renders the Button
     */
    public function render(): void{
        echo '<button id="' . $this->id . '" class="' . implode(' ', $this->classes) . '" ' . 'type="' . $this->type . '"'.'>' . $this->text . '</button>';
    }

    /**
     * Buttons do nothing on Form-Submit
     */
    public function process(): void{}
}