<?php

namespace Valkyrie\Admin\Form\Grouping;

use Valkyrie\Form\Dispatcher;

/**
 * Basic Wrapper for repeating fields
 */
class Repeater extends Group{

    private $buildChilds;
    private $id;
    private $title;
    private $description;
    private $itemName;

    /**
     * Repeater constructor.
     * @param string $name repeater name
     * @param string $title Repeater-Title
     * @param callable $buildChilds function which builds the repeating groups
     * @param string $description Description string below the title
     * @param string $itemName Name used for the add and remove buttons
     */
    public function __construct(string $name, string $title, callable $buildChilds, string $description = '', string $itemName = 'Item'){
        parent::__construct($name);
        $this->id = uniqid();
        $this->itemName = $itemName;
        $this->title = $title;
        $this->description = $description;
        $this->buildChilds = $buildChilds;
    }

    /**
     * Inject all children elements into the repeater
     * @param Dispatcher $dispatcher
     */
    private function injectChildren(Dispatcher $dispatcher){
        $value = $dispatcher->get($this->name);
        if (!is_array($value)) {
            $value = [];
        }
        for ($i = 0; $i < count($value); $i++) {
            $this->addElement($this->newChildren($i));
        }
    }

    /**
     * Adds a children with the given name into the repeater
     * @param string $name
     * @return Group Group element with the given name
     */
    private function newChildren(string $name): Group{
        $g = new Group($name);
        ($this->buildChilds)($g);
        $g->suffixName($this->suffixString);
        $g->prefixName($this->prefixString);
        return $g;
    }

    /**
     * Renders the Repeater with the repeating template and all its children
     * @param Dispatcher $dispatcher
     */
    public function render(Dispatcher $dispatcher): void{
        echo '<div class="core-repeater" data-repeat-id="' . $this->id . '">';
        echo '<div class="core-repeater__head">';
        echo '<h2>' . $this->title . '</h2>';
        echo wpautop($this->description);
        echo '</div>';

        // Echo Template String
        echo $this->getTemplate($dispatcher);

        // Echo Children
        $i = 0;
        echo '<div class="core-repeater__container">';
        foreach ($this->elements as $element) {
            echo '<div class="core-repeater__item" data-repeat-id="' . $i . '" data-repeat-pattern="' . $this->getRepeatPattern() . '">';
            $element->render($dispatcher);
            echo $this->getRemoveButton();
            echo '</div>';
            $i++;
        }
        echo '</div>';

        // Echo Repeating-Menu
        echo '<div class="core-repeater__menu">';
        echo '<button class="core-repeater__button / button">' . sprintf(__('Add %s'), $this->itemName) . '</button>';
        echo '</div>';
        echo '</div>';
    }

    /**
     * Builds the remove repeat-item button
     * @return string
     */
    private function getRemoveButton(): string{
        return '<button class="core-repeater__button / button" tabindex="-1">' . sprintf(__('Remove %s'), $this->itemName) . '</button>';
    }

    /**
     * Builds the repeating template html content
     * @param Dispatcher $dispatcher
     * @return string
     */
    private function getTemplate(Dispatcher $dispatcher): string{
        ob_start();
        echo '<div class="core-repeater__item" data-repeat-id="' . $this->id . '" data-repeat-pattern="' . $this->getRepeatPattern() . '">';
        $this->newChildren($this->id)->render($dispatcher);
        echo $this->getRemoveButton();
        echo '</div>';
        $content = ob_get_clean();

        $return = '<textarea class="core-repeater__template" disabled>';
        $return .= htmlentities2($content);
        $return .= '</textarea>';
        return $return;
    }

    /**
     * Inject the children into the repeater
     * @param Dispatcher $dispatcher
     * @throws \Exception
     */
    public function setValue(Dispatcher $dispatcher): void{
        if (count($this->elements) === 0) {
            $this->injectChildren($dispatcher);
        }
        parent::setValue($dispatcher);
    }

    /**
     * Generate the regexp pattern which is used to replace attributes to fit the required $_POST format
     * @return string
     */
    private function getRepeatPattern(): string{
        $pattern = $this->prefixString . '\d+' . $this->suffixString;
        $pattern = str_replace(['[', ']'], ['\[', '\]'], $pattern);
        return $pattern;
    }
}