Yii2 Setting
============
[![Build Status](https://travis-ci.org/yiithings/yii2-setting.svg)](https://travis-ci.org/yiithings/yii2-setting)
[![Latest Stable Version](https://poser.pugx.org/yiithings/yii2-setting/v/stable.svg)](https://packagist.org/packages/yiithings/yii2-setting) 
[![Total Downloads](https://poser.pugx.org/yiithings/yii2-setting/downloads.svg)](https://packagist.org/packages/yiithings/yii2-setting) 
[![Latest Unstable Version](https://poser.pugx.org/yiithings/yii2-setting/v/unstable.svg)](https://packagist.org/packages/yiithings/yii2-setting)
[![License](https://poser.pugx.org/yiithings/yii2-setting/license.svg)](https://packagist.org/packages/yiithings/yii2-setting)

It helps you to dynamically construct, validate, and display setting variables in the Yii2 framework.

Supports `Bootstrap`(default), `AdminLTE` and `Gentelella Alela!`.

What's This
-----------
Yii2 Setting 是一个通用型的 `设置` 拓展，可以为 Yii2 应用提供开箱即用的定制、操作和显示配置项的功能。它可以帮助你动态的构造配置项，
并将设置的验证规则，数据处理方式等拓展信息保存在数据库。它的配置页面预置支持 `Bootstrap`、, `AdminLTE` 和 `Gentelella Alela!` ，
同时提供了命令行工具和 Gii 生成工具。

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiithings/yii2-setting "*"
```

or add

```
"yiithings/yii2-setting": "*"
```

to the require section of your `composer.json` file.


Usage
-----

本拓展分为包含 `yiithings\setting\Setting` 组件和 `yiithings\setting\Module` 模块，`yiithings\setting\Setting` 组件是必须配置
的，是对配置项进行 CRUD 的统一入口。`yiithings\setting\Module` 模块提供了 Web 端与控制台功能，会设置一些所需属性，是可选的。通常，
我们将组件 ID 设置为 `setting`，将模块 ID 设置为 `settings`。

Once the extension is installed, simply use it in your code by  :
```php
'components' => [、
    'setting' => [
        'class' => 'yiithings\setting\Setting',
    ]
],
'modules' => [
    'settings' => [
        'class' => 'yiithings\setting\Module',
    ]
],
```

 > 组件 API 参见 [Setting Class](src/Setting.php)

模块页面对 `AdminLTE` 和 `Gentelella Alela!` 两种主题提供了主题化定制（默认为`Bootstrap`）。如果你的应用使用了这两种主题重的一个，
可以通过配置 `theme` 属性来设置主题。
```php
'modules' => [
    'settings' => [
        'class' => 'yiithings\setting\Module',
        'theme' => 'gentelella',
    ]
],
```

模块使用了 I18N 组件，并且预置了一些语言消息的翻译，以示图提供适用更多场景。如果预置翻译没有你所需要的语言或不能满足要求，可自行添加翻译，
拓展使用了 `yiithings/setting` 作为消息分类名。
```php
[
    'i18n' => [
        'translations' => [
            'yiithings/setting' => [
               'class'          => 'yii\i18n\PhpMessageSource',
               'sourceLanguage' => 'en',
               'basePath'       => 'YOU_PATH',
               'fileMap'        => [
                   'yiithings/setting' => 'YOU_PATH',
               ],
           ]
        ]
    ]
]
```