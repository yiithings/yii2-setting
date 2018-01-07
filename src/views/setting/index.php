<?php

use yii\helpers\Html;
use yii\web\View;
use yiithings\setting\models\MultiSettingForm;
use yiithings\setting\widgets\ActiveForm;

/** @var View $this */
/** @var MultiSettingForm $model */

$this->title = 'Settings';
?>

<?php $form = ActiveForm::begin([]); ?>
<?php foreach ($model->fields() as $field): ?>
    <?php
        /** @var \yiithings\setting\models\SettingForm $setting */
        $setting = $model->getAttributeObject($field);
        echo $setting->definition->render($model, $form, $field);
    ?>
<?php endforeach ?>
<?= Html::resetButton(Yii::t('yiithings/setting', 'Reset'), ['class' => 'btn btn-danger btn-flat']) ?>
<?= Html::submitButton(Yii::t('yiithings/setting', 'Save'), ['class' => 'btn btn-primary btn-flat']) ?>
<?php ActiveForm::end() ?>



