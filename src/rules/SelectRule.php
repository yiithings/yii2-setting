<?php

namespace yiithings\setting\rules;

use yiithings\setting\Rule;

class SelectRule extends Rule
{
    public $multiple = false;

    public $items = [];

    public function validate($model)
    {
        if ( ! in_array($model->value, $this->items)) {
            return false;
        }

        return parent::validate($model);
    }

    /**
     * @param \yii\widgets\ActiveForm $form
     * @param null                    $field
     * @param null                    $model
     * @return $this|bool|\yii\widgets\ActiveField
     */
    public function renderForm($form, $field = null, $model = null)
    {
        if (false !== ($content = parent::renderForm($form, $field, $model))) {
            return $content;
        }

        if ($this->tag == 'radio') {
            return $form->field($model, $field)->radioList($this->items, $this->options);
        }

        return false;
    }
}