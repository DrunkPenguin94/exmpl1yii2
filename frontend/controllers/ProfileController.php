<?php

namespace frontend\controllers;

use frontend\models\StatusOnline;
use yii\filters\AccessControl;
class ProfileController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [

                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [

                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }
    public function beforeAction($action)
    {
        $modelStatus=new StatusOnline;
        $modelStatus->updateDate();
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        if(false)
            return $this->render('customer');
        else
            return $this->render('retoucher');
    }




}
