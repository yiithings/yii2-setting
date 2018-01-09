<?php

namespace yiithings\setting\models;

use Yii;
use yii\base\Model;
use yiithings\setting\Definition;

class RegisterForm extends Model
{
    /**
     * @var string
     */
    public $settingComponent = 'setting';
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $group;
    /**
     * @var string
     */
    public $description;
    /**
     * @var mixed
     */
    public $value;
    /**
     * @var mixed
     */
    public $defaultValue;
    /**
     * @var string
     */
    public $definitionClass;
    /**
     * @var array|string
     */
    public $definitionOptions;
    /**
     * @var int
     */
    public $sortOrder;
    /**
     * @var bool
     */
    public $autoload;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['value', 'defaultValue',], 'string'],
            [['sortOrder', 'autoload'], 'integer'],
            [['name', 'description', 'definitionClass'], 'string', 'max' => 255],
            [['group', 'type'], 'string', 'max' => 32],
            [['name'], 'required'],
            [['name', 'group'], 'match', 'pattern' => '#^[a-zA-Z_][a-zA-Z0-9_]*$#'],
            [['definitionOptions'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name'              => Yii::t('yiithings/setting', 'Name'),
            'group'             => Yii::t('yiithings/setting', 'Group'),
            'value'             => Yii::t('yiithings/setting', 'Value'),
            'defaultValue'      => Yii::t('yiithings/setting', 'Default Value'),
            'description'       => Yii::t('yiithings/setting', 'Description'),
            'definitionClass'   => Yii::t('yiithings/setting', 'Definition Class'),
            'definitionOptions' => Yii::t('yiithings/setting', 'Definition Options'),
            'sortOrder'         => Yii::t('yiithings/setting', 'Sort Order'),
            'autoload'          => Yii::t('yiithings/setting', 'Autoload'),
        ];
    }

    /**
     * Save fields to setting.
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function save()
    {
        if ( ! empty($this->definitionOptions)) {
            $definitionOption = json_decode($this->definitionOptions, true);
        } else {
            $definitionOption = [];
        }
        if (empty($this->definitionClass)) {
            $this->definitionClass = Definition::className();
        }
        /** @var \yiithings\setting\Setting $setting */
        $setting = Yii::$app->{$this->settingComponent};
        $definition = $definitionOption + ['class' => $this->definitionClass,];

        return $setting->add($this->name, $this->value, $this->group, $this->defaultValue, $definition,
            $this->sortOrder, $this->autoload);
    }
}