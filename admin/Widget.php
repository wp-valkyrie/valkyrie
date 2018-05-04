<?php

namespace Core\Admin;

use Core\Admin\Form\Dispatcher;

/**
 * Handles Custom Widgets
 * @package Core\Admin
 */
class Widget {

    /**
     * The Widgets Render Function
     * @var \Closure
     */
    private $render;

    /**
     * The Widgets Form builder Function
     * @var \Closure
     */
    private $formHandler;

    /**
     * The \WP_Widget this Widget is associated with
     * @var \WP_Widget
     */
    private $widget;

    /**
     * The Widget ID-String
     * @var string
     */
    private $id;

    /**
     * The Widget Title/Name in the Admin Panel
     * @var string
     */
    private $description;

    /**
     * he Widget Title/Name in the Admin Panel
     * @var string
     */
    private $name;

    /**
     * Widget constructor.
     * @param string $id The widget ID-String. Must be unique
     * @param string $name The Widget Title/Name in the Admin Panel
     * @param string $description The Widget Description
     * @param \Closure $render The Render Function, gets array $values (Widget values) and array $atts (Widget attributes) expects void
     * @param \Closure $formHandler The Form builder, gets Form $form expects void
     */
    public function __construct(string $id, string $name, string $description, \Closure $render, \Closure $formHandler){
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->render = $render;
        $this->formHandler = $formHandler;
        $this->widget = self::getWpWidget($this, $id, $name, $description);
    }

    /**
     * Gets the associated \WP_Widget
     * @return \WP_Widget
     */
    public function getWidget(): \WP_Widget{
        return $this->widget;
    }

    /**
     * Gets the build Form for this Widget
     * @return Form
     */
    public function getForm(): Form{
        $name = str_replace('[]', '', $this->widget->get_field_name(''));
        $form = new Form($name);
        $form->setDelimiter('[');
        $form->setSuffix(']');
        ($this->formHandler)($form);
        return $form;
    }

    /**
     * Renders the Form in the admin-panel
     * @param array $values the form-values
     * @throws \Exception
     */
    public function renderForm(array $values): void{
        $form = $this->getForm();

        // custom form Dispatcher in order to push values inside
        $dispatcher = new Dispatcher(Dispatcher::WIDGET, $form->getId(), $form);
        $dispatcher->setPost($values);

        // do boilerplate stuff which will not be done otherwise
        $form->prefix($dispatcher);
        $form->suffix();

        // Render the Form with all values
        $form->render($dispatcher);
    }

    /**
     * Saves the form-data
     * @param array $values List of new values
     * @param array $target List of old values
     * @return array the filled data-array
     */
    public function saveForm(array $values, array $target): array{
        foreach ($values as $key => $value){
            $target[$key] = $value;
        }
        return $target;
    }

    /**
     * Renders the Widget on the Frontend
     * @param array $args the Widget attributes
     * @param array $values The Widget values
     */
    public function renderWidget(array $args, array $values): void{
        ($this->render)($values, $args);
    }

    /**
     * Creates an anonymous Object extending WP_Widget which integrates into the Core::Widget Api
     * @param Widget $instance Instance of Widget, the new WP_Widget will get associated with
     * @param string $id The Widgets id-string
     * @param string $name The Widgets name in the admin panel
     * @param string $description The Widgets description
     * @return \WP_Widget Instance of the WP_Widget Object integrated in the Core::Widget workflow
     * @todo Check if anonymous class should be defined statically or can be defined on every getWpWidget.
     */
    private static function getWpWidget(Widget $instance, string $id, string $name, string $description): \WP_Widget{

        // This is broken up into this anonymous class in order to integrate more easily with the Core workflow and to
        // avoid conflicts which would occur otherwise

        // The class here does not need to be anonymous and could be defined outside with an appropriate name.
        // But because it has no other point then to integrate with the workflow defined in Widget, there really
        // is no point.

        /**
         * Extends \WP-Widget and allows integration integration of
         * \WP_Widgets into the Core::Widget workflow
         */
        return new class($instance, $id, $name, $description) extends \WP_Widget{

            /**
             * Link to the Widget instance, which handles this \WP_Widget instance
             * @var Widget
             */
            private $coreWidget;

            /**
             * WP_Widget constructor
             * @param Widget $coreWidget the associated Widget Object
             * @param string $id The Widgets id-string
             * @param string $name The Widgets title-string
             * @param string $description The Widgets description
             */
            public function __construct(Widget $coreWidget, string $id, string $name, string $description){
                parent::__construct($id, $name, ['description'=>$description]);
                $this->coreWidget = $coreWidget;
            }

            /**
             * Renders the Form with the associated Widget
             * @param array $instance array of values
             * @return void
             */
            public function form($instance){
                try{
                    $this->coreWidget->renderForm($instance);
                } catch(\Exception $e){
                    echo $e->getMessage();
                }
            }

            /**
             * Saves the Form-Data with the associated Widget
             * @param array $new_instance new values
             * @param array $old_instance old values
             * @return array values to be saved
             */
            public function update($new_instance, $old_instance){
                return $this->coreWidget->saveForm($new_instance, $old_instance);
            }

            /**
             * Renders the Widget with the associated Widget
             * @param array $args the Widget attributes
             * @param array $instance the Widget values
             */
            public function widget($args, $instance){
                $this->coreWidget->renderWidget($args, $instance);
            }
        };
    }
}