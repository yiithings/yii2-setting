<?php

namespace yiithings\setting\controllers;

use Yii;
use yii\web\Controller;
use yiithings\setting\models\MultiSettingForm;
use yiithings\setting\models\SettingForm;

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

        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    protected function find()
    {

    }
}