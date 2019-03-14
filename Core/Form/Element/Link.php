<?php

namespace Core\Form\Element;

use Core\Admin\Form\Group;
use Core\Form\Dispatcher;
use Core\Form\Element;

/**
 * Basic Checkbox Element
 * @package Core\Admin\Form
 */
class Link extends Element{

    private static $prepared = false;
    private $group;
    private $id;
    private $value;

    public function __construct(string $name){
        parent::__construct($name);
        $this->id = uniqid();
        self::prepare();

        // Build the link group
        $this->group = new Group($name);
        $this->group->addElement(new Input('link', '', 'text', ['data-link-part' => 'link']));
        $this->group->addElement(new Input('target', '', 'text', ['data-link-part' => 'target']));
        $this->group->addElement(new Input('title', '', 'text', ['data-link-part' => 'title']));
        $this->group->addElement(new Input('rel', '', 'text', ['data-link-part' => 'rel']));
    }

    /**
     * Prepares the usage of the wp editor dialog
     * once
     */
    public static function prepare(){
        if (self::$prepared) {
            return;
        }
        if (is_admin()) {
            require_once ABSPATH . 'wp-includes/class-wp-editor.php';
            add_action('admin_footer', function (){
                \_WP_Editors::wp_link_dialog();
            });
        }
    }

    /**
     * Prefixes the groups name
     * @param string $prefix
     */
    public function prefixName(string $prefix): void{
        $this->group->prefixName($prefix);
    }

    /**
     * Suffixes the groups name
     * @param string $suffix
     */
    public function suffixName(string $suffix): void{
        $this->group->suffixName($suffix);
    }

    /**
     * Renders the individual Element
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        ?>
        <div class="core-link">
            <div class="core-link__container">
                <button class="core-link__button / button">Edit Link</button>
                <textarea disabled id="<?php echo $this->id; ?>" class="core-link__area"></textarea>
                <div class="core-link__fields">
                    <?php $this->group->render($dispatcher); ?>
                </div>
                <div class="core-link__display">
                    <?php if (isset($this->value) && !empty($this->value['link'])): ?>
                        <div class="core-link__link">
                            <strong><?php echo $this->value['title']; ?></strong>
                            <small><?php echo $this->value['target']; ?></small>
                            <br/>
                            <?php echo $this->value['link']; ?>
                        </div>
                    <?php endif; ?>
                    <button class="core-link__clear"></button>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Processes the individual Element on form submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     * @throws \Exception
     */
    public function process(Dispatcher $dispatcher): void{
        $this->group->process($dispatcher);
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     * @throws \Exception
     */
    public function setValue(Dispatcher $dispatcher): void{
        $this->value = $dispatcher->get($this->group->name);
        $this->group->setValue($dispatcher);
    }
}