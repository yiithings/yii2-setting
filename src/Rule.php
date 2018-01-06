<?php


namespace yiithings\setting;

use yii\base\Component;
use yii\db\ActiveRecord;
use yii\widgets\ActiveForm;
use yiithings\setting\models\SettingForm;

/**
 * Class Rule
 *
 * @package yiithings\setting
 * @property array $rules
 */
class Rule extends Component
{
    /**
     * @var array
     */
    public $rules = [];
    /**
     * @var string
     */
    public $label;
    /**
     * @var string
     */
    public $tag;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var ActiveRecord|SettingForm
     */
    protected $model;

    public function bind($model)
    {
        $this->model = $model;
    }

    public function isBind()
    {
        return $this->model === null;
    }

    /**
     * @param SettingForm $model
     * @return bool
     */
    public function validate($model)
    {
        return true;
    }

    public function rules()
    {
        return $this->rules;
    }

    /**
     * @param SettingForm $setting
     * @param array       $params
     */
    public function render($setting, $params = [])
    {
        
    }

    /**
     * @param ActiveForm  $form
     * @param string      $field
     * @param SettingForm $model
     * @return bool|\yii\widgets\ActiveField
     */
    public function renderForm($form, $field = null, $model = null)
    {
        if ($model === null) {
            $model = $this->model;
        }
        if ($field === null) {
            $field = $model->name;
        }

        if ($this->tag == 'textarea') {
            return $form->field($model, $field)->textarea($this->options)->label($this->label);
        } elseif ($this->model->type == 'string') {
            return $form->field($model, $field)->input('text', $this->options)->label($this->label);
        } elseif ($this->model->type == 'bool') {
            return $form->field($model, $field)->checkbox([
                'label' => $this->label !== null ? $this->label : $field,
            ]);
        }

        return false;
    }

    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['_setting']);
    }
}