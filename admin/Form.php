<?php

namespace Core\Admin;

use Core\Admin\Form\Dispatcher;
use Core\Admin\Form\Element;

/**
 * Form-Builder for the WP Backend
 * @package Core\Wordpress
 */
class Form{


    /**
     * List of all Form Elements
     * @var array Array of Element objects
     */
    private $items = [];

    /**
     * Identifier-String for the current Form
     * @var string
     */
    private $id;

    /**
     * Form constructor.
     * @param string $id Identifier-String for the current Form
     */
    public function __construct(string $id){
        $this->id = $id;
    }

    /**
     * Adds a new Element to the Form-Builder
     * @param Element $element The new Element
     */
    public function addElement(Element $element): void{
        array_push($this->items, $element);
    }

    /**
     * Calls the process method of all registered Element objects
     * @param Dispatcher $dispatcher The Dispatcher-Object
     */
    private function process(Dispatcher $dispatcher): void{
        foreach ($this->items as $item){
            $item->process($dispatcher);
        }
    }

    /**
     * Returns the Forms Nonce-String
     * @return string The hashed String
     */
    private function getNonceString(): string{
        return md5($this->id);
    }

    /**
     * Calls the render method of all registered Element objects
     * @param Dispatcher $dispatcher The Dispatcher-Object
     */
    private function render(Dispatcher $dispatcher): void{
        echo '<form id="'. sanitize_title($this->id) .'" action="'.'#'.sanitize_title($this->id).'" method="post">';
        foreach ($this->items as $item){
            echo '<div>';
            $item->setValue($dispatcher);
            $item->render($dispatcher);
            echo '</div>';
        }

        // Identifier for this Form
        echo '<input type="hidden" name="core-form" value="'.$this->id.'">';

        // WP-Nonce
        wp_nonce_field($this->getNonceString());

        echo '</form>';
    }

    /**
     * Processes the Form and renders the Elements
     * @param int $type The Dispatcher-Object Type
     * @throws \Exception
     */
    public function dispatch(int $type): void{
        $dispatcher = new Dispatcher($type, sanitize_title($this->id));
        if (isset($_POST['core-form']) && $_POST['core-form'] === $this->id && check_admin_referer($this->getNonceString())){
            $dispatcher->setPost($_POST);
            $this->process($dispatcher);
        }
        $this->render($dispatcher);
    }
}