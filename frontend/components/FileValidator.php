<?php
namespace frontend\components;

use yii\validators\Validator;
use common\models\Order;
use yii;
class FileValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = 'Вы можете загружать не больше 4 файлов';
    }

    public function validateAttribute($model,$attribute) {
        Yii::trace($model->filesAttached);
        $arr=explode(";",$model->filesAttached);
        $arr= array_filter($arr, function($element) {
            return !empty($element);
        });
        $countDelFile=count($arr);
        $modelFilesAttached=$model->getFileAttached(1);
        $countNowFile=count($modelFilesAttached);


        Yii::trace($arr);
        $message =$this->message;
    }
/*
    public function clientValidateAttribute($model, $attribute, $view)
    {
        Yii::trace($model->id);
        Yii::trace($model->filesAttached);
        $arr=explode(";",$model->filesAttached);
        Yii::trace($arr);
        $message =$this->message;
        return <<<JS

JS;
    }

*/
}