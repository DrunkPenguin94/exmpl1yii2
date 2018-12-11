<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\LoginFormEmail;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Feedback;
use common\models\FeedbackSearch;
use common\models\Reviews;
use common\models\User;
use frontend\models\StatusOnline;
use common\models\Message;
use common\models\SafeCrow;
use common\models\FilesOrder;
use yii\web\NotFoundHttpException;
/**
 * Site controller
 */
class SiteController extends Controller
{

    public function beforeAction($action)
    {
        $modelStatus=new StatusOnline;
        $modelStatus->updateDate();
        return parent::beforeAction($action);
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */




    public function actionIndex()
    {
        if(isset(Yii::$app->getUser()->id))
            return $this->redirect("order");
        return $this->render('index');
    }


    public function actionCheckNotice()
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(!isset(Yii::$app->getUser()->id))
            return json_encode(["error"=>"Пользователь не авторизован"]);


        $countMessage=Message::getNewMessageFromUser(Yii::$app->getUser()->id);
        $countNotification=Yii::$app->getUser()->identity->countNewNotifications;


        return json_encode([
            "message"=>$countMessage,
            "notice"=>$countNotification
        ]);
    }



    public function actionTest()
    {

            Yii::trace(Yii::$app->params["adminEmail"]);
        $model=User::find()->one();

        $result = Yii::$app->mailer
            ->compose(
                ['html' => 'registration-html', 'text' => 'registration-text'],
                ['user' => $model]
            )
            ->setFrom([Yii::$app->params["adminEmail"]=>Yii::$app->name])
         
            ->setTo(Yii::$app->params["adminEmail"])
            ->setSubject('Восстановление пароля')
            ->send();
        return $result;





    }



    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginFormEmail();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }


    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }



    public function actionAbout()
    {
        return $this->render('about');
    }


    public function actionSignup()
    {
        $post=Yii::$app->request->post();
        $model = new SignupForm();
        $model->typeuser=1;
        if ($model->load(Yii::$app->request->post())) {

            $model->typeuser=$post["SignupForm"]["typeuser"];
            $model->googleCaptcha=$post["g-recaptcha-response"];
            if ($user = $model->signup()) {
                $model->sendEmail($user);
                return $this->render('end_registration', [
                    'model' => $model,
                ]);
              
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                //Yii::$app->session->addFlash('success', 'Check your email for further instructions.');
                return $this->render('resetPasswordThx', [

                ]);
                //return $this->goHome();
            } else {
                //Yii::$app->session->addFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }


    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            //Yii::$app->session->addFlash('success', 'New password saved.');
            return $this->render('resetPasswordGood', [
                'model' => $model,
            ]);

        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


    public function actionForRetoucher()
    {
        return $this->render('forRetoucher');
    }

    public function actionForCustomer()
    {
        return $this->render('forCustomer');
    }

    public function actionRules()
    {
        return $this->render('rules');
    }

    public function actionFeedback()
    {
		$post=Yii::$app->request->post();
        $model = new Feedback();
		$model->googleCaptcha=empty($post["g-recaptcha-response"])? null : $post["g-recaptcha-response"];
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->sendEmail();



            return $this->render('feedbackSent');
        } else {
            return $this->render('feedback', [
                'model' => $model,
            ]);
        }

    }

    public function actionWishes()
    {
        return $this->render('wishes');
    }

    public function actionAccountActivation($token)
    {
        $modelUser  = User::findByUserActiveToken($token);
        if ($modelUser !=null) {
            $modelUser->status=10;
			$modelUser->is_send_email=1;


            $modelUser->save();

            $modelUser->registrationSafeCrow();

            Yii::$app->getUser()->login($modelUser);



        }
        return $this->redirect("/");
    }

    public function actionRating()
    {
        $modelCustomer=new User;
        $modelCustomer=$modelCustomer->getListCustomer(10);

        $modelRetusher=new User;
        $modelRetusher=$modelRetusher->getListRetusher(10);

        return $this->render('rating',[
            "modelCustomer"=>$modelCustomer,
            "modelRetusher"=>$modelRetusher
        ]);
    }

    public function actionLoadRating()
    {
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_array_1"]) && isset($post["id_array_2"])) {

            $arrId1=explode(",",$post["id_array_1"]);
            $arrId2=explode(",",$post["id_array_2"]);

            $modelCustomer = new User;
            $modelCustomer = $modelCustomer->getListCustomer(10,$arrId1);

            $modelRetusher = new User;
            $modelRetusher = $modelRetusher->getListRetusher(10,$arrId2);

            $returnBlock1="";
            $returnBlock2="";

            if(isset($modelCustomer) && !empty($modelCustomer))
                foreach($modelCustomer as $value)
                    $returnBlock1.=$this->renderAjax("rating_one",["value"=>$value]);

            if(isset($modelRetusher) && !empty($modelRetusher))
                foreach($modelRetusher as $value)
                    $returnBlock2.=$this->renderAjax("rating_one",["value"=>$value]);

            if(count($modelCustomer)<10&& count($modelRetusher)<10 )
                $flagHiddenButton=true;
            else
                $flagHiddenButton=false;


            $arrReturn=[
                "customer"=>$returnBlock1,
                "retusher"=>$returnBlock2,
                "hidden"=>$flagHiddenButton
            ];

            return  json_encode($arrReturn);
        }else{
            return  json_encode([
                "error"=>true,
            ]);
        }
    }

    public function actionDownload($id = null)
    {

        if(!isset(Yii::$app->getUser()->id)){
            throw new NotFoundHttpException('User not found');
        }

        $modelFile=FilesOrder::checkAccessFile($id);
		Yii::trace( $modelFile);
        if($modelFile){


            $path=dirname(dirname(__DIR__)) . '/'.$modelFile->path.$modelFile->folder.'/'.$modelFile->name.".".$modelFile->format ;


            $f = fopen($path, 'r');
            Yii::$app->response->sendStreamAsFile($f, $modelFile->name.".".$modelFile->format , ['mimeType' => '<mimeType>'])->send();
        } else {
            throw new NotFoundHttpException('Access denied');
        }

    }
	
	public function actionDownloadMini($id = null)
    {

        $modelFile = FilesOrder::find()->where(["id" => $id])->one();
		if(isset($modelFile)){

			$path=dirname(dirname(__DIR__)) . '/'.$modelFile->path.$modelFile->folder.'/'.$modelFile->name."_m".".".$modelFile->format ;


			$f = fopen($path, 'r');
			Yii::$app->response->sendStreamAsFile($f, $modelFile->name.".".$modelFile->format , ['mimeType' => '<mimeType>'])->send();
		}else{
			 throw new NotFoundHttpException('Access denied');
		}

    }
	
	
}
