<?php

namespace Core\Form\Element;

use Core\Form\Dispatcher;
use Core\Form\Element;

/**
 * Wrapper for the WP-Editor
 * @package Core\admin\form
 */
class Editor extends Element{

    /**
     * The wp_editor Options array
     * @var array
     * @see https://codex.wordpress.org/Function_Reference/wp_editor
     */
    private $options;

    /**
     * The editors default content
     * @var string
     */
    private $content;

    /**
     * The editors label
     * @var string
     */
    private $label;

    /**
     * Editor ID
     * @var string
     */
    private $id;

    /**
     * Editor constructor.
     * @param string $name The editors name-attribute
     * @param string $label The editors label-string
     * @param array $options The wp_editor Options array
     * @param string $content The editors default content
     * @see https://codex.wordpress.org/Function_Reference/wp_editor
     */
    public function __construct(string $name, string $label = '', array $options = [], string $content = ''){
        parent::__construct($name);
        $this->content = $content;
        $this->label = $label;
        $this->id = uniqid();
        $this->options = wp_parse_args($options, []);
    }

    /**
     * Renders the WYSIWYG Editor
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        ob_start();
        ?>
        <div class="core-editor" data-editor-name="<?php echo $this->name;?>" data-editor-id="<?php echo $this->id;?>">
            <textarea class="core-editor__settings" disabled>
                <?php echo json_encode(wp_parse_args($this->options, self::getDefaultSettings()));?>
            </textarea>
            <div class="core-editor__template">
                <textarea disabled class="core-editor__wrapper" name="<?php echo $this->name;?>"><?php echo $this->content;?></textarea>
            </div>
            <div class="core-editor__area"></div>
        </div>
        <?php
        $input = ob_get_clean();
        $label = '<label>' . $this->label . '</label>';
        echo self::getRenderedField($label, $input);
    }

    /**
     * Saves the Editors content on Form-Submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function process(Dispatcher $dispatcher): void{
        $value = $dispatcher->getValue($this->name);
        $dispatcher->save($this->name, $value);
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function setValue(Dispatcher $dispatcher): void{
        $this->content = $dispatcher->get($this->name);
    }

    /**
     * Returns the default settings meant to be given to a javascript
     * function as JSON
     * @return array
     */
    public static function getDefaultSettings(): array{
        return [
            'mediaButtons' => true,
            'tinymce' => [
                'toolbar1' => 'bold,italic,bullist,numlist,link,blockquote,alignleft,aligncenter,alignright,strikethrough,hr,forecolor,pastetext,removeformat,codeformat,undo,redo,formatselect'
            ],
            'quicktags' => true
        ];
    }
}