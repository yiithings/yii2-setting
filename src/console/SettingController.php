<?php

namespace yiithings\setting\console;

use yiithings\setting\Setting;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Manages application settings.
 *
 * @package panlatent\setting\controllers
 * @property Setting $setting
 */
class SettingController extends Controller
{
    public $settingComponent = 'setting';

    public function options($actionID)
    {
        switch ($actionID) {
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
    public function actionList()
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
     * @param string $value
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSet($name, $value)
    {
        $success = $this->setting->set($name, $this->prepareValueParam($value));
        if ($success) {
            $this->stdout("Setting updated!\n", Console::FG_GREEN);
        } else {
            $this->stderr("Setting failed!\n", Console::FG_RED);
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
        if ($type === 'json') {
            return json_decode($value, true);
        }
        settype($value, $type);

        return $value;
    }
}