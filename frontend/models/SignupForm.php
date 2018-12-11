<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use Yii;
use common\models\SafeCrow;
/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $telephone;
  //  public $name;
  //  public $surname;
    public $password;
    public $repeatpassword;
    public $typeuser;
    public $googleCaptcha;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            [['username','telephone'], 'required'],

            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с таким Логином уже зарегистрирован.'],
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

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для email a-z, A-Z, 0-9, "_". Без знака пробела');
                        break;
                    }
                }
            }],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Пользователь с такой электронной почтой уже зарегистрирован.'],


         //   ['name', 'required'],
         //   ['name', 'string', 'min' => 2, 'max' => 255],
          //  ['surname', 'required'],
          //  ['surname', 'string', 'min' => 2, 'max' => 255],


            [['password','repeatpassword'], 'required'],
            ['password', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для пароля a-z, A-Z, 0-9, "_". Без знака пробела.');
                        break;
                    }
                }
            }],
            [['password','repeatpassword'], 'string', 'min' => 6, 'max' => 255],
            ['password','compare', 'compareAttribute'=>'repeatpassword'],
            ['typeuser','safe'],

            ['telephone', 'string', 'min' => 10, 'max' => 18],
          /*  ['telephone', function ($attribute, $params) {
                $str=str_split ($this->$attribute);
                foreach($str as $value){
                    if(!checkLetter($value)){
                        $this->addError($attribute, 'Допустимымые символы для телефона 0-9. Без знака пробела.');
                        break;
                    }
                }
            }],*/

            ['googleCaptcha','safe'],

            //Добавить проверку когда будет на рабочем
            ['googleCaptcha', 'required'],
            ['googleCaptcha', function ($attribute, $params) {

                $googleCheck = file_get_contents('https://www.google.com/recaptcha/api/siteverify?'.
                    'secret='.Yii::$app->params["googleSecretKeyCaptcha"].'&'.
                    'response='.$this->$attribute


                ); //получаем данные в формате json

                $googleCheckDecode = json_decode($googleCheck);
                Yii::trace($googleCheckDecode->success);
                if(!isset($googleCheckDecode->success) || $googleCheckDecode->success==false){
                   $this->addError($attribute, 'Ошибка reCaptcha, подтвердите что вы не робот.');
                }





            }],
        ];
    }

    public function attributeLabels() {
        return [
            'username' => 'Логин',
            'name'=>'Имя',
            'surname'=>'Фамилия',
            'email' => 'Электронная почта',
            'password' => 'Пароль',
            'repeatpassword' => 'Повторите пароль',
            'googleCaptcha'=>'reCAPTCHA',
            'telephone'=>'Телефон'

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            Yii::trace($this->getErrors());
            return null;
        }

       // Yii::trace($this->typeuser);

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->telephone = $this->telephone;
        $user->status = 0;
        $user->is_admin = 0;
       // $user->name = $this->name;
       // $user->surname = $this->surname;
        $user->type = $this->typeuser;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        
        return $user->save() ? $user : null;
    }

    public function sendEmail($user){
        return Yii::$app->mailer
            ->compose(
                ['html' => 'registration-html', 'text' => 'registration-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params["robotEmail"]=>Yii::$app->name])
            
            ->setTo($user->email)
            ->setSubject('Подтверждение регистрации')
            ->send();
    }
}
