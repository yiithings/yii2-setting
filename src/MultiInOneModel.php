<?php

namespace yiithings\setting;

use BadMethodCallException;
use yii\base\Model;

trait MultiInOneModel
{
    /**
     * @var Model[]
     */
    private $_attributes = [];

    /**
     * @return array
     */
    public function attributes()
    {
        $attributes = parent::attributes();

        return array_merge($attributes, array_keys($this->_attributes));
    }

    /**
     * @return array
     */
    public function safeAttributes()
    {
        $attributes = parent::safeAttributes();

        return array_merge($attributes, array_keys($this->_attributes));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttributeObject($name)
    {
        return $this->_attributes[$name];
    }

    /**
     * @return array
     */
    public function getAttributeObjects()
    {
        return $this->_attributes;
    }

    /**
     * @param array $attributeObjects
     */
    public function setAttributeObjects($attributeObjects)
    {
        $this->_attributes = $attributeObjects;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->_attributes[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        return $this->getAttributeInternal($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        if ( ! $this->hasAttribute($name)) {
            return;
        }
        $this->setAttributeInternal($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getAttributeInternal($name)
    {
        throw new BadMethodCallException("This method must be overloaded: Get internal attribute $name");
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setAttributeInternal($name, $value)
    {
        throw new BadMethodCallException("This method must be overloaded: Set internal attribute $name");
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that attributes and related objects can be accessed like properties.
     *
     * @param string $name property name
     * @throws \yii\base\InvalidParamException if relation name is wrong
     * @return mixed property value
     * @see getAttribute()
     * @throws \yii\base\UnknownPropertyException
     */
    public function __get($name)
    {
        if (isset($this->_attributes[$name]) || array_key_exists($name, $this->_attributes)) {
            return $this->getAttribute($name);
        } elseif ($this->hasAttribute($name)) {
            return null;
        }

        $value = parent::__get($name);

        return $value;
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     *
     * @param string $name  property name
     * @param mixed  $value property value
     * @throws \yii\base\UnknownPropertyException
     */
    public function __set($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * Checks if a property value is null.
     * This method overrides the parent implementation by checking if the named attribute is `null` or not.
     * @param string $name the property name or the event name
     * @return bool whether the property value is null
     */
    public function __isset($name)
    {
        try {
            return $this->__get($name) !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sets a component property to be null.
     * This method overrides the parent implementation by clearing
     * the specified attribute value.
     * @param string $name the property name or the event name
     */
    public function __unset($name)
    {
        if ($this->hasAttribute($name)) {
            unset($this->_attributes[$name]);
        }
    }

}