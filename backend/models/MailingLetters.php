<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Message;
use common\models\User;
/**
 * ContactForm is the model behind the contact form.
 */
class MailingLetters extends Model
{

    public $text;
    


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['text'], 'required'],
            [['text'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            
            'text' => 'Текст для рассылки',

        ];
    }

	public function start(){
		$users=User::find()->where(["status"=>"10"]);
		$admin=User::find()->where(["username"=>"admin"])->one();
		
		if(isset($admin)){
			foreach ($users->each(50) as $user){
				
				$modelNewMessage= new Message;
				$modelNewMessage->text=nl2br(ltrim($this->text));
				$modelNewMessage->id_user_from=$admin->id;
				$modelNewMessage->id_user_to=$user->id;
				$modelNewMessage->date_create=date("Y-m-d H:i:s");
				$modelNewMessage->save();
			}
			return true;
		}
		
		return false;
	}
}
