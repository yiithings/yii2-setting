<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yiithings\setting\models\MultiSettingForm;
use yiithings\setting\widgets\ActiveForm;
use yiithings\setting\widgets\FlashAlert;

/** @var View $this */
/** @var MultiSettingForm $model */

$this->title = Yii::t('yiithings/setting', 'Settings');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => Url::current()];
?>
<?= FlashAlert::widget() ?>
<?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
<div class="page-title">
    <div class="title_left">
        <h3><?= $this->title ?></h3>
    </div>
    <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for...">
                <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><?= Yii::t('yiithings/setting', 'Settings') ?>
                    <small><?= Yii::t('yiithings/setting', 'All settings') ?></small>
                </h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="#">Settings 1</a>
                            </li>
                            <li><a href="#">Settings 2</a>
                            </li>
                        </ul>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <br>
                <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal form-label-left']]); ?>
                <?php foreach ($model->fields() as $field): ?>
                    <?php
                    /** @var \yiithings\setting\models\SettingForm $setting */
                    $setting = $model->getAttributeObject($field);
                    echo $setting->definition->render($model, $form, $field, [
                        'class'         => 'form-control col-md-7 col-xs-12',
                        'fieldTemplate' => "{label}\n<div class=\"col-md-6 col-sm-6 col-xs-12\">{input}</div>\n{hint}\n{error}",
                        'labelOptions'  => [
                            'class' => 'control-label col-md-3 col-sm-3 col-xs-12',
                        ],
                    ]);
                    ?>
                <?php endforeach ?>
                <div class="ln_solid"></div>
                <div class="form-group">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        <?= Html::resetButton(Yii::t('yiithings/setting', 'Reset'),
                            ['class' => 'btn btn-danger btn-flat']) ?>
                        <?= Html::submitButton(Yii::t('yiithings/setting', 'Save'),
                            ['class' => 'btn btn-primary btn-flat']) ?>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h2><i class="fa fa-bars"></i> <?= Yii::t('yiithings/setting', 'Managements Panel') ?>
                    <small><?= Yii::t('yiithings/setting', 'Manage settings') ?></small>
                </h2>
                <div class="clearfix"></div>
                <div class="x_content">
                    <p></p>
                    <a class="btn btn-app" href="<?= Url::to(['register/']) ?>">
                        <i class="fa fa-plus"></i> <?= Yii::t('yiithings/setting', 'Register') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
