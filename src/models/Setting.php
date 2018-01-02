<?php

namespace panlatent\setting\models;

use Yii;

/**
 * This is the model class for table "setting".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property resource $rule
 * @property string $type
 * @property string $value
 * @property string $default_value
 * @property integer $sort_order
 * @property integer $autoload
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Setting $parent
 * @property Setting $setting
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
            [['parent_id', 'sort_order', 'autoload', 'created_at', 'updated_at'], 'integer'],
            [['rule', 'value', 'default_value'], 'string'],
            [['type'], 'required'],
            [['name', 'type'], 'string', 'max' => 32],
            [['parent_id'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Setting::className(), 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Name',
            'rule' => 'Rule',
            'type' => 'Type',
            'value' => 'Value',
            'default_value' => 'Default Value',
            'sort_order' => 'Sort Order',
            'autoload' => 'Autoload',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Setting::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetting()
    {
        return $this->hasOne(Setting::className(), ['parent_id' => 'id']);
    }

    public function getRule()
    {
        if (empty($this->rule)) {
            return false;
        }

        $rule = unserialize($this->rule);
    }

    public function setRule($rule)
    {

    }
}
