<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use Yii;

/**
 * Signup form
 */
class editInfoUserForm extends Model
{
    public $name;
    public $surname;
    public $patronymic;
    public $email;
    public $telephone;
    public $username;
    public $password;
    public $repeatpassword;
	public $is_send_email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','name','surname','patronymic','telephone'], 'trim'],
            ['username', 'required'],
			['is_send_email', 'safe'],
            ['username', 'unique', 'targetClass' => '\common\models\User','filter'=>function($query){

                $query->andWhere(["<>","id",Yii::$app->getUser()->id]);
                return $query;
            }, 'message' => 'Пользователь с таким Логином уже зарегистрирован.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для логина a-z, A-Z, 0-9, "_". Без знака пробела');
                        break;
                    }
                }
            }],
            ['name', function ($attribute, $params) {
                $str= preg_split('//u', $this->$attribute,-1, PREG_SPLIT_NO_EMPTY);
                Yii::trace($str);
                foreach($str as $value){
                    if(!checkLetterRus($value)){

                        $this->addError($attribute, 'Допустимымые символы для имени a-я, А-Я.');
                        break;
                    }
                }
            }],
            ['surname', function ($attribute, $params) {
                $str=  preg_split('//u', $this->$attribute,-1, PREG_SPLIT_NO_EMPTY);
                foreach($str as $value){
                    if(!checkLetterRus($value)){
                        $this->addError($attribute, 'Допустимымые символы для фамилии a-я, А-Я.');
                        break;
                    }
                }
            }],
         /*   ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User','filter'=>function($query){

                $query->andWhere(["<>","id",Yii::$app->getUser()->id]);
                return $query;
            }, 'message' => 'Пользователь с такой электронной почтой уже зарегистрирован.'],
            ['email', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для email a-z, A-Z, 0-9, "_". Без знака пробела');
                        break;
                    }
                }
            }],
			*/

//            [['password','repeatpassword'], 'required'],
            [['password','repeatpassword'], 'string', 'min' => 6],
            ['password','compare', 'compareAttribute'=>'repeatpassword'],
            ['password', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для пароля a-z, A-Z, 0-9, "_". Без знака пробела.');
                        break;
                    }
                }
            }],
            ['telephone', 'string', 'min' => 10, 'max' => 18],
           /* ['telephone', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для телефона 0-9. Без знака пробела.');
                        break;
                    }
                }
            }],*/
        ];
    }

    public function attributeLabels() {
        return [
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'username' => 'Логин',
            'telephone' => 'Телефон',
          //  'email' => 'Электронная почта',
            'password' => 'Пароль',
            'repeatpassword' => 'Повторите пароль',
			'is_send_email'=>'Рассылка E-mail'

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function update()
    {
        if (!$this->validate()) {
            Yii::trace($this->getErrors());
            return null;
        }



        $user = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->patronymic = $this->patronymic;
        $user->username = $this->username;
       // $user->email = $this->email;
        $user->telephone = $this->telephone;
		$user->is_send_email = $this->is_send_email;
        if(isset($this->password) && $this->password!="") {
             $user->setPassword($this->password);
            $user->generateAuthKey();
        }


        return $user->save() ? $user : null;
       // return $user;
    }
}
