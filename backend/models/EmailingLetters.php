<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\Message;
use common\models\User;
/**
 * ContactForm is the model behind the contact form.
 */
class EmailingLetters extends Model
{

    public $text;
    public $theme;
	public $name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['text','theme'], 'required'],
            [['text','theme','name'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            
            'text' => 'Текст для рассылки',
			'theme' => 'Тема рассылки',
			'name' => 'Название',
        ];
    }

	public function start($name){
		$users=User::find()->where(["status"=>"10","is_send_email"=>"1"]);
		
			
		
			foreach ($users->each(50) as $user){
				Yii::$app->mailer
				->compose(
					['html' => 'email-html', 'text' => 'email-text'],
					['model' => $this,'name_img'=>$name]
				)
				->setFrom([Yii::$app->params["robotEmail"]=>Yii::$app->name])
				->setTo($user->email)
				->setSubject($this->theme)
				->send();
				
			}
			
		
		return true;
	}
}
