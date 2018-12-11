<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use Yii;

/**
 * Signup form
 */
class EditInfoUser extends Model
{
	public $id;
    public $name;
    public $surname;
    public $patronymic;
    public $email;
    public $username;
    public $rating;
    public $skill;
	public $password;
	public $is_send_email;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['username','name','surname','patronymic','rating','skill','id','password','is_send_email'], 'safe'],
            [['username','name','surname','patronymic'], 'trim'],
            ['username', 'required'],
            [['skill','rating'], 'integer'],
			 ['username', 'unique', 'targetClass' => '\common\models\User','filter'=>function($query){

                $query->andWhere(["<>","id",$this->id]);
                return $query;
            }, 'message' => 'Пользователь с таким логином уже зарегистрирован.'],
           //  ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с таким Логином уже зарегистрирован.'],
//            ['username', 'string', 'min' => 2, 'max' => 255],
//            ['username', function ($attribute, $params) {
//                $str=str_split ($this->$attribute);
//                foreach($str as $value){
//                    if(!checkLetter($value)){
//                        $this->addError($attribute, 'Допустимымые символы для логина a-z, A-Z, 0-9, "_". Без знака пробела');
//                        break;
//                    }
//                }
//            }],
//            ['name', function ($attribute, $params) {
//                $str= preg_split('//u', $this->$attribute,-1, PREG_SPLIT_NO_EMPTY);
//                Yii::trace($str);
//                foreach($str as $value){
//                    if(!checkLetterRus($value)){
//
//                        $this->addError($attribute, 'Допустимымые символы для имени a-я, А-Я.');
//                        break;
//                    }
//                }
//            }],
//            ['surname', function ($attribute, $params) {
//                $str=  preg_split('//u', $this->$attribute,-1, PREG_SPLIT_NO_EMPTY);
//                foreach($str as $value){
//                    if(!checkLetterRus($value)){
//                        $this->addError($attribute, 'Допустимымые символы для фамилии a-я, А-Я.');
//                        break;
//                    }
//                }
//            }],


        ];
    }

    public function attributeLabels() {
        return [
			'name' => 'Имя',
			'surname' => 'Фамилия',
			'patronymic' => 'Отчество',
			'username' => 'Логин',
            'skill' => 'Скилл',
            'rating' => 'Рейтинг',
			'password' => 'Новый Пароль',
			'is_send_email'=>'Рассылка e-mail'
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function loadData($model){
        $this->name = $model->name;
        $this->surname = $model->surname;
        $this->patronymic = $model->patronymic;
        $this->username = $model->username;
		$this->email = $model->email;
        $this->skill = $model->skill;
        $this->rating = $model->rating;
		$this->is_send_email = $model->is_send_email;
    }
    public function update($id)
    {
        if (!$this->validate()) {
            Yii::trace($this->getErrors());
            return false;
        }



        $user = User::find()->where(["id"=>$id])->one();
        if(isset($user)){
            $user->name = $this->name;
            $user->surname = $this->surname;
            $user->patronymic = $this->patronymic;
            $user->username = $this->username;

            $user->skill = $this->skill;
            $user->rating = $this->rating;
			$user->is_send_email = $this->is_send_email;
			if(isset($this->password) && $this->password!="") {
				$user->setPassword($this->password);
				$user->generateAuthKey();
			}
			
            $user->save();
        }


        return  $user;
       // return $user;
    }
}
