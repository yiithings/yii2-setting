<?php

namespace yiithings\setting\definitions;

use yiithings\setting\Definition;

class Range extends Definition
{
    /**
     * @var int|double|null
     */
    public $max;
    /**
     * @var int|double|null
     */
    public $min;

    public function validate()
    {
        if ( ! is_numeric($this->model->value)) {
            $this->model->addError('value', 'Value is not a numeric type');
        }
        if ($this->max !== null && $this->max < $this->model->value) {
            $this->model->addError('value', "Value cannot be greater than $this->max");
        }
        if ($this->min !== null && $this->min > $this->model->value) {
            $this->model->addError('value', "Value cannot be smaller than $this->min");
        }

        return parent::validate();
    }
}