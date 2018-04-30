<?php

namespace Core\Admin\Form;

/**
 * Dispatches the Form-Elements save and get interaction
 * @package Core\Admin\Form
 */
class Dispatcher{

    const META = 101;
    const OPTION = 102;

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
     * Dispatcher constructor.
     * @param int $type The Dispatcher Type: Dispatcher::META or Dispatcher::OPTION
     * @param string $prefix The key prefix for all saved values
     * @throws \Exception Throws Exception if $type is not Dispatcher::META or Dispatcher::OPTION
     */
    public function __construct(int $type, string $prefix){
        $this->type = $type;
        $this->prefix = $prefix;

        if ($type === self::META){
            $this->id = get_the_ID();
        }
        elseif ($type === self::OPTION){
            $this->id = null;
        }
        else{
            throw new \Exception('Type must be Dispatcher::META or Dispatcher::OPTION');
        }
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
     * Returns true if the current Dispatcher is from type META
     * @return bool
     */
    public function isMeta(): bool{
        return $this->type === self::META;
    }

    /**
     * Returns true if the dispatcher processes Form-Data
     * @return bool
     */
    public function hasPost(): bool{
        return !is_null($this->post);
    }

    /**
     * Returns true if the current Dispatcher is from type OPTION
     * @return bool
     */
    public function isOption(): bool{
        return $this->type === self::OPTION;
    }

    /**
     * Saves the given value depending on the Dispatcher-Type
     * @param string $name The values key
     * @param mixed $value The key to be saved
     * @return bool|int
     */
    public function save(string $name, $value){
        if($this->isMeta()){
            return update_post_meta($this->id, $name, $value);
        }
        else{
            return update_option($name, $value);
        }
    }

    /**
     * Retrieves the value depending on the Dispatcher-Type by the given key
     * @param string $name The values key
     * @return mixed
     */
    public function get(string $name){
        if ($this->isMeta()){
            return get_post_meta($this->id, $name, true);
        }
        else{
            return stripslashes(get_option($name));
        }
    }

    /**
     * Checks if the key exists in the Post-Data-Array
     * @param string $key The Post-Data-key
     * @return bool True if the value is set in the Post-Data
     */
    public function hasValue(string $key): bool{
        return isset($this->post[$key]);
    }

    /**
     * Checks if the key exists and is not empty
     * @param string $key the Post-Data-key
     * @return bool True if the value exists and is not empty
     */
    public function isFilled(string $key): bool{
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
        if ($this->hasValue($key)){
            return $this->post[$key];
        }
        return null;
    }
}