<?php

namespace yiithings\setting;

use Yii;
use yii\base\BootstrapInterface;
use yii\console\Application as ConsoleApplication;
use yii\web\Application as WebApplication;
use yiithings\setting\commands\SettingController;

class Module extends \yii\base\Module implements BootstrapInterface
{
    public $defaultRoute = 'setting';

    public $theme = 'bootstrap';

    public function bootstrap($app)
    {
        if ($app instanceof ConsoleApplication) {
            $app->controllerMap['setting'] = [
                'class' => SettingController::className(),
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
                'fileMap'        => [
                    'yiithings/setting' => 'setting.php',
                ],
            ];
        }
        if (Yii::$app instanceof WebApplication) {
            if ($this->theme && $this->theme !== 'bootstrap') {
                if ( ! Yii::$app->view->theme) {
                    Yii::$app->view->theme = Yii::createObject(['class' => 'yii\base\Theme']);
                }
                Yii::$app->view->theme->pathMap['@yiithings/setting/views'] = '@yiithings/setting/themes/' . $this->theme;
            }
        }
    }
}