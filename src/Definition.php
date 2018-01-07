<?php

namespace yiithings\setting;

use ArrayObject;
use InvalidArgumentException;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class Definition
 *
 * @package yiithings\setting
 * @property Renderer $renderer
 */
class Definition extends Component implements DefinitionInterface
{
    const EVENT_BEFORE_RENDER = 'beforeRender';
    const EVENT_AFTER_RENDER = 'afterRender';
    /**
     * @var array
     */
    public $rules = [];
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var DefinableModel|Model|ActiveRecord
     */
    protected $model;
    /**
     * @var ArrayObject|Validator[]
     */
    private $_validators;
    /**
     * @var Renderer
     */
    private $_renderer;

    public static function bind(DefinableModel $model, DefinitionInterface $definition)
    {
        $definition->bindTo($model);
    }

    public function bindTo(DefinableModel $model)
    {
        $newThis = clone $this;
        $newThis->model = $model;
        $model->setDefinition($newThis);
    }

    public function rules()
    {
        return $this->rules;
    }

    public function validate()
    {
        foreach ($this->getValidators() as $validator) {
            $validator->validateAttributes($this->model, ['name', 'value']);
        }

        return ! $this->model->hasErrors();
    }

    public function render()
    {
        $this->getRenderer()->prepare(func_get_args());

        if (false === $this->beforeRender()) {
            return false;
        }
        $content = $this->getRenderer()->execute($this->model);
        $this->afterRender();

        return $content;
    }

    public function beforeRender()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_RENDER, $event);

        return $event->isValid;
    }

    public function afterRender()
    {
        $this->trigger(self::EVENT_AFTER_RENDER);
    }

    /**
     * @return Renderer
     * @throws InvalidConfigException
     */
    public function getRenderer()
    {
        if ($this->_renderer === null) {
            $this->_renderer = Yii::createObject([
                    'class' => Renderer::className(),
                ] + $this->options);
        }

        return $this->_renderer;
    }

    /**
     * @param mixed $renderer
     * @throws InvalidConfigException
     */
    public function setRenderer($renderer)
    {
        if ( ! is_object($renderer) || $renderer instanceof Renderer) {
            if (is_string($renderer) || is_array($renderer)) {
                $renderer = (array)$renderer;
                $renderer = $renderer + ['class' => Renderer::className()] + $this->options;
            }
            $renderer = Yii::createObject($renderer);
        }
        $this->_renderer = $renderer;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getAttribute($name)
    {
        $type = $this->model->getAttribute('type');
        switch ($type) {
            case 'bool':
            case 'boolean':
            case 'int':
            case 'integer':
            case 'float':
                $value = $this->model->getAttribute($name);
                settype($value, $type);

                return $value;
            case 'string':
                return $this->model->getAttribute($name);
            case 'json':
                return json_decode($this->model->getAttribute($name), true);
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value)
    {
        if ( ! ($type = $this->model->getAttribute('type'))) {
            $type = gettype($value);
        }
        switch ($type) {
            case 'bool':
            case 'boolean':
                $this->model->setAttribute('type', 'bool');
                $this->model->setAttribute($name, $value ? '1' : '0');
                break;
            case 'int':
            case 'integer':
                $this->model->setAttribute('type', 'int');
                $this->model->setAttribute($name, (int)$value);
                break;
            case 'float':
            case 'string':
                $this->model->setAttribute('type', gettype($value));
                $this->model->setAttribute($name, (string)$value);
                break;
            case 'array':
            case 'json':
                $this->model->setAttribute('type', 'json');
                $this->model->setAttribute($name, json_encode($value));
                break;
            default:
                throw new InvalidArgumentException("Undefined value type");
        }
    }


    /**
     * @return ArrayObject|Validator[]
     * @throws InvalidConfigException
     */
    public function getValidators()
    {
        if ($this->_validators === null) {
            $this->_validators = $this->createValidators();
        }

        return $this->_validators;
    }

    /**
     * @return ArrayObject
     * @throws InvalidConfigException
     */
    public function createValidators()
    {
        $validators = new ArrayObject();
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0])) { // attributes, validator type
                $validator = Validator::createValidator($rule[0], $this->model, ['value'], array_slice($rule, 1));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), ['model', '_validators', '_renderer']);
    }
}