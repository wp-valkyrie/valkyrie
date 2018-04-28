<?php

namespace Core\Admin;


class Notice{
    const NONE = 10;
    const SUCCESS = 11;
    const ERROR = 12;
    const WARNING = 13;

    private $content;
    private $type;
    private $dismissible;

    public function __construct(string $content, int $type = self::NONE, bool $dismissible = false){
        $this->content = $content;
        $this->type = $type;
        $this->dismissible = $dismissible;
    }

    public function render(){
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
        echo '<div class="' . $class . '">' . apply_filters('the_content', $this->content) . '</div>';
    }
}