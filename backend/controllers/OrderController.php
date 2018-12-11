<?php

namespace backend\controllers;

use Yii;
use common\models\Order;
use common\models\OrderSearch;
use common\models\FilesOrder;
use backend\models\CommenceArbitration;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\StatusOnline;


/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
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
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	public function actionDownload($id = null)
    {

        if(!isset(Yii::$app->getUser()->id)){
            throw new NotFoundHttpException('User not found');
        }
//        if (!$id) {
//            throw new NotFoundHttpException('File not found');
//        }
//
//        if (($model = Product::find()->where(['id' => $id])->one()) == NULL) {
//            throw new NotFoundHttpException('File not found');
//        }
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
    public function actionArbitr()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(["id_status"=>9]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $modelCommenceArbitration=new CommenceArbitration;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'modelCommenceArbitration'=>$modelCommenceArbitration
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionResultArbitration()
    {
        $post=Yii::$app->request->post();
        if(isset($post["CommenceArbitration"]["id_order"])){
            $modelOrder=Order::find()->where([
                "id"=>$post["CommenceArbitration"]["id_order"],
                "id_status"=>9
            ])->one();
            if(isset($modelOrder)){

                if($modelOrder->resultArbitration($post))
                    Yii::$app->session->setFlash('success',"Арбитраж закрыт");
                else
                    Yii::$app->session->setFlash('error',"Результат арбитража не сохранен");
            }else{
                Yii::$app->session->setFlash('error',"Заказ с арбитражем не найден");
            }
        }else{
            Yii::$app->session->setFlash('error',"Нет параметров заказа");
        }


        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$post["CommenceArbitration"]["id_order"]]));
    }
}
