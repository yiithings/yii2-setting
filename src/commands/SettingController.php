<?php

namespace yiithings\setting\commands;

use InvalidArgumentException;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
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
     * @var bool whether to set setting with rule.
     */
    public $withRule = false;
    /**
     * @var string rule class name.
     */
    public $class = 'yiithings\setting\Rule';
    /**
     * @var string rule label name.
     */
    public $label;
    /**
     * @var string rule tag name.
     */
    public $tag;
    /**
     * @var array rule option attribute.
     */
    public $options = [];
    /**
     * @var string read json and append properties to rule.
     */
    public $append;

    public function options($actionID)
    {
        switch ($actionID) {
            case 'set':
                $options = ['withRule', 'class', 'label', 'tag', 'options', 'append'];
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
        if ($this->withRule) {
            $rule = Yii::createObject([
                'class'   => $this->class,
                'label'   => $this->label,
                'tag'     => $this->tag,
                'options' => $this->options,
            ] + $append);
        } else {
            $rule = null;
        }
        $success = $this->setting->set($name, $this->prepareValueParam($value), null, $rule);
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