<?php

namespace yiithings\setting\definitions;

use yiithings\setting\Definition;

class Select extends Definition
{
    public $multiple = false;

    public $items = [];

    public function validate()
    {
        if ( ! in_array($this->model->value, $this->items)) {
            $this->model->addError('value', 'Value is not in list limit');
        }

        return parent::validate();
    }

    public function beforeRender()
    {
        $this->getRenderer()->tag = 'radio';
        $this->getRenderer()->fieldCallback = function($renderModel, $form, $field) {
//            if ($this->tag == 'radio') {
                return $form->field($renderModel, $field)->radioList($this->items, $this->options);
//            }
        };
    }

}