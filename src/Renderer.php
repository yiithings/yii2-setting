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
     * @var array
     */
    public $labelOptions = [];
    /**
     * @var callable
     */
    public $fieldCallback;
    /**
     * @var string
     */
    public $fieldTemplate;
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
            list($this->form, $this->field) = $params;
        } elseif (count($params) == 3) {
            list($this->renderModel, $this->form, $this->field) = $params;
        } elseif (count($params) == 4) {
            list($this->renderModel, $this->form, $this->field, $options) = $params;
            if (isset($options['labelOptions'])) {
                $this->labelOptions = $options['labelOptions'];
                unset($options['labelOptions']);
            }
            if (isset($options['fieldTemplate'])) {
                $this->fieldTemplate = $options['fieldTemplate'];
                unset($options['fieldTemplate']);
            }
            $this->options = $options;
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
        if ($this->fieldTemplate) {
            $fieldObject->template = $this->fieldTemplate;
        }

        if ($this->tag == 'textarea') {
            $fieldObject->textarea($this->options)->label($this->label, $this->labelOptions);
        } elseif ($model->type == 'string') {
            $fieldObject->input('text', $this->options)->label($this->label, $this->labelOptions);
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