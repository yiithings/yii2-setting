<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');


$config = \yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../app/config/main.php'),
    require(__DIR__ . '/../../app/config/web.php')
);

Yii::setAlias('@yiithings/setting', dirname(dirname(dirname(__DIR__))) . '/src');
Yii::setAlias('@tests', dirname(dirname(__DIR__)));

Yii::setAlias('@vendor', dirname(dirname(dirname(dirname(__DIR__)))). '/vendor');
Yii::setAlias('@bower', '@vendor/bower-asset');

(new yii\web\Application($config))->run();
