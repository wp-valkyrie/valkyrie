<?php

namespace Core\Admin;

use Core\Admin\Form\Condition;
use Core\Admin\Form\Dispatcher;
use Core\Admin\Form\Element;

/**
 * Form-Builder for the WP Backend
 * @package Core\Wordpress
 */
class Form{

    const MODULE_PREFIX_DELIMIT = '-';

    /**
     * List of all Form Elements
     * @var Element[]
     */
    private $items = [];

    /**
     * List of conditional Logik for this Form
     * @var Condition[]
     */
    private $logic = [];

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
     * Returns the Forms Nonce-String
     * @return string The hashed String
     */
    private function getNonceString(): string{
        return md5($this->id);
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
     * Adds the dispatchers-prefix to all Elements
     * @param Dispatcher $dispatcher
     */
    private function prefix(Dispatcher $dispatcher): void{
        foreach ($this->items as $item){
            $item->prefixName($dispatcher->getPrefix() . self::MODULE_PREFIX_DELIMIT);
        }
    }

    /**
     * Gets the logic from all Elements
     * and stores them into the member logic variable
     */
    private function fetchLogic(): void{
        $logic = [];
        foreach ($this->items as $item){
            $logic = array_merge($logic, $item->getLogic());
        }
        $this->logic = $logic;
    }

    /**
     * Injects the Form-Logic as a Script-Tag into the HMTML
     * gets called in the rendering method
     * @param Dispatcher $dispatcher The Dispatcher-Object
     * @todo cleanup this function, its kind of messy
     */
    private function injectLogic(Dispatcher $dispatcher): void{
        $json = json_encode($this->logic);
        $id = $this->id . uniqid();
        $prefix = $dispatcher->getPrefix() . self::MODULE_PREFIX_DELIMIT;
        ?>
        <script type="application/javascript">
            let <?php echo $id;?> = new Form("<?php echo $this->id;?>", "<?php echo $prefix;?>", <?php echo $json;?>.map((item)=>new Condition(item.field, item.value, item.not, item.elementName)))
            <?php echo $id;?>.init();
        </script>
        <?php
    }

    /**
     * Calls the render method of all registered Element objects
     * @param Dispatcher $dispatcher The Dispatcher-Object
     */
    private function render(Dispatcher $dispatcher): void{
        if ($dispatcher->isOption()){
            echo '<form id="'. sanitize_title($this->id) .'" action="'.'#'.sanitize_title($this->id).'" method="post">';
        }
        else{
            echo '<div id="'. sanitize_title($this->id) .'">';
        }

        foreach ($this->items as $item){
            echo '<div>';
            $item->setValue($dispatcher);
            $item->render($dispatcher);
            echo '</div>';
        }

        // Identifier for this Form
        echo '<input type="hidden" name="core-form" value="'.$this->id.'">';

        // WP-Nonce
        wp_nonce_field(__FILE__, $this->getNonceString());
        $this->injectLogic($dispatcher);

        if ($dispatcher->isOption()) {
            echo '</form>';
        }
        else{
            echo '</div>';
        }
    }

    /**
     * Processes the Form and renders the Elements
     * @param int $type The Dispatcher-Object-Type
     * @param bool $process True if the form should be processed on Form-Submit
     * @param bool process True if the form should be rendered
     */
    public function dispatch(int $type = null, bool $process = true, bool $render = true): void{
        if (is_null($type)){
            $type = Dispatcher::OPTION;
            if (get_current_screen()->parent_base === 'edit'){
                $type = Dispatcher::META;
            }
        }
        try{
            $dispatcher = new Dispatcher($type, sanitize_title($this->id));
            $this->prefix($dispatcher);
            if ($process){
                if (isset($_POST['core-form']) && $_POST['core-form'] === $this->id){
                    if (check_admin_referer(__FILE__, $this->getNonceString())){
                        $dispatcher->setPost($_POST);
                        $this->process($dispatcher);
                    }
                }
            }
            if ($render){
                $this->fetchLogic();
                $this->render($dispatcher);
            }
        } catch(\Exception $e){
            echo $e->getMessage();
        }
    }
}