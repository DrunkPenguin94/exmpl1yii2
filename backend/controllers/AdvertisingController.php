<?php

namespace backend\controllers;

use Yii;
use common\models\Advertising;
use common\models\AdvertisingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\LoadAdvertising;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

/**
 * AdvertisingController implements the CRUD actions for Advertising model.
 */
class AdvertisingController extends Controller
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

    /**
     * Lists all Advertising models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdvertisingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Advertising model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Advertising model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Advertising();
        $modelFile = new LoadAdvertising();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {



            $modelFile->imageFile = UploadedFile::getInstance($modelFile, 'imageFile');
            if ($modelFile->upload($model)) {
                // file is uploaded successfully
                Yii::trace("ploho");
            }


            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelFile'=>$modelFile
            ]);
        }
    }

    /**
     * Updates an existing Advertising model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelFile = new LoadAdvertising();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if(isset($_FILES["LoadAdvertising"]) && $_FILES["LoadAdvertising"]["error"]["imageFile"]==0){
                $modelFile->imageFile = UploadedFile::getInstance($modelFile, 'imageFile');
                if ($modelFile->upload($model)) {
                    // file is uploaded successfully
                   // if(file_exists($path . $modelFileOrder->name . '.' . $file->extension)){
                }

            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'modelFile'=>$modelFile
            ]);
        }
    }

    /**
     * Deletes an existing Advertising model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Advertising model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Advertising the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Advertising::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
