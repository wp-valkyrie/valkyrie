<?php


namespace Core\Admin\Form;


class Group extends ParentElement{

    public $prefixString = '';
    public $suffixString = '';

    /**
     * Renders the individual Element
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     */
    public function render(Dispatcher $dispatcher): void{
        parent::render($dispatcher);
    }

    /**
     * Prefixes all child elements with the array convention
     * @param string $prefix prefix string
     */
    public function prefixName(string $prefix): void{
        $this->name = $prefix . $this->name;
        $childPrefix = trim($this->name, '-');
        if (substr_count($childPrefix, '[') !== substr_count($childPrefix, ']')) {
            $childPrefix .= ']';
        }
        $childPrefix .= '[';
        $this->prefixString = $childPrefix;
        parent::prefixName($childPrefix);
    }

    /**
     * Suffixes all child elements with the closing array convention
     * @param string $suffix suffix string
     */
    public function suffixName(string $suffix): void{
        $this->name .= $suffix;
        $this->suffixString = ']';
        parent::suffixName(']');
    }

    /**
     * Processes the individual Element on form submit
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     * @throws \Exception
     */
    public function process(Dispatcher $dispatcher): void{
        $value = $dispatcher->getValue($this->name);
        $dispatcher->save($this->name, $value);
    }

    /**
     * Sets the individual Elements value based on the Dispatcher
     * @param Dispatcher $dispatcher The current Elements Dispatcher-Object
     * @throws \Exception
     */
    public function setValue(Dispatcher $dispatcher): void{
        $value = $dispatcher->get($this->name);
        parent::setValue(new GroupDispatcher($dispatcher, $value));
    }
}