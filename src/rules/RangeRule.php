<?php

namespace yiithings\setting\rules;

use yiithings\setting\Rule;

class RangeRule extends Rule
{
    /**
     * @var int|double|null
     */
    public $max;
    /**
     * @var int|double|null
     */
    public $min;

    /**
     * @param \yiithings\setting\models\SettingForm $model
     * @return bool
     */
    public function validate($model)
    {
        if ( ! is_numeric($model->value)) {
            $model->addError('value', 'Value is not a numeric type');
            return false;
        } elseif ($this->max !== null && $this->max < $model->value) {
            $model->addError('value', "Value cannot be greater than $this->max");
            return false;
        } elseif ($this->min !== null && $this->min > $model->value) {
            $model->addError('value', "Value cannot be smaller than $this->min");
            return false;
        }

        return parent::validate($model);
    }
}