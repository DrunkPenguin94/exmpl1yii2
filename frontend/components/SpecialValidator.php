<?php
namespace frontend\components;

use yii\validators\Validator;
use common\models\Order;
use yii;
class SpecialValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = 'Выберите исполнителя для заказа';
    }

    public function validateAttribute($model,$attribute) {

    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message =$this->message;
        return <<<JS
if($('#createorder-special').prop("checked") && $('#createorder-idspecialuser').val()==""){
    messages.push('$message');
}
JS;
    }
}