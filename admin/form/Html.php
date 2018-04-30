<?php
/**
 * Created by PhpStorm.
 * User: Scarlet
 * Date: 27.04.2018
 * Time: 09:40
 */

namespace Core\Admin\Form;

/**
 * HTML-Content Element
 * @package Core\Wordpress\Form
 */
class Html extends Element{

    /**
     * HTML-Content
     * @var string
     */
    private $content;

    /**
     * Html constructor.
     * @param string $content The HTML content
     */
    public function __construct(string $content){
        parent::__construct('');
        $this->content = $content;
    }

    /**
     * HTML-Elements do nothing on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
     public function process(Dispatcher $dispatcher): void{}

    /**
     * Renders the HTML content
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        echo $this->content;
    }

    /**
     * HTML-Elements do not have a value
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{}
}