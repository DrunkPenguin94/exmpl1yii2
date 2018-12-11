<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use Yii;
use common\models\SafeCrow;
/**
 * Signup form
 */
class BindCardOnOrderForm extends Model
{
    public $id_card;
    public $id_order;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['id_order'], 'required'],

            [['id_card'],'required', 'message' => 'Пожалуйста выберете карту'],
            [['id_card','id_order'],'safe']

        ];
    }

    public function attributeLabels() {
        return [
            'id_card' => 'Карта',
            'id_order'=>'Id заказа',


        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function bind()
    {

        

    }


}
