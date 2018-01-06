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
 * @property string $data
 * @property string $default_data
 * @property string $rule_name
 * @property resource $rule_data
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
            [['data', 'default_data', 'rule_data'], 'string'],
            [['sort_order', 'autoload', 'created_at', 'updated_at'], 'integer'],
            [['name', 'rule_name'], 'string', 'max' => 255],
            [['group', 'type'], 'string', 'max' => 32],
            [['name', 'group'], 'unique', 'targetAttribute' => ['name', 'group']],
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
            'data' => Yii::t('yiithings/setting', 'Data'),
            'default_data' => Yii::t('yiithings/setting', 'Default Data'),
            'rule_name' => Yii::t('yiithings/setting', 'Rule Name'),
            'rule_data' => Yii::t('yiithings/setting', 'Rule Data'),
            'sort_order' => Yii::t('yiithings/setting', 'Sort Order'),
            'autoload' => Yii::t('yiithings/setting', 'Autoload'),
            'created_at' => Yii::t('yiithings/setting', 'Created At'),
            'updated_at' => Yii::t('yiithings/setting', 'Updated At'),
        ];
    }
}
