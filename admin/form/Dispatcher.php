<?php

namespace Core\Admin\Form;
use Core\Admin\Form;

/**
 * Dispatches the Form-Elements save and get interaction
 * @package Core\Admin\Form
 */
class Dispatcher{

    const META = 101;
    const OPTION = 102;
    const WIDGET = 103;

    const NOT_EXIST = '----not-exist----';

    /**
     * The Dispatcher Type: Dispatcher::META or Dispatcher::OPTION
     * @var int
     */
    private $type;

    /**
     * The key prefix for all saved values
     * @var string
     */
    private $prefix;

    /**
     * The Form-Post-Data
     * @var array
     */
    private $post = null;

    /**
     * The current Post_ID if $type is Dispatcher::META
     * @var int
     */
    private $id;


    /**
     * The Form this Dispatcher belongs to
     * @var Form
     */
    private $form;

    /**
     * Dispatcher constructor.
     * @param int $type The Dispatcher Type: Dispatcher::META or Dispatcher::OPTION
     * @param string $prefix The key prefix for all saved values
     * @throws \Exception Throws Exception if $type is not Dispatcher::META, Dispatcher::OPTION or Dispatcher::WIDGET
     */
    public function __construct(int $type, string $prefix, Form $form){
        $this->form = $form;
        $this->type = $type;
        $this->prefix = $prefix;

        if ($type === self::META){
            $this->id = get_the_ID();
        }
        elseif ($type === self::OPTION || $type === self::WIDGET){
            $this->id = null;
        }
        else{
            throw new \Exception('Type must be Dispatcher::META, Dispatcher::OPTION or Dispatcher::WIDGET');
        }
    }

    /**
     * Prepares the Element name for the task at hand depending on the dispatcher type
     * @param string $name The original name
     * @return string The updated name-string
     */
    private function cleanName(string $name): string{
        if ($this->isWidget()){
            $prefix = $this->prefix . $this->form->getDelimiter();
            if(strpos($name, $prefix) !== false){
                // Removes the Widget Boilerplate from the name-string
                $suffix = $this->form->getSuffix();
                $name = substr($name, strlen($prefix), strlen($name) - strlen($prefix) - strlen($suffix));
            }
        }
        return $name;
    }

    /**
     * Returns the current Dispatcher-Prefix
     * @return string
     */
    public function getPrefix(): string{
        return $this->prefix;
    }

    /**
     * Sets the Post-Data array
     * @param array $post The Post-Data
     */
    public function setPost(array $post): void{
        $this->post = $post;
    }

    /**
     * Returns true if the current Dispatcher is of type META
     * @return bool
     */
    public function isMeta(): bool{
        return $this->type === self::META;
    }

    /**
     * Returns true if the current Dispatcher is of type OPTION
     * @return bool
     */
    public function isOption(): bool{
        return $this->type === self::OPTION;
    }

    /**
     * Returns true if the current Dispatcher is of type Widget
     * @return bool
     */
    public function isWidget(): bool{
        return $this->type === self::WIDGET;
    }

    /**
     * Returns true if the dispatcher processes Form-Data
     * @return bool
     */
    public function hasPost(): bool{
        return !is_null($this->post);
    }

    /**
     * Saves the given value depending on the Dispatcher-Type
     * @param string $name The values key
     * @param mixed $value The key to be saved
     * @return bool|int
     */
    public function save(string $name, $value){
        $name = $this->cleanName($name);
        if($this->isMeta()){
            return update_post_meta($this->id, $name, $value);
        }
        elseif($this->isOption()){
            return update_option($name, $value);
        }
        else{
            return true;
        }
    }

    /**
     * Retrieves the value depending on the Dispatcher-Type by the given key
     * @param string $name The values key
     * @return mixed
     */
    public function get(string $name){
        $name = $this->cleanName($name);
        if ($this->isMeta()){
            return get_post_meta($this->id, $name, true);
        }
        elseif($this->isOption()){
            return stripslashes(get_option($name));
        }
        else{
            if ($this->hasValue($name)){
                return $this->post[$name];
            }
            return null;
        }
    }

    /**
     * Checks if the key exists in the Post-Data-Array
     * @param string $key The Post-Data-key
     * @return bool True if the value is set in the Post-Data
     */
    public function hasValue(string $key): bool{
        $key = $this->cleanName($key);
        return isset($this->post[$key]);
    }

    /**
     * Checks if the key exists and is not empty
     * @param string $key the Post-Data-key
     * @return bool True if the value exists and is not empty
     */
    public function isFilled(string $key): bool{
        $key = $this->cleanName($key);
        if ($this->hasValue($key)){
            return !empty($this->post[$key]);
        }
        return false;
    }

    /**
     * Gets the value from the Post-Data-Array
     * @param string $key the Post-Data-key
     * @return mixed
     */
    public function getValue(string $key){
        $key = $this->cleanName($key);
        if ($this->hasValue($key)){
            return $this->post[$key];
        }
        return null;
    }

    /**
     * Checks if a option or meta-value with the given key exists
     * @param string $key The key to check for
     * @return bool True if the key exists
     */
    public function isset(string $key){
        $key = $this->cleanName($key);
        global $wpdb;
        if ($this->isMeta()){
            return !empty($wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '$key' "));
        }
        elseif($this->isOption()){
            return get_option($key, self::NOT_EXIST) !== self::NOT_EXIST;
        }
        else{
            return $this->isset($this->post[$key]);
        }
    }
}