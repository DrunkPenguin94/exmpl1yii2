<?php

namespace frontend\controllers;
use common\models\PerformerOrder;
use frontend\models\StatusOnline;
use kroshilin\yakassa\actions\CheckOrderAction;
use yii\filters\AccessControl;
use common\models\Order;
use frontend\models\CreateOrder;
use frontend\models\UploadAdditionalFileOrder;
use frontend\models\UploadAdditionalFileToCheckOrder;
use frontend\models\UploadAdditionalFileOrderCreate;
use common\models\FilesOrder;
use common\models\OrderHistory;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\UploadedFile;
use common\models\Notification;
use common\models\OrderRevision;
use common\models\OrderArbitration;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use common\models\SafeCrow;
use common\models\SafeCrowOrder;
use common\models\User;
use common\models\Reviews;
class OrderController extends \yii\web\Controller
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


    public function beforeAction($action)
    {
        $modelStatus=new StatusOnline;
        $modelStatus->updateDate();
        return parent::beforeAction($action);
    }
    public function actionIndex()
    {
        $get=Yii::$app->request->get();


        if(isset($get["quantity"])){
            if(in_array($get["quantity"],["10","20","50"]))
                $quantity=$get["quantity"];
        }else{
            $quantity=10;
        }


        $requestOrder=Order::createQueryForMenuOrder($get);




        $modelOrders = new ActiveDataProvider([
            'query' => $requestOrder,
            'pagination' => [
                'pageSize' => $quantity,
            ],
        ]);

        $ordersCount=$requestOrder->count();
        $arrParam=[
            "modelOrders"=>$modelOrders,
            "ordersCount"=>$ordersCount,
        ];
        return $this->render('index',[
                "layouts"=>"all_order",
                "arrParam"=>$arrParam
        ]);
    }


    public function actionAddReview(){
        $post=Yii::$app->request->post();
        $id_user=Yii::$app->getUser()->id;


        $modelOrder=Order::find()->where(["id"=>$post["Reviews"]["id_order"]])->one();
        if(isset($modelOrder) && ($modelOrder->id_performer==$id_user || $modelOrder->id_customer==$id_user)) {
            $modelReview = new Reviews();
            if ($modelReview->load($post)) {
                $modelReview->id_from_user = $id_user;
                $modelReview->date=date("Y-m-d H:i");
                $modelReview->text=nl2br($post["Reviews"]["text"]);
                if($modelReview->validate()){
                    $modelReview->save();
                    Yii::$app->session->addFlash('success',"Отзыв успешно отправлен.");

                }else{
                    Yii::$app->session->addFlash('error',"Ошибка при сохранении отзыва");
                }
            }else{
                Yii::$app->session->addFlash('error',"Ошибка при сохранении отзыва");
            }
        }else{
            Yii::$app->session->addFlash('error',"Вы не относитесь к заказу");
        }

        return $this->redirect(["/order/view","id"=>$post["Reviews"]["id_order"]]);
    }

    public function actionView($id){
        $get=Yii::$app->request->get();
        if(isset($get["_"]))
            Yii::$app->session->addFlash('success', "Карта успешно привязана");

        $modelOrder=Order::find()->where(["id"=>$id])->one();




        $modelUploadAdditionalFileCheck=new UploadAdditionalFileToCheckOrder;
        $modelUploadAdditionalFileCheck->checkboxView=$modelOrder->show_pre_source;
        $modelPerformerOrder=new PerformerOrder;
        $modelOrderRevision=new OrderRevision;
        $modelOrderArbitration= new OrderArbitration;
        $modelReviews= new Reviews;
        if(!isset($modelOrder))
            return $this->redirect("index");

        $modelOrder->checkPayOrder();


        $POST=Yii::$app->request->post();

        if($modelPerformerOrder->load($POST) && $modelOrder->isSearchPerformer()&& Yii::$app->getUser()->identity->isRetusher() ){
            if($modelPerformerOrder->apply($id,$modelOrder))
                return $this->redirect(["/order/view","id"=>$id]);
        }

        $arrParam=[
            "modelOrder"=>$modelOrder,
            "modelPerformerOrder"=>$modelPerformerOrder,
            "modelUploadAdditionalFileCheck"=>$modelUploadAdditionalFileCheck,
            "modelOrderRevision"=>$modelOrderRevision,
            "modelOrderArbitration"=>$modelOrderArbitration,
            "modelReviews"=>$modelReviews
        ];

        if($modelOrder->isCreater() && ($modelOrder->isSearchPerformer() || $modelOrder->isSuggestedPerformer())){
            $modelPerformer = new ActiveDataProvider([
                'query' => PerformerOrder::find()->where(["id_order"=>$id,"status"=>0])->orderBy("status desc,id desc")
            ]);
            $arrParam["modelPerformer"]=$modelPerformer;
            if($modelOrder->isSuggestedPerformer()){
                $modelPerformerSuggested= new ActiveDataProvider([
                    'query' => PerformerOrder::find()->where(["id_order"=>$id,"status"=>1])->orderBy("status desc,id desc")
                ]);

                $arrParam["modelPerformerSuggested"]=$modelPerformerSuggested;
            }

        }





        return $this->render('index',[
            "arrParam"=>$arrParam,
            "layouts"=>"one_order"
        ]);
    }

    public function actionNameList ( $q = null , $id = null ) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];

        if (!is_null($q)) {
            $query = new Query;
            $query->select(["id, CONCAT(username, ' (',surname,' ',name,') ')  AS text"])
                ->from('user')
                ->where(['and',
                    ['like', 'username', $q],
                    ['type'=>2]
                ])
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        }
        elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => User::find($id)->username];
        }
        return $out;
    }

    public function actionCreate(){

        $post = Yii::$app->request->post();
        if(Yii::$app->getUser()->identity->type==2)
            return $this->redirect("/order");

        $modelOrder= new CreateOrder;
		$modelOrder->conditionSafeCrow=1;
        $modelFile= new UploadAdditionalFileOrderCreate;
        $modelCreater=Yii::$app->user->identity;

        if (Yii::$app->request->isAjax && $modelOrder->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelOrder);
        }




        if ($modelOrder->load($post)) {
            $modelFile->files = UploadedFile::getInstances($modelFile, 'files');
            if($modelFile->validate()) {
                $modelResult = $modelOrder->newOrder($post);
                if ($modelResult) {

                    if ($modelFile->upload($modelResult)) {
                        Yii::$app->session->addFlash('success', "Заказ успешно создан");
                        //return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $modelResult->id]));
                        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));
                    } else {
                        Yii::$app->session->addFlash('success', "Заказ успешно создан");
                        Yii::$app->session->addFlash('error', "Ошибка, файлы к заказу не прикрепленны.");
                    }


                }
            }else{

            }
        }
        $arrParam=[
            "modelOrder"=>$modelOrder,
            "modelFile"=>$modelFile,
            "modelCreater"=>$modelCreater
        ];

        return $this->render("index",[
            "arrParam"=>$arrParam,
            "layouts"=>"create_order"
        ]);
    }

    public function actionChange($id){

        $post=Yii::$app->request->post();
        $modelOrder=Order::find()
            ->where([
                "id_customer"=>Yii::$app->getUser()->id,
                "id"=>$id
            ])
            ->one();

        if (Yii::$app->request->isAjax && $modelOrder->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelOrder);
        }

        $modelFile= new UploadAdditionalFileOrder;

        if(!$modelOrder->canChangeOrder()){
            return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $id]));
        }


        if( $modelOrder->load($post)){
            $modelOrder->autoSetSpecial();
            if($modelOrder->validate()){
                $modelOrder->save();

                $modelFile->files = UploadedFile::getInstances($modelFile, 'files');
                $modelFile->filesAttached=empty($post["UploadAdditionalFileOrder"]["filesAttached"]) ? null : $post["UploadAdditionalFileOrder"]["filesAttached"];
                if($modelFile->upload($modelOrder)){
                    Yii::$app->session->addFlash('success',"Заказ отредактирован");
                    return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $id]));
                }

            }

            Yii::trace($modelOrder->getErrors());
        }




        $modelCreater=Yii::$app->user->identity;
        if(isset($modelOrder->id_performer) && ( $modelOrder->id_status==1 || $modelOrder->id_status==2)){
            $modelOrder->special=1;
            $modelOrder->idSpecialUser=$modelOrder->id_performer;
        }else {
            $modelOrder->special=0;
        }

        $arrParam=[
            "modelOrder"=>$modelOrder,
            "modelFile"=>$modelFile,
            "modelCreater"=>$modelCreater,
            "change"=>false
        ];

        if(isset($modelOrder)) {
            if ($modelOrder->isCreater() && $modelOrder->canDeleteOrder()) {

            }
        }
        return $this->render("index",[
            "arrParam"=>$arrParam,
            "layouts"=>"create_order"
        ]);
    }

    public function actionDelete($id){//удаление заказчиком заказа
        //добавить проверку заказа
        $modelOrder=Order::find()
            ->where([
                "id_customer"=>Yii::$app->getUser()->id,
                "id"=>$id
            ])
            ->one();
        if(isset($modelOrder)){
            if($modelOrder->isCreater() && $modelOrder->canDeleteOrder()) {

                $modelOrder->cancelStartedOrderCustomer();


                Yii::$app->session->addFlash('success',"Заказ удален");
                return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));
            }else{
                Yii::$app->session->addFlash('error',"На данном этапе нельзя удалить заказ");
                return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $id]));
            }
        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));

    }




    public function actionCancelStartedOrderPerformer($id){//отказ исполнителем на этапе 3 (ожидание оплаты),4 (в работе), 8 (на доработке),  5 (на проверке) не имеет смысла отменять
        $modelOrder=Order::find()
            ->andWhere([
                "id_performer"=>Yii::$app->getUser()->id,

                "id"=>$id
            ])
            ->andWhere(["id_status"=>[3,4,8]])
            ->one();

        if(isset($modelOrder)){
            if($modelOrder->cancelStartedOrderPerformer())
                Yii::$app->session->addFlash('success',"Вы успешно отказались от заказа");
            else
                Yii::$app->session->addFlash('error',"Ошибка при отказе от заказа");


        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");

        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
    }

    public function actionCancelOrderPerformer($id){//отказ на этапе предложения стать исполнителем
        $modelOrder=Order::find()->where(["id_performer"=>Yii::$app->getUser()->id,"id_status"=>2,"id"=>$id])->one();

        if(isset($modelOrder)){
            $modelOrder->cancelOrderPerformer();
            Yii::$app->session->addFlash('success',"Вы успешно отказались от заказа");


        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");

        }
        //return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));
    }

    public function actionAcceptOrderPerformer($id){
		 
		$post=Yii::$app->request->post();
		 
		if(empty($post["performerconditionsafecrow"]) || $post["performerconditionsafecrow"]!="1"){
			 Yii::$app->session->addFlash('error',"Для начала работы по заказу, требуется обязательное согласие с условиями оферты SafeCrow ");
			  return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
		}
        $modelOrder=Order::find()->where(["id_performer"=>Yii::$app->getUser()->id,"id_status"=>2,"id"=>$id])->one();
        $post=Yii::$app->request->post();
        if(isset($modelOrder)){
            if($modelOrder->acceptOrderPerformer($post))
                Yii::$app->session->addFlash('success',"Вы успешно приняли заказ, после резерва денежных средств появится форма для сдачи работы");
            else
                Yii::$app->session->addFlash('error',"Ошибка создание сделки на SafeCrow");

        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");

        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
    }

    public function actionChoicePerformer($id_order,$id_performer){
        $modelOrder=Order::find()
            ->where([
                "id"=>$id_order,
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>1
            ])
            ->one();
        if(isset($modelOrder)){
            if($modelOrder->choisePerformer($id_performer)){
                Yii::$app->session->addFlash('success',"Вы успешно выбрали исполнителя, ожидайте согласия/отказа с условиями заказа");
            }else{
                Yii::$app->session->addFlash('error',"Ошибка при выборе исполнителя");
            }


        }else{
            Yii::$app->session->addFlash('error',"Ошибка при выборе исполнителя");
        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id_order]));
    }


    public function actionNotChoicePerformer($id_order,$id_performer){
        $modelOrder=Order::find()
            ->where([
                "id"=>$id_order,
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>2,
                "id_performer"=>$id_performer
            ])
            ->one();
        if(isset($modelOrder)){
            if($modelOrder->notChoisePerformer($id_performer)){
                Yii::$app->session->addFlash('success',"Вы успешно отменили выбранного исполнителя");
            }else{
                Yii::$app->session->addFlash('error',"Ошибка при отмене исполнителя");
            }


        }else{
            Yii::$app->session->addFlash('error',"Ошибка при отмене исполнителя.");
        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id_order]));
    }


    public function actionCancelApplicationPerformer($id_order){
        $modelOrder=Order::find()
            ->where([
                "id"=>$id_order,
                "id_status"=>1,
            ])
            ->one();
        if(isset($modelOrder)){
            if($modelOrder->cancelApplicationPerformer()){
                Yii::$app->session->addFlash('success',"Вы успешно отказались от участия в конкурсе на роль исполнителя");
            }else{
                Yii::$app->session->addFlash('error',"Ошибка вы не участвовали в конкурсе на роль исполнителя");
            }

        }else{
            Yii::$app->session->addFlash('error',"Ошибка при отказе участия в конкурсе");
        }

        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id_order]));
    }

    public function actionPayOrder($id){
        $modelOrder=Order::find()
            ->where([
                "id"=>$id,
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>3
            ])
            ->one();

        if(isset($modelOrder)){

            $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"],Yii::$app->params["safecrow_api_secret"],Yii::$app->params["safecrow_dev"]);

            $modelSafeCrawOrder=SafeCrowOrder::find()->where(["id_order"=>$modelOrder->id,"status"=>0])->one();

            $result=$modelSafeCrow->payOrder(
                $modelSafeCrawOrder->id_order_safecrow,
                Yii::$app->urlManager->createAbsoluteUrl(['order/pay-order-check',"id_order"=>$modelOrder->id])
            );

            if($result){
                return $this->redirect($result->redirect_url);
            }




        }else{
            Yii::$app->session->addFlash('error',"Ошибка, нельзя оплатить заказ");
        }


        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
    }

    public function actionPayOrderCheck($id_order){
        $id=$id_order;
        $modelOrder=Order::find()
            ->where([
                "id"=>$id,
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>3
            ])
            ->one();

        if(isset($modelOrder)) {
            $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"],Yii::$app->params["safecrow_api_secret"],Yii::$app->params["safecrow_dev"]);
            $modelSafeCrawOrder=SafeCrowOrder::find()->where(["id_order"=>$modelOrder->id,"status"=>0])->one();

            $result=$modelSafeCrow->showOrder($modelSafeCrawOrder->id_order_safecrow );
            if($result && $result->status=="paid"){
                $modelSafeCrawOrder->status=1;
                $modelOrder->id_status = 4;
                $modelOrder->save();
                $modelSafeCrawOrder->save();
                $modelNotification = new \common\models\Notification;
                $modelNotification->autoComplete(0, $modelOrder->id_performer, 7, json_encode(["id_order" => $modelOrder->id]));
                Yii::$app->session->addFlash('success', "Вы успешно оплатили заказ");
            }elseif($result && $result->status=="pending"){
				Yii::$app->session->addFlash('success', "Идет обработка платежа");
			}else{
                Yii::$app->session->addFlash('error',"Ошибка ответа сервера SafeCrow");
            }



        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
        }
        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));

    }




    public function actionAddCheckFileOrder(){

        $modelUploadAdditionalFileCheck=new UploadAdditionalFileToCheckOrder;
        if (Yii::$app->request->isAjax && $modelUploadAdditionalFileCheck->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($modelUploadAdditionalFileCheck);
        }



        $post=Yii::$app->request->post();





        $modelOrder=Order::find()
            ->where([
                "id"=>$post["UploadAdditionalFileToCheckOrder"]["id_order"],
                "id_performer"=>Yii::$app->getUser()->id,

            ])
            ->andWhere(["or",   "id_status"=>4,   "id_status"=>8])
            ->one();

        if(isset($modelOrder)){

            if( $modelOrder->saveAttacheCheckAndSourseFile($post)){
                Yii::$app->session->addFlash('success',"Заказ отправлен на проверку. Ожидается проверка заказчиком");

            }else{
                Yii::$app->session->addFlash('error',"Ошибка при отправки заказа на проверку");
            }




        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
        }



        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$post["UploadAdditionalFileToCheckOrder"]["id_order"]]));
    }


    public function actionOrderFinished($id){
        $modelOrder=Order::find()
            ->where([
                "id"=>$id,
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>5
            ])
            ->one();

        if(isset($modelOrder)){

            if($modelOrder->orderFinished()){
                Yii::$app->session->addFlash('success',"Заказ успешно закрыт. Деньги будут перечисленны исполнителю в близжайшее время");

            }else{
                Yii::$app->session->addFlash('error',"Ошибка завершения заказа");
            }
            return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$id]));
        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
            return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));
        }




    }

    public function actionOrderRevision(){
        $post=Yii::$app->request->post();
        $modelOrder=Order::find()
            ->where([
                "id"=>$post["OrderRevision"]["id_order"],
                "id_customer"=>Yii::$app->getUser()->id,
                "id_status"=>5
            ])
            ->one();
        $modelOrderRevisions=$modelOrder->orderRevisions;

        if(isset($modelOrder) && count($modelOrderRevisions)<3 ){

            if($modelOrder->orderRevision($post)){
                Yii::$app->session->addFlash('success',"Заказ отправлен на доработку");
                return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$post["OrderRevision"]["id_order"]]));
            }else{
                Yii::$app->session->addFlash('error',"Ошибка данных");
            }

        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
        }

        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));

    }



    //начать арбитраж
    public function actionOrderArbitration()
    {
        $post = Yii::$app->request->post();
        if (isset($post["OrderArbitration"]["id_order"])){
            $modelOrder = Order::find()
                ->andWhere([
                    "id" => $post["OrderArbitration"]["id_order"],

                    "id_status" => [8,5]
                ])
                ->andWhere([
                    'or',"id_performer" => Yii::$app->getUser()->id,"id_customer" => Yii::$app->getUser()->id
                ])

                ->one();

            if(isset($modelOrder) && $modelOrder->canStartArbitr()){
                if($modelOrder->startArbitration($post))
                    Yii::$app->session->addFlash('success',"По заказу № ".$post["OrderArbitration"]["id_order"]." начат арбитраж.");
                else
                    Yii::$app->session->addFlash('error',"Произошла ошибка, арбитраж не начат.");


            }else{
                Yii::$app->session->addFlash('error',"Заказ не найден");
            }

        }else{
            Yii::$app->session->addFlash('error',"Заказ не найден");
            return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order']));
        }

        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$post["OrderArbitration"]["id_order"]]));
    }


    public function actionBindCard()
    {

        $get=Yii::$app->request->get();
        $id_order=$get["id_order"];
        if(!isset($id_order)){
            Yii::$app->session->addFlash('error', "Произошла ошибка.");
            return $this->redirect('/order');
        }



        $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"],Yii::$app->params["safecrow_api_secret"],Yii::$app->params["safecrow_dev"]);
        $modelUser=Yii::$app->getUser()->identity;
        $modelUserSaveCrow=$modelUser->safeCrowUser;
        Yii::trace("1");
        Yii::trace($modelUserSaveCrow);
        if(!isset($modelUserSaveCrow)){
            if($modelUser->registrationSafeCrow()){
                Yii::trace("2");
                unset($modelUser->safeCrowUser);
                $modelUserSaveCrow=$modelUser->safeCrowUser;
                Yii::trace($modelUserSaveCrow);
            }else{
                Yii::$app->session->addFlash('error', "Ошибка регистрации на сервисе SafeCrow");
                return $this->redirect('/order/view/'.$id_order);
            }
        }

        $result=$modelSafeCrow->bindCardOnUser(
            $modelUserSaveCrow->id_user_safe_crow,
            Yii::$app->urlManager->createAbsoluteUrl(['order/view/'.$id_order,'_'=> time()])
            //Yii::$app->urlManager->createAbsoluteUrl(['order'])

        );

        if($result && isset($result->redirect_url)){
            //Yii::$app->session->addFlash('success', "Карта успешно привязана, если она не отображается в списке обновите страницу");

            return $this->redirect($result->redirect_url);
        }else{
            Yii::$app->session->addFlash('error', "Произошла ошибка, попробуйте позже");
            return $this->redirect('/order/view/'.$id_order);
        }


    }




}
