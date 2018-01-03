<?php

namespace yiithings\setting;

use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yiithings\setting\commands\SettingController;

class Module extends \yii\base\Module implements BootstrapInterface
{

    public $setting = 'setting';

    public function bootstrap($app)
    {
        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['setting'] = [
                'class' => SettingController::className()
            ];
        }
    }

    public function init()
    {
        parent::init();
    }
}