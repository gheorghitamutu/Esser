<?php
/**
 * Created by PhpStorm.
 * User: Fr33LaNc3
 * Date: 6/9/2018
 * Time: 22:04
 */

namespace AppModel;

abstract class AbstractEntity
{
    protected $_values = array();
    protected $_allowedFields = array();

    /**
     * Constructor
     */
    public function __construct($fields)
    {
        $this->_allowedFields = $fields;
        foreach ($fields as $name) {
            $this->$name = $name;
        }
    }

    /**
     * Assign a value to the specified field via the corresponding mutator (if it exists);
     * otherwise, assign the value directly to the '$_values' array
     */
    public function __set($name, $value)
    {
        //echo $name . ' ' . $value;


        if (!in_array($name, $this->_allowedFields)) {
            throw new \InvalidArgumentException("Setting the field $name is not allowed for this entity.");
        }
        $mutator = 'set' . ucfirst($name);
        if (method_exists($this, $mutator) && is_callable(array($this, $mutator))) {
            $this->$mutator($value);
        }
        else {
            $this->_values[$name] = $value;
        }
    }

    /**
     * Get the value of the specified field (via the getter if it exists or by getting it from the $_values array)
     */
    public function __get($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new \InvalidArgumentException("Getting the field '$name' is not allowed for this entity.");
        }
        $accessor = 'get' . ucfirst($name);
        if (method_exists($this, $accessor) && is_callable(array($this, $accessor))) {
            return $this->$accessor;
        }
        if (isset($this->_values[$name])) {
            return $this->_values[$name];
        }
        throw new \InvalidArgumentException("The field '$name' has not been set for this entity yet.");
    }

    /**
     * Check if the specified field has been assigned to the entity
     */
    public function __isset($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new \InvalidArgumentException("The field '$name' is not allowed for this entity.");
        }
        return isset($this->_values[$name]);
    }

    /**
     * Unset the specified field from the entity
     */
    public function __unset($name)
    {
        if (!in_array($name, $this->_allowedFields)) {
            throw new \InvalidArgumentException("Unsetting the field '$name' is not allowed for this entity.");
        }
        if (isset($this->_values[$name])) {
            unset($this->_values[$name]);
            return true;
        }
        throw new \InvalidArgumentException("The field '$name' has not been set for this entity yet.");
    }

    /**
     * Get an associative array with the values assigned to the fields of the entity
     */
    public function toArray()
    {
        return $this->_values;
    }
}