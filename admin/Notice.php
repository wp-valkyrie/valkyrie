<?php

namespace Core\Admin;

/**
 * Basic Admin Notice
 * @package Core\Admin
 */
class Notice{
    const NONE = 10;
    const SUCCESS = 11;
    const ERROR = 12;
    const WARNING = 13;

    /**
     * The Notice-Content
     * @var string
     */
    private $content;

    /**
     * he Notice-type (color)
     * @var int
     */
    private $type;

    /**
     * True if the notice is dismissible
     * @var bool
     */
    private $dismissible;

    /**
     * Notice constructor.
     * @param string $content The Notice-Content
     * @param int $type The Notice-type (color)
     * @param bool $dismissible True if the notice is dismissible
     * @todo Don't show notice after it was dismissed
     */
    public function __construct(string $content, int $type = self::NONE, bool $dismissible = false){
        $this->content = $content;
        $this->type = $type;
        $this->dismissible = $dismissible;
    }

    /**
     * Renders the Notice
     */
    public function render(){
       echo $this;
    }

    /**
     * Returns the Notice HTML-Content
     * according to the set type and content
     * @return string
     */
    public function __toString(): string{
        $class = 'notice';
        switch ($this->type){
            case self::SUCCESS :
                $class .= ' notice-success';
                break;
            case self::WARNING :
                $class .= ' notice-warning';
                break;
            case self::ERROR :
                $class .= ' notice-error';
                break;
        }
        if ($this->dismissible){
            $class .= ' is-dismissible';
        }
        return '<div class="' . $class . '">' . apply_filters('the_content', $this->content) . '</div>';
    }
}