<?php

namespace yiithings\setting;

use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yiithings\setting\commands\SettingController;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $defaultRoute = 'setting';

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
        if ( ! isset(Yii::$app->i18n->translations['yiithings/setting'])) {
            Yii::$app->i18n->translations['yiithings/setting'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en',
                'basePath'       => '@yiithings/setting/messages',
                'fileMap' => [
                    'yiithings/setting' => 'setting.php',
                ]
            ];
        }
    }
}