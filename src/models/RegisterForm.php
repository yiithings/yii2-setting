<?php

namespace yiithings\setting\models;

use Yii;
use yii\base\Model;
use yiithings\setting\Definition;

class RegisterForm extends Model
{
    public $settingComponent = 'setting';

    public $name;

    public $group;

    public $description;

    public $value;

    public $defaultValue;

    public $definitionClass;

    public $definitionOptions;

    public $sortOrder;

    public $autoload;

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