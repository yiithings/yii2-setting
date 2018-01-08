<?php

namespace yiithings\setting;

use InvalidArgumentException;
use Yii;
use yii\base\Component;
use yiithings\setting\models\SettingForm;

class Builder extends Component
{
    public $modelClass = 'yiithings\\setting\\models\\SettingForm';
    /**
     * @var bool
     */
    public $isNewValue = false;
    /**
     * @var bool
     */
    public $isNewDefaultValue = false;
    /**
     * @var string
     */
    private $_name;
    /**
     * @var string
     */
    private $_group;
    /**
     * @var string
     */
    private $_description;
    /**
     * @var mixed
     */
    private $_value;
    /**
     * @var mixed
     */
    private $_defaultValue;
    /**
     * @var Definition
     */
    private $_definition;
    /**
     * @var int
     */
    private $_sortOrder;
    /**
     * @var bool
     */
    private $_autoload;

    /**
     * Returns a build object.
     *
     * @param SettingForm $model
     * @param bool        $isNewInstance
     * @return SettingForm
     * @throws \yii\base\InvalidConfigException
     */
    public function build($model = null, $isNewInstance = false)
    {
        if ($model === null) {
            $model = Yii::createObject(['class' => $this->modelClass]);
            $isNewInstance = true;
        }
        if ($isNewInstance) {
            $model->name = $this->_name;
            $model->group = $this->_group;
            $model->value = $this->_value;
            $model->defaultValue = $this->_defaultValue;
            $model->sort_order = empty($this->_sortOrder) && $this->_sortOrder !== 0 ? 50 : $this->_sortOrder;
            $model->autoload = $this->_autoload ? '1' : '0';
            if ($this->_definition) {
                $this->_definition->bindTo($model);
            }
        } else {
            if ( ! empty($this->_name)) {
                $model->name = $this->_name;
            }
            if ( ! empty($this->_group)) {
                $model->group = $this->_group;
            }
            if ($this->isNewValue) {
                $model->value = $this->_value;
            }
            if ($this->isNewDefaultValue) {
                $model->defaultValue = $this->_defaultValue;
            }
            if ($this->_sortOrder !== null) {
                $model->sort_order = $this->_sortOrder;
            }
            if ($this->_autoload !== null) {
                $model->autoload = $this->_autoload ? '1' : '0';
            }
            if ($this->_definition) {
                $this->_definition->bindTo($model);
            } elseif ($this->_definition === false) {
                $model->definition = null;
            }
        }

        return $model;
    }

    /**
     * @param string $name
     * @return Builder
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * @param string $group
     * @return Builder
     */
    public function setGroup($group)
    {
        $this->_group = $group;

        return $this;
    }

    /**
     * @param string $description
     * @return Builder
     */
    public function setDescription($description)
    {
        $this->_description = $description;

        return $this;
    }

    /**
     * @param mixed $value
     * @return Builder
     */
    public function setValue($value)
    {
        $this->_value = $value;
        $this->isNewValue = true;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     * @return Builder
     */
    public function setDefaultValue($defaultValue)
    {
        $this->_defaultValue = $defaultValue;
        $this->isNewDefaultValue = true;

        return $this;
    }

    /**
     * @param mixed $definition
     * @return Builder
     * @throws \yii\base\InvalidConfigException
     */
    public function setDefinition($definition)
    {
        if ( ! $definition) {
            $this->_definition = null;

            return $this;
        }
        if ( ! $definition instanceof DefinitionInterface) {
            $definition = Yii::createObject($definition);
            if ( ! $definition instanceof DefinitionInterface) {
                throw new InvalidArgumentException("Invalid definition argument");
            }
        }
        $this->_definition = $definition;

        return $this;
    }

    /**
     * @param int $sortOrder
     * @return Builder
     */
    public function setSortOrder($sortOrder)
    {
        $this->_sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @param bool $autoload
     * @return Builder
     */
    public function setAutoload($autoload)
    {
        $this->_autoload = $autoload;

        return $this;
    }
}