<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use common\models\ForumFolder;
use common\models\ForumMessage;
use common\models\ForumPost;
use frontend\models\NewMessageForum;
/**
 * Site controller
 */
class ForumController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [

                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex($idFolder=null)
    {

        $modelFolder=ForumFolder::find()->orderBy(' type ,mass desc,date_create DESC');

        if(isset($idFolder))
            $modelFolder->andWhere(["id_parent"=>$idFolder]);
        else
            $modelFolder->andWhere(["is","id_parent",null]);

        $dataProviderFolder = new ActiveDataProvider([
            'query' => $modelFolder,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);



        $modelBackFolder = ForumFolder::find()->where(["id" => $idFolder])->one();
        $modelBack=null;
        if(isset($modelBackFolder))
            $modelBack=$modelBackFolder;


        $modelNewMessageForum=new NewMessageForum;
        $modelNewMessageForum->mod=0;
        return $this->render('index',[
            "modelBack"=>$modelBack,
            'modelFolder'=>$modelFolder,
            'dataProviderFolder'=>$dataProviderFolder,
            'modelNewMessageForum'=>$modelNewMessageForum
        ]);
    }

    public function actionPost($idPost=null)
    {
        $modelNewMessageForum=new NewMessageForum;
        $post=Yii::$app->request->post();

        if($modelNewMessageForum->load($post)){
            $modelNewMessageForum->id_user=Yii::$app->getUser()->id;
            if($modelNewMessageForum->mod==0){
                Yii::trace("0");
                $modelPost=$modelNewMessageForum->createTheme();
                if(isset($modelPost))
                    return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['forum/post',"idPost"=>$modelPost->id_post]));
            }elseif($modelNewMessageForum->mod==1){
                Yii::trace("1");
                $modelPost=$modelNewMessageForum->createMessage();
                if(isset($modelPost)) {
                    $modelCountPost=ForumMessage::find()->where(["id_post"=>$modelPost->id_post])->all();
                    $modelCountPost=ceil(count($modelCountPost)/10);
                    return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['forum/post', "idPost" => $modelPost->id_post, 'page' => $modelCountPost]));
                }
            }
        }


        if(empty($idPost))
            return $this->redirect('index');

        $modelMessage=ForumMessage::find()->where(["id_post"=>$idPost])->orderBy('date_create');


        $dataProviderMessage = new ActiveDataProvider([
            'query' => $modelMessage,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);


        $modelMessage=$modelMessage->one();
        $modelBackFolder = ForumFolder::find()->where(["id" => $idPost])->one();
        $modelNewMessageForum=new NewMessageForum;
        $modelNewMessageForum->mod=1;


        return $this->render('index',[
            "modelBack"=>$modelBackFolder,
            'modelMessage'=>$modelMessage,
            'dataProviderMessage'=>$dataProviderMessage,
            'modelNewMessageForum'=>$modelNewMessageForum
        ]);
    }

}
