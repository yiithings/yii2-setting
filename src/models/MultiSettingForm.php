<?php

namespace yiithings\setting\models;

use yii\base\Model;

/**
 * Class MultiSettingForm
 *
 * @package yiithings\setting\models
 * @property SettingForm[] settings
 */
class MultiSettingForm extends Model
{
    protected $settingAttributes = [];
    /**
     * @var SettingForm[]
     */
    private $_attributes = [];

    public function attributes()
    {
        $attributes = parent::attributes();

        return array_merge($attributes, array_keys($this->_attributes));
    }

    public function safeAttributes()
    {
        $attributes = parent::safeAttributes();

        return array_merge($attributes, array_keys($this->_attributes));
    }

    public function save()
    {
        $success = true;
        foreach ($this->_attributes as $name => $model) {
            if ( ! $model->save()) {
                $success = false;
                $errors = $model->getErrors();
                if (isset($errors['value'])) {
                    $this->addErrors([
                        $name => $errors['value'],
                    ]);
                }

            }
        }

        return $success;
    }

    /**
     * @param string $name
     * @return SettingForm
     */
    public function getSetting($name)
    {
        return $this->_attributes[$name];
    }

    /**
     * @param SettingForm[] $settings
     */
    public function setSettings($settings)
    {
        foreach ($settings as $model) {
            $this->setAttribute($model->name, $model);
        }
    }

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
        return $this->_attributes[$name]->value;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setAttribute($name, $value)
    {
        if (is_object($value) && $value instanceof SettingForm) {
            $this->_attributes[$name] = $value;
        } else {
            if ( ! $this->hasAttribute($name)) {
                return;
            }
            $this->_attributes[$name]->value = $value;
        }
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

//        if (isset($this->_related[$name]) || array_key_exists($name, $this->_related)) {
//            return $this->_related[$name];
//        }
        $value = parent::__get($name);
//        if ($value instanceof ActiveQueryInterface) {
//            return $this->_related[$name] = $value->findFor($name, $this);
//        }

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

//        elseif (array_key_exists($name, $this->_related)) {
//            unset($this->_related[$name]);
//        }
//
//        elseif ($this->getRelation($name, false) === null) {
//            parent::__unset($name);
//        }
    }
}