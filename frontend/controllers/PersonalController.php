<?php


namespace frontend\controllers;
use Yii;
use common\models\User;
use common\models\Reviews;
use common\models\Notification;
use frontend\models\editInfoUserForm;
use frontend\models\StatusOnline;
use frontend\models\UploadAvatar;
use frontend\models\UploadPortfolio;
use common\models\FilesImage;
use frontend\models\LastMessage;
use common\models\Message;
use yii\db\Query;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use common\models\SafeCrow;
class PersonalController extends \yii\web\Controller
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
                    [
                        'actions' => ['view-people'],
                        'allow' => true,
                        'roles' => ['?','@'],
                    ],
                ],
            ]
        ];
    }
    public function beforeAction($action)
    {
        $modelStatus=new StatusOnline;
        $modelStatus->updateDate();
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        if($action->id=="notifications"){
            $requestNotification=Notification::updateAll(["viewed"=>0],["id_user"=>Yii::$app->getUser()->id]);
        }

        // your custom code here
        return $result;
    }

    public function actionBindCard()
    {

        $get=Yii::$app->request->get();

        if(isset($get["status"]) && $get["status"]=="success"){
            Yii::$app->session->addFlash('success', "Карта успешно привязана");
            return $this->redirect('/personal');
        }elseif(isset($get["status"]) && $get["status"]!="success"){
            Yii::$app->session->addFlash('error', "Произошла ошибка, попробуйте позже");
            return $this->redirect('/personal');
        }


        $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"],Yii::$app->params["safecrow_api_secret"],Yii::$app->params["safecrow_dev"]);
        $modelUser=Yii::$app->getUser()->identity;
        $modelUserSaveCrow=$modelUser->safeCrowUser;

        if(!isset($modelUserSaveCrow)){
            if($modelUser->registrationSafeCrow()){
                Yii::trace("2");
                unset($modelUser->safeCrowUser);
                $modelUserSaveCrow=$modelUser->safeCrowUser;
                Yii::trace($modelUserSaveCrow);
            }else{
                Yii::$app->session->addFlash('error', "Ошибка регистрации на сервисе SafeCrow");
                return $this->redirect('/personal');
            }
        }

       $result=$modelSafeCrow->bindCardOnUser(
           $modelUserSaveCrow->id_user_safe_crow,
           Yii::$app->urlManager->createAbsoluteUrl(['personal/bind-card'])

       );


       if($result && isset($result->redirect_url)){
           return $this->redirect($result->redirect_url);
       }else{
           Yii::$app->session->addFlash('error', "Произошла ошибка, попробуйте позже");
           return $this->redirect('/personal');
       }


    }


    public function actionIndex()
    {
        $post=Yii::$app->request->post();

        $userModel = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();

        $userModelNew=new editInfoUserForm;

        if($userModelNew->load($post)){

            $user=$userModelNew->update();
            if(isset($user)){
                $user->updateUserSafeCrow();
                Yii::$app->session->addFlash('success', "Изменения в личных данных сохранены");
                return $this->redirect('personal');
            }
            Yii::$app->session->addFlash('error', "Произошла ошибка");

        }else{
            $userModelNew->name=$userModel->name;
            $userModelNew->surname=$userModel->surname;
            $userModelNew->patronymic=$userModel->patronymic;
            $userModelNew->username=$userModel->username;
            $userModelNew->email=$userModel->email;
            $userModelNew->telephone=$userModel->telephone;
			$userModelNew->is_send_email=$userModel->is_send_email;
            $userModelNew->password="";
            $userModelNew->repeatpassword="";
        }


        $modelAllUserBindCard=$userModel->bindCardSaveCrow;

        return $this->render('index',[
                "block"=>"personal_block",
                'userModel'=>$userModel,
                "arrayModel"=>[
                    'userModel'=>$userModelNew,

                    "modelAllUserBindCard"=>$modelAllUserBindCard
                ],
                "title" => 'Личные данные'
            ]);
    }



    public function actionProfile()
    {
        $post=Yii::$app->request->post();

        $modelUploadFile=new UploadAvatar;
        if (Yii::$app->request->isPost && isset($post["UploadAvatar"])) {
            $modelUploadFile->imageFile = UploadedFile::getInstance($modelUploadFile, 'imageFile');
            if ($modelUploadFile->upload()) {
                return $this->redirect("profile");

            }
        }

        $modelUploadPortfolio=new UploadPortfolio;
        if (Yii::$app->request->isPost && isset($post["UploadPortfolio"])) {
            $modelUploadPortfolio->imageFile = UploadedFile::getInstance($modelUploadPortfolio, 'imageFile');
            if ($modelUploadPortfolio->upload()) {
                return $this->redirect("profile");
            }
        }


        $userModel = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();

        $modelReview=new  Reviews;
        return $this->render('index',[
                "block"=>"profile_block",
                'userModel'=>$userModel,
                'arrayModel'=>[
                    'userModel'=>$userModel,
                    "modelUploadFile"=>$modelUploadFile,
                    "modelReview"=>$modelReview,
                    "modelUploadPortfolio"=>$modelUploadPortfolio
                ],
                "title" => 'Профиль'
        ]);
    }
    public function actionDeletePortfolio(){
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_portfolio"])){


            $modelFile=FilesImage::find()->where(["id"=>$post["id_user"]])->one();


            $modelFiles=FilesImage::find()
                ->where(["id"=>$post["id_portfolio"],"id_user"=>Yii::$app->getUser()->id])
                ->one();



            if(isset($modelFiles)){


//                unlink ( __DIR__.'/../web/files/img/portfolio/'.$modelFiles->name.".".$modelFiles->format  );
//                unlink ( __DIR__.'/../web/files/img/portfolio/'.$modelFiles->name_mini.".".$modelFiles->format_mini);
                unlink ( dirname(dirname(__DIR__)).'/frontend/web/files/img/portfolio/'.$modelFiles->name.".".$modelFiles->format  );
                unlink ( dirname(dirname(__DIR__)).'/frontend/web/files/img/portfolio/'.$modelFiles->name_mini.".".$modelFiles->format_mini);

                $modelFiles->delete();

                return  json_encode([
                    "delete" => "true",

                ]);
            }else{
                return  json_encode([
                    "error" => "Ошибка"
                ]);
            }

        }
        return json_encode([
            "error" => "Ошибка"
        ]);
    }

    public function actionGetReviews()
    {
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["last_id"]) && isset($post["id_user"])){
            $userModel=User::find()->where(["id"=>$post["id_user"]])->one();
            $modelReviews=$userModel->getReviews(5,$post["last_id"],1);


            if(count($modelReviews)!=0){
                return  json_encode([
                    "block_add" => $this->renderAjax("_review",[
                        "modelReviews"=>$modelReviews,
                        "userModel"=>$userModel
                    ]),

                ]);
            }else{
                return  json_encode([
                    "hidden_button" => "1"
                    ]);
            }

        }
        return json_encode([
            "error" => "Ошибка"
        ]);



    }

    public function actionUpdateMiniInfo(){
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_user"]) && $post["id_user"]==Yii::$app->getUser()->id){

            $userModel=User::find()->where(["id"=>$post["id_user"]])->one();
           // Yii::trace(nl2br($post["text_short_info"]));
            $userModel->short_info=nl2br($post["text_short_info"]);
            if($userModel->validate()){
                $userModel->save();
                return  json_encode([
                    "update" => deleteScript($userModel->short_info)
                ]);
            }else{
                Yii::trace($userModel->getErrors());
                return  json_encode([
                    "update" => "false"
                ]);
            }
        }
        return  json_encode([
            "update" => "false"
        ]);
    }


    public function actionUpdateRequirements(){
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_user"]) && $post["id_user"]==Yii::$app->getUser()->id){

            $userModel=User::find()->where(["id"=>$post["id_user"]])->one();

            $userModel->requirements=nl2br($post["text"]);
            if($userModel->validate()){
                $userModel->save();
                return  json_encode([
                    "update" => deleteScript($userModel->requirements),
                    "message"=>"Информация обновлена"
                ]);
            }else{
                Yii::trace($userModel->getErrors());
                return  json_encode([
                    "update" => "false",
                    "message"=>"Ошибка, неккоректные данные"
                ]);
            }
        }
        return  json_encode([
            "update" => "false",
            "message"=>"Ошибка, неккоректные данные"
        ]);
    }




    public function actionViewPeople($id_user="add")
    {
        if($id_user==Yii::$app->getUser()->id)
            return $this->redirect('profile');
        $post=Yii::$app->request->post();
        $modelReview=new  Reviews;

        if(isset($post["Reviews"]["id_to_user"]) && $id_user=="add") {
            $id_user=$post["Reviews"]["id_to_user"];
            if ($modelReview->load($post) && $modelReview->validate()) {
                $modelReview->date=date("Y-m-d H:i");
                $modelReview->save();
                Yii::trace('view-people?id_user=' . $id_user);
                return $this->redirect('view-people?id_user=' . $id_user);
            }
            Yii::trace($modelReview->getErrors());
        }

        $modelUser=User::find()->where(["id"=>$id_user])->one();

        if(isset($modelUser)){
            $modelUploadFile=new UploadAvatar;
            $modelReview = new Reviews;
            $modelUploadPortfolio=new UploadPortfolio;
            return $this->render('index',[
                "block"=>"profile_block",
                "userModel"=>$modelUser,
                "arrayModel"=>[
                    "userModel"=>$modelUser,
                    'stranger'=>true,
                    "modelUploadFile"=>$modelUploadFile,
                    "modelReview"=>$modelReview,
                    "modelUploadPortfolio"=>$modelUploadPortfolio
                ],
                "title"=>"Профиль"

            ]);
        }else{
            return $this->redirect('index');
        }


    }
    
    public function actionAllMessages(){

        $modelMessage=new LastMessage;
        $modelMessage=$modelMessage->allMessageCommon(Yii::$app->getUser()->id,5);

        $modelMessageCount=new LastMessage;
        $countAllMessage=count($modelMessageCount->allMessageCommon(Yii::$app->getUser()->id,0));

        $userModel = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();

        return $this->render('index',[
            "block"=>"all_message",
            "userModel"=>$userModel,
            "arrayModel"=>[
                "modelMessage"=>$modelMessage,
                "countAllMessage"=>$countAllMessage
            ],
            "title" => 'Сообщения'
        ]);
        
        
    }

    public function actionLoadMoreMessage(){

        $countReturnMessage=5;

        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_array"])){


            $arrId=explode(",",$post["id_array"]);
            $modelMessage=new LastMessage;
            $modelMessageAdd=$modelMessage->allMessageCommon(Yii::$app->getUser()->id,$countReturnMessage,$arrId);
            Yii::trace($modelMessageAdd);
            if(isset($modelMessageAdd) && !empty ($modelMessageAdd)){
                $block="";
                foreach ($modelMessageAdd as $modelMessageOne) {
                    $modelUser=User::find()->where(["id"=>$modelMessageOne["id_user_from"]])->one();
                    $block.=$this->renderAjax("one_message_prev",[
                        "modelMessage"=>$modelMessageOne,
                        "modelUser"=>$modelUser
                    ]);

                }

                if(count($modelMessageAdd)<$countReturnMessage)
                    $arrReturn=[
                        "block_hidden" => "hidden",
                         "block_add" => $block
                    ];
                else{
                    $arrReturn=[
                        "block_add" => $block
                    ];
                }


                return  json_encode($arrReturn);
            }else{

                return  json_encode([
                    "block_hidden" => "hidden"
                ]);
            }
        }
        return  json_encode([
            "block_add" => "false"
        ]);
    }

    public function actionLoadMessageSearch(){

        $post=Yii::$app->request->post();
        if(Yii::$app->request->isAjax && isset($post["search"])){

            $modelMessage=new LastMessage;
            $modelMessageAdd=$modelMessage->search(Yii::$app->getUser()->id,$post["search"]);
            Yii::trace($modelMessageAdd);
            if(isset($modelMessageAdd) && !empty ($modelMessageAdd)){
                $block="";
                foreach ($modelMessageAdd as $modelMessageOne) {
                    $modelUser=User::find()->where(["id"=>$modelMessageOne["id_user_from"]])->one();
                    $block.=$this->renderAjax("one_message_prev",[
                        "modelMessage"=>$modelMessageOne,
                        "modelUser"=>$modelUser
                    ]);

                }


                $arrReturn=[
                    "block_add_search" => $block
                ];

                return  json_encode($arrReturn);
            }else{

                return  json_encode([
                    "block_add_search" => null
                ]);
            }
        }
        return  json_encode([
            "block_add_search" => "false"
        ]);

    }

    public function actionCorrespondence($user){
        $post=Yii::$app->request->post();

        $modelMessage=new LastMessage;
        $modelMessage=$modelMessage->correspondence(Yii::$app->getUser()->id,$user);
        $modelUser = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();
        $modelUserTo=User::find()->where(["id"=>$user])->one();

        foreach ($modelMessage as $value){
            if($value->id_user_to==Yii::$app->getUser()->id && $value->new==0){
                $value->new=1;
                $value->save();
            }

        }

        if(!isset($modelUser) || !isset($modelUserTo) || !isset($modelMessage))
            return $this->redirect(["index"]);

        $modelStatus=new StatusOnline;
        $modelStatus->id=$modelUserTo->id;


        $userModel = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();
        return $this->render('index',[
            "block"=>"correspondence",
            "userModel"=>$userModel,
            'arrayModel'=>[
                "modelMy"=>$modelUser,
                "modelUser"=>$modelUserTo,
                "modelMessage"=>$modelMessage,
                "modelStatus"=>$modelStatus
            ],
            "title" => 'Личная переписка'
        ]);

    }

    public function actionLoadCorrespondence(){
        $post=Yii::$app->request->post();

        $nMessage=10;

        if(Yii::$app->request->isAjax && isset($post["id_user"]) && ( isset($post["id_since"]) || isset($post["id_to"]) ) ) {

            $modelMessage = new LastMessage;

            if(isset($post["id_since"]))
                $modelMessage = $modelMessage->correspondence(Yii::$app->getUser()->id, $post["id_user"], $nMessage, $post["id_since"]);
            elseif(isset($post["id_to"]))
                $modelMessage = $modelMessage->correspondence(Yii::$app->getUser()->id, $post["id_user"], $nMessage,null,$post["id_to"]);

            $modelUser = User::find()->where(["id" => Yii::$app->getUser()->id])->one();
            $modelUserTo = User::find()->where(["id" => $post["id_user"]])->one();

            if (!isset($modelUser) || !isset($modelUserTo) || !isset($modelMessage))
                return  json_encode([
                    "block_add_corr" => "false"
                ]);

            $modelStatus = new StatusOnline;
            $modelStatus->id = $modelUserTo->id;

            $returnBlock="";
            if(isset($modelMessage) && !empty ($modelMessage)) {

                foreach ($modelMessage as $value){
                    if($value->id_user_to==Yii::$app->getUser()->id && $value->new==0){
                        $value->new=1;
                    }
                    $value->save();
                }

                foreach ($modelMessage as $modelMessageOne) {
                    if($modelMessageOne->id_user_from==$modelUser->id)
                        $modelUserMessage=$modelUser;
                    else
                        $modelUserMessage=$modelUserTo;


                    $returnBlock.=$this->renderAjax("one_message_prev", [
                        "modelMessage"=>$modelMessageOne,
                        "modelUser"=>$modelUserMessage,
                        "mod"=>"full"
                    ]) ;
                    $hiddenButton=false;
                    if($nMessage>count($modelMessage) && isset($post["id_since"]))
                        $hiddenButton="hidden";
                }
                Yii::trace($returnBlock);
                return  json_encode([
                    "block_add_corr" => $returnBlock,
                    "hidden_button"=>$hiddenButton
                ]);
            }else{
                return json_encode([
                    "block_add_corr" => "empty",
                    "hidden_button"=>"hidden"
                ]);
            }



        }else{
            return  json_encode([
                "block_add_corr" => "false"
            ]);
        }

    }

    public function actionSendCorrespondence(){
        $post=Yii::$app->request->post();

        if(Yii::$app->request->isAjax && isset($post["id_user"]) &&  isset($post["text"]) ) {

            $modelNewMessage= new Message;
            $modelNewMessage->text=nl2br(ltrim($post["text"]));
            $modelNewMessage->id_user_from=Yii::$app->getUser()->id;
            $modelNewMessage->id_user_to=$post["id_user"];
            $modelNewMessage->date_create=date("Y-m-d H:i:s");

            $lastMessage=Message::find()->where(["id_user_from"=>Yii::$app->getUser()->id])->orderBy("date_create Desc")->one();

            if(date("Y-m-d H:i:s")==$lastMessage->date_create) {
                return  json_encode([
                    "block_add_corr" => "false"
                ]);

            }

//            $modelNewMessage->date_create=Yii::$app->formatter->asDatetime(date("Y-m-d H:i:s"));
            if($modelNewMessage->validate()) {
                $modelNewMessage->save();
                $modelNewMessage=Message::find()->where(["id"=>$modelNewMessage->id])->one();
                $modelUser = User::find()->where(["id" => Yii::$app->getUser()->id])->one();
                $modelUserTo = User::find()->where(["id" => $post["id_user"]])->one();

                if (!isset($modelUser) || !isset($modelUserTo))
                    return  json_encode([
                        "block_add_corr" => "false"
                    ]);

                $modelStatus = new StatusOnline;
                $modelStatus->id = $modelUserTo->id;

                $returnBlock="";

                if($modelNewMessage->id_user_from==$modelUser->id)
                    $modelUserMessage=$modelUser;
                else
                    $modelUserMessage=$modelUserTo;


                $returnBlock.=$this->renderAjax("one_message_prev", [
                    "modelMessage"=>$modelNewMessage,
                    "modelUser"=>$modelUserMessage,
                    "mod"=>"full"
                ]) ;

                return  json_encode([
                    "block_add_corr" => $returnBlock
                ]);
            }else {
                return  json_encode([
                    "block_add_corr" => "false"
                ]);
            }
        }else{
            return  json_encode([
                "block_add_corr" => "false"
            ]);
        }

    }


    public function actionCreateMessage()
    {
        $post=Yii::$app->request->post();
        $modelNewMessage= new Message;
        if($modelNewMessage->load($post)) {
            $modelNewMessage->id_user_from=Yii::$app->getUser()->id;
            $modelNewMessage->date_create=date("Y-m-d H:i:s");
            if($modelNewMessage->validate()) {
                $modelNewMessage->save();
                return $this->redirect('correspondence?user=' . $post["Message"]["id_user_to"]);

            }
         }

        $modelUser = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();

//        $modelNewMessage= new Message;
//        $modelNewMessage->text=$post["text"];
//        $modelNewMessage->id_user_from=Yii::$app->getUser()->id;
//        $modelNewMessage->id_user_to=$post["id_user"];

        return $this->render('index',[
            "block"=>"new_correspondence",
            "userModel"=>$modelUser,
            'arrayModel'=>[
                "modelMy"=>$modelUser,
                "modelMessage"=>$modelNewMessage
            ],
            'title'=>'Новое сообщение'
        ]);
    }


    public function actionNameList ( $q = null , $id = null ) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = new Query;
            $query->select(["id, CONCAT(username,'')  AS text"])
//            $query->select(["id , username , surname , name "])
                ->from('user')
                ->where(['like', 'username', $q])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();


            Yii::trace($data); Yii::trace( array_values($data));
            $out['results'] = array_values($data);

        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->username];
        }
        return $out;
    }


    public function actionNotifications(){
        $userModel = User::find()->where(["id"=>Yii::$app->getUser()->id])->one();


        $requestNotification=Notification::find()->where(["id_user"=>Yii::$app->getUser()->id])->orderBy("date_create DESC,id Desc");

        $modeNotification = new ActiveDataProvider([
            'query' => $requestNotification,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);


        return $this->render('index',[
            "block"=>"notifications",
            "userModel"=>$userModel,
            'arrayModel'=>[
                "modeNotification"=>$modeNotification
            ],
            "title"=>"Уведомления"
        ]);
    }


}
