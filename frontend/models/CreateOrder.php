<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Order;
use common\models\PerformerOrder;
use common\models\Notification;
/**
 * ContactForm is the model behind the contact form.
 */
class CreateOrder extends Order
{

	public $conditionSafeCrow;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
                parent::rules(),
                [
					["conditionSafeCrow","safe"],
                    ['date_deadline', function ($attribute, $params) {
                        $dateNow=strtotime(date("Y-m-d H:i"));


                        $dateDeadLine=strtotime($this->$attribute);

                        if($dateDeadLine-$dateNow>3600*24*7+3600)
                            $this->addError($attribute, 'Срок выполнения заказа не может быть больше 7 дней от текущей даты');

                        if($dateDeadLine<$dateNow)
                            $this->addError($attribute, 'Срок выполнения заказа не может быть прошедшей датой');

                        if($dateDeadLine<$dateNow)
                            $this->addError($attribute, 'Срок выполнения заказа не может быть прошедшей датой');
                    }],
                    [['date_deadline'], 'datetime','format'=>'php:Y-m-d H:i'],
                    [['name_text'], function ($attribute, $params) {
                        if (strlen($this->$attribute) < 8) {

                            $this->addError($attribute, 'Название заказа не может быть меньше 8 символов.');

                        }
                    }],
					
					[['conditionSafeCrow'], function ($attribute, $params) {
                        if ($this->$attribute!=1) {

                            $this->addError($attribute, 'Подтвердите свое согласие с условиями SafeCrow');

                        }
                    }],


                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            []
        );
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     *
     * @param string $email the target email address
     * @return bool whether the email was sent
     */
    public function newOrder($post)
    {
        $this->id_customer=Yii::$app->getUser()->id;
        $this->date_create=date("Y-m-d H:i:s");
        $this->date_deadline=date("Y-m-d H:i",strtotime($this->date_deadline));
        $this->show_pre_source=0;
        if($this->special==1){
            $this->id_status=2;
            $this->id_performer=$this->idSpecialUser;


        }else{
            $this->id_status=1;
        }

        if($this->validate()){
            $order=new Order;
            $order->setAttributes($this->attributes);
            $order->info_text=nl2br($this->info_text);
            if($order->validate()) {
                $order->save();
                if($order->id_status==2){
                    $modelPerformerOrder= new PerformerOrder;
                    $modelPerformerOrder->applyAutoCreateOrder($order->id,$order);
                    $order->choisePerformer($order->id_performer); //уведомление внутри функции отправится
                }


                $modelNotification= new Notification;
                $modelNotification->autoComplete(0,$order->id_customer,1,json_encode(["id_order"=>$order->id]));
               

                return $order;
            }
            else {
                Yii::trace($order->getErrors());
            }
        }
        Yii::trace($this->getErrors());
        return false;


    }
}
