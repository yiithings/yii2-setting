<?php

namespace yiithings\setting\models;

use InvalidArgumentException;
use Yii;
use yii\base\InvalidConfigException;
use yiithings\setting\AccessInterceptor;
use yiithings\setting\DefinableModel;
use yiithings\setting\Definition;
use yiithings\setting\DefinitionInterface;

/**
 * Class SettingForm
 *
 * @package yiithings\setting\models
 * @property mixed      $value
 * @property mixed      $defaultValue
 * @property Definition $definition
 */
class SettingForm extends Setting implements DefinableModel
{
    use AccessInterceptor;
    /**
     * @var Definition
     */
    private $_definition;
    /**
     * @var mixed
     */
    private $_value;
    /**
     * @var mixed
     */
    private $_defaultValue;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    /**
     * @return Definition|DefinitionInterface
     * @throws InvalidConfigException
     */
    public function getDefinition()
    {
        if ($this->_definition === null) {
            if (empty($this->definition_name)) {
                $definition = Yii::createObject([
                    'class' => Definition::className(),
                ]);
            } elseif (empty($this->definition_data)) {
                if ( ! class_exists($this->definition_name, true)) {
                    throw new InvalidArgumentException("Invalid definition class name");
                }
                $definition = Yii::createObject([
                    'class' => $this->definition_name,
                ]);
            } else {
                $definition = unserialize($this->definition_data);
                if ( ! is_object($definition)) {
                    throw new InvalidArgumentException("Invalid serialize data of definition_data");
                } elseif ( ! $definition instanceof Definition) {
                    throw new InvalidArgumentException("The serialized object is not a valid instance of " . Definition::className());
                }
            }
            /** @var Definition $definition */
            $definition->bindTo($this);
        }

        return $this->_definition;
    }

    /**
     * @param mixed $definition
     * @throws InvalidConfigException
     */
    public function setDefinition($definition)
    {
        if (is_object($definition) && $definition instanceof Definition) {
            $this->definition_name = get_class($definition);
            $this->definition_data = serialize($definition);
            $this->_definition = $definition;
            $this->_definition->activeAttributes = ['value', 'default_value'];

            return;
        }

        if (is_string($definition)) {
            $definition = (array)$definition;
        }
        if (is_array($definition) && ! isset($definition['class'])) {
            $definition['class'] = Definition::className();
        }
        $definition = Yii::createObject($definition);
        if ( ! is_object($definition) || ! $definition instanceof Definition) {
            throw new InvalidArgumentException("Invalid definition value");
        }
        $this->setDefinition($definition);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        if ( ! ($group = $this->getGroup())) {
            return $this->getName();
        }

        return $group . '.' . $this->getName();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute('name');
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if ( ! preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $name)) {
            throw new InvalidArgumentException("Invalid name argument value: $name");
        }

        $this->setAttribute('name', $name);
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->getAttribute('group');
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        if ($group === null) {
            $group = '';
        }
        if ($group !== '' && ! preg_match('#^[a-zA-Z_][a-zA-Z0-9_]*$#', $group)) {
            throw new InvalidArgumentException("Invalid group argument value: $group");
        }
        $this->setAttribute('group', $group);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute('type');
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->setAttribute('type', $type);
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getValue()
    {
        if ($this->_value === null) {
            $this->_value = $this->getDefinition()->getAttribute('value');
        }

        return $this->_value;
    }

    /**
     * @param mixed $value
     * @throws InvalidConfigException
     */
    public function setValue($value)
    {
        $this->getDefinition()->setAttribute('value', $value);
        $this->_value = $value;
    }

    /**
     * @return mixed
     * @throws InvalidConfigException
     */
    public function getDefaultValue()
    {
        if ($this->_defaultValue === null) {
            $this->_defaultValue = $this->getDefinition()->getAttribute('default_value');
        }

        return $this->_defaultValue;
    }

    /**
     * @param mixed $value
     * @throws InvalidConfigException
     */
    public function setDefaultValue($value)
    {
        $this->getDefinition()->setAttribute('default_value', $value);
        $this->_defaultValue = $value;
    }

    /**
     * @param string $attributeNames
     * @param bool   $clearErrors
     * @return bool
     * @throws InvalidConfigException
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) && $this->getDefinition()->validate();
    }
}