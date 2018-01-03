<?php

namespace panlatent\setting\models;

use InvalidArgumentException;
use panlatent\setting\Rule;

/**
 * This is the model class for table "setting".
 *
 * @property integer  $id
 * @property string   $name
 * @property string   $type
 * @property string   $data
 * @property string   $group
 * @property string   $default_data
 * @property string   $rule_name
 * @property resource $rule_data
 * @property integer  $sort_order
 * @property integer  $autoload
 * @property integer  $created_at
 * @property integer  $updated_at
 * @property mixed    $value
 * @property mixed    $defaultValue
 * @property Rule     $rule
 */
class Setting extends \yii\db\ActiveRecord
{
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['data', 'default_data', 'rule_data'], 'string'],
            [['sort_order', 'autoload', 'created_at', 'updated_at'], 'integer'],
            [['name', 'rule_name'], 'string', 'max' => 255],
            [['type', 'group'], 'string', 'max' => 32],
            [
                ['name', 'group'],
                'unique',
                'targetAttribute' => ['name', 'group'],
                'message'         => 'The combination of Name and Group has already been taken.',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'           => 'ID',
            'name'         => 'Name',
            'type'         => 'Type',
            'data'         => 'Data',
            'group'        => 'Group',
            'default_data' => 'Default Data',
            'rule_name'    => 'Rule Name',
            'rule_data'    => 'Rule Data',
            'sort_order'   => 'Sort Order',
            'autoload'     => 'Autoload',
            'created_at'   => 'Created At',
            'updated_at'   => 'Updated At',
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return parent::validate($attributeNames, $clearErrors) && $this->validateRule();
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
     * @return Rule|false
     */
    public function getRule()
    {
        if ($this->_rule === null) {
            if (empty($this->rule_name)) {
                return false;
            }
            $this->_rule = unserialize($this->rule_data);
        }

        return $this->_rule;
    }

    /**
     * @param mixed $rule
     */
    public function setRule($rule)
    {
        if (empty($rule)) {
            $this->_rule = null;
            $this->rule_name = '';
            $this->rule_data = '';

            return;
        }

        if ( ! $rule instanceof Rule) {
            throw new InvalidArgumentException();
        }
        $this->_rule = $rule;
        $this->rule_name = get_class($rule);
        $this->rule_data = serialize($rule);
    }


    protected function getDataAttribute($name)
    {
        switch ($this->type) {
            case 'boolean':
            case 'integer':
            case 'float':
                $value = $this->$name;
                settype($value, $this->type);
                return $value;
            case 'string':
                return $this->$name;
            case 'json':
                return json_decode($this->$name, true);
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }

    protected function setDataAttribute($name, $value)
    {
        switch (gettype($value)) {
            case 'boolean':
            case 'integer':
            case 'double':
            case 'string':
                $this->type = gettype($value);
                $this->$name = (string)$value;
                break;
            case 'array':
                $this->type = 'json';
                $this->$name = json_encode($value);
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }

    protected function validateRule()
    {
        if (false === ($rule = $this->getRule())) {
            return true;
        }

        return $rule->validate($this);
    }
}
