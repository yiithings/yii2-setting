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
            case 'add':
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
     * Get a setting value.
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
     * Register a setting.
     *
     * @param string $name
     * @param $value
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdd($name, $value)
    {
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

        list($name, $group) = $this->prepareNameParam($name);
        list($value, $defaultValue) = $this->prepareValueParam($value);
        $success = $this->setting->add($name, $value, $group,  $defaultValue, $definition);
        if ($success) {
            $this->stdout("Setting added!\n", Console::FG_GREEN);
        } else {
            $this->stderr("Setting add failed!\n", Console::FG_RED);
            foreach ($this->setting->lastErrors as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->stderr(" - $attribute: $error\n", Console::FG_RED);
                }
            }
        }
    }

    /**
     * Set a setting or use default definition class register.
     *
     * @param string $name
     * @param string $value value string use [type]:value set type.
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSet($name, $value)
    {
        list($name, $group) = $this->prepareNameParam($name);
        list($value, ) = $this->prepareValueParam($value);
        $success = $this->setting->set($name, $value, $group);
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
     * Returns a array with setting name and group by command param.
     *
     * e.g. site.language to ['site', 'language'], site_name to ['site_name', '']
     *
     * @param string $name
     * @return array
     */
    private function prepareNameParam($name)
    {
        if (false === ($pos = strpos($name, '.'))) {
            return [$name, ''];
        }

        return [substr($name, $pos + 1), substr($name, 0, $pos)];
    }

    /**
     * Returns a array with setting value and default value by command param.
     *
     * e.g. 1000:1 to ['1000', '1'], 1000:1:int to [1000, 1].
     *
     * @param string $value
     * @return bool|mixed|string
     */
    private function prepareValueParam($value)
    {
        if (false === ($pos = strpos($value, ':'))) {
            return [$value, null];
        }
        $defaultValue = substr($value, $pos + 1);
        $value = substr($value, 0, $pos);
        if (false === ($pos = strrpos($defaultValue, ':'))) {
            return [$value, $defaultValue];
        }
        $type = substr($defaultValue, $pos + 1);
        $defaultValue = substr($defaultValue, 0, $pos);

        return [$value, $this->prepareValue($type, $defaultValue)];
    }

    /**
     * Set a value type by type param.
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    private function prepareValue($type, $value)
    {
        if ($type === null || $type == 'string') {
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