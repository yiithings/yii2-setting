<?php

namespace yiithings\setting\commands;

use InvalidArgumentException;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;
use yiithings\setting\Definition;
use yiithings\setting\Setting;

/**
 * Manages application settings.
 *
 * @package panlatent\setting\controllers
 * @property Setting $setting
 */
class SettingController extends Controller
{
    /**
     * @var string setting component id.
     */
    public $settingComponent = 'setting';
    /**
     * @var string definition class name.
     */
    public $class = '';
    /**
     * @var string|array set definition rules from json input.
     */
    public $rules = [];
    /**
     * @var string|array set definition options from json input.
     */
    public $options = [];
    /**
     * @var string definition label name.
     */
    public $label;
    /**
     * @var string definition tag name.
     */
    public $tag;

    public function options($actionID)
    {
        switch ($actionID) {
            case 'set':
                $options = ['class', 'rules', 'options', 'label', 'tag'];
                break;
            default:
                $options = [];
        }

        return array_merge(parent::options($actionID), [
            'settingComponent',
        ], $options);
    }

    /**
     * List all settings.
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAll()
    {
        $all = $this->setting->all();
        if (empty($all)) {
            $this->stdout("(empty)\n");

            return;
        }

        foreach ($all as $group => $values) {
            foreach ($values as $name => $value) {
                $type = gettype($value);
                $this->stdout("- ($type)$value\n");
            }
        }
    }

    /**
     * Get a setting value
     *
     * @param string $name
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGet($name)
    {
        $value = $this->setting->get($name);
        $type = gettype($value);
        $this->stdout("Value: ($type)$value\n");
    }

    /**
     * Set a setting.
     *
     * @param string $name
     * @param string $value value string use [type]:value set type.
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSet($name, $value)
    {
        $append = ! empty($this->append) ? json_decode($this->append, true) : [];
        if ( ! is_array($append)) {
            throw new InvalidArgumentException("Cannot parser append json: {$this->append}");
        }
        if ( ! empty($this->class) || ! empty($this->rules) || ! empty($this->options)) {
            if (empty($this->class)) {
                $this->class = Definition::className();
            } elseif ($this->class[0] == '@'){
                $alias = 'yiithings\\setting\\definitions\\' . Inflector::classify(substr($this->class, 1));
                if ( ! class_exists($alias)) {
                    $this->stderr("Not found definition class alias: {$this->class}!\n", Console::FG_RED);
                    return;
                }
                $this->class = $alias;
            }
            if ( ! class_exists($this->class)) {
                $this->stderr("Not found definition class!\n", Console::FG_RED);
                return;
            }

            $definition = Yii::createObject([
                'class'   => $this->class,
                'rules'   => is_array($this->rules) ? $this->rules : json_decode($this->rules),
                'options' => is_array($this->options) ? $this->options : json_decode($this->options),
            ]);
        } else {
            $definition = null;
        }
        $success = $this->setting->set($name, $this->prepareValueParam($value), null, $definition);
        if ($success) {
            $this->stdout("Setting updated!\n", Console::FG_GREEN);
        } else {
            $this->stderr("Setting failed!\n", Console::FG_RED);
            foreach ($this->setting->lastErrors as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->stderr(" - $attribute: $error\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Remove a setting.
     *
     * @param string $name
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemove($name)
    {
        $success = $this->setting->remove($name);
        if ($success) {
            $this->stdout("Setting removed!\n", Console::FG_GREEN);
        } else {
            $this->stderr("Removed failed!\n", Console::FG_RED);
        }
    }

    /**
     * @return Setting
     */
    public function getSetting()
    {
        return Yii::$app->{$this->settingComponent};
    }

    /**
     * @param string $value
     * @return bool|mixed|string
     */
    private function prepareValueParam($value)
    {
        if (false === ($pos = strpos($value, ':'))) {
            return $value;
        }
        $type = substr($value, 0, $pos);
        $value = substr($value, $pos + 1);
        if ($type == 'string') {
            return $value;
        }

        switch ($type) {
            case 'bool':
            case 'boolean':
            case 'int':
            case 'integer':
            case 'float':
                settype($value, $type);

                return $value;
            case 'json':
                return json_decode($value, true);
                break;
        }
        throw new InvalidArgumentException("Undefined value type");
    }
}