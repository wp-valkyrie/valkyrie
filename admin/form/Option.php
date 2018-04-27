<?php

namespace Core\Admin\Form\Selectable;

/**
 * Wrapper for Selectable options
 * @package Core\Wordpress\Form\Selectable
 */
class Option{

    /**
     * The Option value
     * @var string
     */
    public $value;

    /**
     * The Option label
     * @var string
     */
    public $label;

    /**
     * If the Option is checked or selected
     * @var bool
     */
    public $checked;

    /**
     * Option constructor.
     * @param string $value Option value
     * @param string $label Option label
     * @param bool $checked If the Option is checked or selected
     */
    public function __construct(string $value, string $label, bool $checked = false){
        $this->value = $value;
        $this->label = $label;
        $this->checked = $checked;
    }
}