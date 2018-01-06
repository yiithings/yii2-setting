<?php

namespace yiithings\setting\models;

use ArrayObject;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidConfigException;
use yii\validators\Validator;
use yiithings\setting\Rule;

/**
 * Class SettingForm
 *
 * @package yiithings\setting\models
 * @property mixed $value
 * @property mixed $defaultValue
 * @property Rule  $rule
 */
class SettingForm extends Setting
{
    protected $appendRules = false;
    /**
     * @var mixed
     */
    private $_value;
    /**
     * @var mixed
     */
    private $_defaultValue;
    /**
     * @var Rule
     */
    private $_rule;

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) && $this->validateRule();
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

    public function getValue()
    {
        if ($this->_value === null) {
            $this->_value = $this->getDataAttribute('data');
        }

        return $this->_value;
    }

    public function setValue($value)
    {
        $this->setDataAttribute('data', $value);
        $this->_value = $this->data;
    }

    public function getDefaultValue()
    {
        if ($this->_defaultValue === null) {
            $this->_defaultValue = $this->getDataAttribute('default_data');
        }

        return $this->_defaultValue;
    }

    public function setDefaultValue($value)
    {
        if ($value === null) {
            $this->default_data = null;

            return;
        }
        $this->setDataAttribute('default_data', $value);
        $this->_defaultValue = $this->default_data;
    }

    /**
     * @return Rule
     * @throws \yii\base\InvalidConfigException
     */
    public function getRule()
    {
        if ($this->_rule === null) {
            if (empty($this->rule_name)) {
                $this->_rule = Yii::createObject([
                    'class' => 'yiithings\setting\Rule',
                ]);
            } else {
                $this->_rule = unserialize($this->rule_data);
            }
        }
        if ($this->_rule->isBind()) {
            $this->_rule->bind($this);
        }

        return $this->_rule;
    }

    /**
     * @param mixed $rule
     * @throws \yii\base\InvalidConfigException
     */
    public function setRule($rule)
    {
        if (empty($rule)) {
            $this->_rule = null;
            $this->rule_name = '';
            $this->rule_data = '';

            return;
        }

        if (is_string($rule)) {
            $rule = (array)$rule;
        }
        if (is_array($rule)) {
            if ( ! isset($rule['class'])) {
                $rule['class'] = 'yiithings\setting\Rule';
            }
            $rule = Yii::createObject($rule);
        }
        if ( ! is_object($rule) || ! $rule instanceof Rule) {
            throw new InvalidArgumentException();
        }
        $this->_rule = $rule;
        $this->rule_name = get_class($rule);
        $this->rule_data = serialize($rule);
    }

    /**
     * @param ArrayObject $validators
     * @throws \yii\base\InvalidConfigException
     */
    public function appendRuleValidators($validators)
    {
        foreach ($this->getRule()->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0])) { // attributes, validator type
                $validator = Validator::createValidator($rule[0], $this, ['value'], array_slice($rule, 1));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }
    }

    public function getValidators()
    {
        $validators = parent::getValidators();
        if ($this->appendRules === false) {
            $this->appendRuleValidators($validators);
            $this->appendRules = true;
        }

        return $validators;
    }

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }
        parent::__set($name, $value);
    }

    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
            return;
        }
        parent::__unset($name);
    }

    protected function getDataAttribute($attribute)
    {
        switch ($this->type) {
            case 'bool':
            case 'boolean':
            case 'int':
            case 'integer':
            case 'float':
                $value = $this->$attribute;
                settype($value, $this->type);

                return $value;
            case 'string':
                return $this->$attribute;
            case 'json':
                return json_decode($this->$attribute, true);
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     */
    protected function setDataAttribute($attribute, $value)
    {
        if ( ! ($type = $this->type)) {
            $type = gettype($value);
        }
        switch ($type) {
            case 'bool':
            case 'boolean':
                $this->type = 'bool';
                $this->$attribute = $value ? '1' : '0';
                break;
            case 'int':
            case 'integer':
                $this->type = 'int';
                $this->$attribute = (int)$value;
                break;
            case 'float':
            case 'string':
                $this->$attribute = (string)$value;
                $this->type = gettype($value);
                break;
            case 'array':
            case 'json':
                $this->type = 'json';
                $this->$attribute = json_encode($value);
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }

    protected function validateRule()
    {
        return $this->getRule()->validate($this);
    }
}