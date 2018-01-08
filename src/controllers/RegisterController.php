<?php

namespace yiithings\setting\controllers;

use Yii;
use yii\web\Controller;
use yiithings\setting\models\RegisterForm;
use yiithings\setting\widgets\AlertMessage;

class RegisterController extends Controller
{
    public function actionIndex()
    {
        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->addFlash('alerts',
                AlertMessage::message(Yii::t('yiithings/setting', 'Settings has been saved!'), AlertMessage::SUCCESS));
            $this->redirect(['setting/index']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}