<?php

namespace yiithings\setting\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property int $id
 * @property string $name
 * @property string $group
 * @property string $type
 * @property string $description
 * @property string $value
 * @property string $default_value
 * @property string $definition_name
 * @property resource $definition_data
 * @property int $sort_order
 * @property int $autoload
 * @property int $created_at
 * @property int $updated_at
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value', 'default_value', 'definition_data'], 'string'],
            [['sort_order', 'autoload', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description', 'definition_name'], 'string', 'max' => 255],
            [['group', 'type'], 'string', 'max' => 32],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yiithings/setting', 'ID'),
            'name' => Yii::t('yiithings/setting', 'Name'),
            'group' => Yii::t('yiithings/setting', 'Group'),
            'type' => Yii::t('yiithings/setting', 'Type'),
            'description' => Yii::t('yiithings/setting', 'Description'),
            'value' => Yii::t('yiithings/setting', 'Value'),
            'default_value' => Yii::t('yiithings/setting', 'Default Value'),
            'definition_name' => Yii::t('yiithings/setting', 'Definition Name'),
            'definition_data' => Yii::t('yiithings/setting', 'Definition Data'),
            'sort_order' => Yii::t('yiithings/setting', 'Sort Order'),
            'autoload' => Yii::t('yiithings/setting', 'Autoload'),
            'created_at' => Yii::t('yiithings/setting', 'Created At'),
            'updated_at' => Yii::t('yiithings/setting', 'Updated At'),
        ];
    }
}
