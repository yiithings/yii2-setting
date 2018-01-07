<?php

namespace yiithings\setting\models;

use yii\base\Model;
use yii\db\ActiveRecord;
use yiithings\setting\MultiInOneModel;

/**
 * Class MultiSettingForm
 *
 * @package yiithings\setting\models
 * @property SettingForm[] settings
 */
class MultiSettingForm extends Model
{
    use MultiInOneModel;

    /**
     * @param SettingForm[] $settings
     */
    public function setSettings($settings)
    {
        $keys = [];
        foreach ($settings as $model) {
            $keys[] = $model->getFullName();
        }
        $this->setAttributeObjects(array_combine($keys, $settings));
    }

    /**
     * @return bool
     */
    public function save()
    {
        $success = true;
        foreach ($this->_attributes as $name => $model) {
            /** @var ActiveRecord $model */
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
     * @return mixed
     */
    protected function getAttributeInternal($name)
    {
        return $this->_attributes[$name]->value;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setAttributeInternal($name, $value)
    {
        $this->_attributes[$name]->value = $value;
    }
}