<?php

namespace yiithings\setting\controllers;

use Yii;
use yii\web\Controller;
use yiithings\setting\models\MultiSettingForm;
use yiithings\setting\models\SettingForm;
use yiithings\setting\widgets\AlertMessage;

/**
 * Manages application settings.
 *
 * @package panlatent\setting\controllers
 */
class SettingController extends Controller
{
    public function actionIndex()
    {
        $model = new MultiSettingForm();
        $model->settings = SettingForm::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('alerts',
                AlertMessage::message(Yii::t('yiithings/setting', 'Settings has been saved!'), AlertMessage::SUCCESS));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}