<?php

namespace panlatent\setting;

use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $setting = 'setting';

    public function bootstrap($app)
    {
        if ($app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'panlatent\setting\console';
        }
    }

    public function init()
    {

    }
}