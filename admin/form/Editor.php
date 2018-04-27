<?php
/**
 * Created by PhpStorm.
 * User: Scarlet
 * Date: 27.04.2018
 * Time: 10:13
 */

namespace Core\Admin\Form;

/**
 * Wrapper for the WP-Editor
 * @package Core\admin\form
 */
class Editor implements Element{

    private $name;
    private $options;
    private $content;

    public function __construct(string $name, array $options = [], string $content = ''){
        $this->name = $name;
        $this->content = $content;
        $this->options = wp_parse_args($options, [
            'id' => uniqid()
        ]);
    }

    /**
     * Renders the WYSIWYG Editor
     */
    public function render(): void{
        wp_editor($this->content,$this->options['id'], $this->options);
    }

    /**
     * Saves the Editors content on Form-Submit
     */
    public function process(): void{
    }
}