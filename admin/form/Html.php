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
class Html implements Element{

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
        $this->content = $content;
    }

    /**
     * HTML-Elements do nothing on Form-Submit
     */
    public function process(): void{}

    /**
     * Renders the HTML content
     */
    public function render(): void{
        echo $this->content;
    }
}