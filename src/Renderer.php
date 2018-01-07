<?php

namespace yiithings\setting;

use yii\base\Component;
use yii\base\Model;
use yii\widgets\ActiveForm;
use yiithings\setting\models\SettingForm;

class Renderer extends Component
{
    /**
     * @var string
     */
    public $tag = 'input';
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $description;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var callable
     */
    public $fieldCallback;
    /**
     * @var Model
     */
    protected $renderModel;
    /**
     * @var \yii\widgets\ActiveForm
     */
    protected $form;
    /**
     * @var string
     */
    protected $field;

    /**
     * @param array $params
     */
    public function prepare($params)
    {
        if (count($params) == 2) {
            $this->form = $params[0];
            $this->field = $params[1];
        } elseif (count($params) == 3) {
            $this->renderModel = $params[0];
            $this->form = $params[1];
            $this->field = $params[2];
        }
    }

    /**
     * @param Model|SettingForm $model
     * @return bool|\yii\widgets\ActiveField
     */
    public function execute($model)
    {
        if ($this->renderModel === null) {
            $this->renderModel = $model;
        }

        return $this->renderForm($model, $this->form, $this->field);
    }

    /**
     * @param SettingForm $model
     * @param ActiveForm  $form
     * @param string      $field
     * @return bool|\yii\widgets\ActiveField
     */
    public function renderForm($model, $form, $field = null)
    {
        if ($field === null) {
            $field = $model->name;
        }
        $fieldObject = $form->field($this->renderModel, $field);
        if ($this->fieldCallback) {
            if (false === call_user_func($this->fieldCallback, $this->renderModel, $field)) {
                return false;
            }
        }

        if ($this->tag == 'textarea') {
            $fieldObject->textarea($this->options)->label($this->label);
        } elseif ($model->type == 'string') {
            $fieldObject->input('text', $this->options)->label($this->label);
        } elseif ($model->type == 'bool') {
            $fieldObject->checkbox([
                'label' => $this->label !== null ? $this->label : $field,
            ]);
        } else {
            return false;
        }

        return $fieldObject;
    }
}