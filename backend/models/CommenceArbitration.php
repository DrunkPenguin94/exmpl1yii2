<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Order;
use common\models\PerformerOrder;
use common\models\User;
/**
 * ContactForm is the model behind the contact form.
 */
class CommenceArbitration extends Model
{

    public $text;
    public $id_order;
    public $result;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['text', 'id_order','result'], 'required'],
            [['id_order'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_order' => 'id']],

            [['id_order','result'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id_order' => 'ID',
            'result' => 'Решение арбитража в пользу',
            'text' => 'Комментарий к решению',

        ];
    }


}
