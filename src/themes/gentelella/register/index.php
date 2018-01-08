<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yiithings\setting\models\RegisterForm;
use yiithings\setting\widgets\ActiveForm;
use yiithings\setting\widgets\FlashAlert;

/** @var View $this */
/** @var RegisterForm $model */

$this->title = Yii::t('yiithings/setting', 'Register');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('yiithings/setting', 'Settings'),
    'url'   => Url::to(['setting/index']),
];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= FlashAlert::widget() ?>
<?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]) ?>
<h3><?= $this->title ?></h3>
<?php $form = ActiveForm::begin([]); ?>
<?= $form->field($model, 'name')->textInput() ?>
<?= $form->field($model, 'group')->textInput() ?>
<?= $form->field($model, 'value')->textInput() ?>
<?= $form->field($model, 'defaultValue')->textInput() ?>
<?= $form->field($model, 'description')->textarea() ?>
<?= $form->field($model, 'definitionClass')->textInput() ?>
<?= $form->field($model, 'definitionOptions')->textarea() ?>
<?= $form->field($model, 'sortOrder')->textInput() ?>
<?= $form->field($model, 'autoload')->checkbox(); ?>
<?= Html::submitButton(Yii::t('yiithings/setting', 'Save'), ['class' => 'btn btn-primary btn-flat']) ?>
<?php ActiveForm::end() ?>

