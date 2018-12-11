<?php


namespace frontend\controllers;

use common\models\DocumentPages;
use frontend\models\StatusOnline;
use yii\filters\AccessControl;
class DocumentController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [

        ];
    }
    public function beforeAction($action)
    {
        $modelStatus=new StatusOnline;
        $modelStatus->updateDate();
        return parent::beforeAction($action);
    }

    public function actionPrivacyPolicy()
    {
            $model=DocumentPages::findOne(["id"=>"2"]);

            return $this->render('index',["model"=>$model]);
    }
    public function actionUserAgreement()
    {
        $model=DocumentPages::findOne(["id"=>"3"]);

        return $this->render('index',["model"=>$model]);
    }
    public function actionSiteRules()
    {
        $model=DocumentPages::findOne(["id"=>"4"]);

        return $this->render('index',["model"=>$model]);
    }
    public function actionPersonalInformation()
    {
        $model=DocumentPages::findOne(["id"=>"5"]);

        return $this->render('index',["model"=>$model]);
    }




}
