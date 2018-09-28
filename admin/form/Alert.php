<?php

namespace Core\Admin\Form;

use Core\Admin\Notice;

class Alert extends Element{

    const FORM_SAVED = 10;

    /**
     * The Notice to render on the condition
     * @var Notice
     */
    private $notice;

    /**
     * Custom boolean expression or builtin constant
     * determines, if the notice will be rendered
     * @var bool|int
     */
    private $condition;


    /**
     * True, if the notice should be rendered
     * @var bool
     */
    private $render = false;

    /**
     * Notice constructor.
     * @param bool|int $condition Custom boolean expression or builtin constant
     * @param Notice $notice The Notice to display, if the condition is met
     */
    public function __construct($condition, Notice $notice){
        parent::__construct('');
        $this->notice = $notice;
        $this->condition = $condition;
    }

    /**
     * Renders the Notice if the condition is met
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        if (!$this->render && is_bool($this->condition)) {
            if ($this->condition) {
                $this->render = true;
            }
        }
        if ($this->render) {
            echo $this->notice;
        }
    }

    /**
     * The Notice does not have a processed value
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void{
        if ($this->condition === self::FORM_SAVED) {
            $this->render = true;
        }
    }

    /**
     * Allows the rendering if the condition is self::FORM_SAVED
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
    }
}