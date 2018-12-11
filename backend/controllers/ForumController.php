<?php

namespace backend\controllers;

use Yii;
use common\models\ForumMessage;
use common\models\ForumFolder;
use common\models\ForumFolderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\NewMessageForum;
use yii\filters\AccessControl;

/**
 * ForumController implements the CRUD actions for ForumFolder model.
 */
class ForumController extends Controller
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
     * Lists all ForumFolder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $get=Yii::$app->request->get();
        $searchModel = new ForumFolderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->orderBy('type,mass desc, date_create desc');

        $id_=isset($get["ForumFolderSearch"]["id_parent"]) ? $get["ForumFolderSearch"]["id_parent"] : "";
        $backFolder=ForumFolder::find()->where(['id'=>$id_])->one();


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'backFolder'=>$backFolder
        ]);
    }

    /**
     * Displays a single ForumFolder model.
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
     * Creates a new ForumFolder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ForumFolder();
        $post=Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post()) && $post['ForumFolder']['type']==0) {
            if(isset($post["ForumFolder"]["id_parent"]) && $post["ForumFolder"]["id_parent"]!='') {
                $modelParent = ForumFolder::find()->where(["id" => $post["ForumFolder"]["id_parent"]])->one();
                $level=$modelParent->level+1;
            }else{
                $level=0;
            }

            $model->level=$level;
            $model->date_create=date("Y-m-d H:i");
            if ($model->validate()) {

                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::trace($model->getErrors());
            }
        }elseif($model->load(Yii::$app->request->post()) && $post['ForumFolder']['type']==1){
            $modelNewMessageForum=new NewMessageForum;
            $modelNewMessageForum->theme=$model->name;
            $modelNewMessageForum->text=$model->info;
            $modelNewMessageForum->id_parent=$model->id_parent;
            $modelNewMessageForum->id_user=Yii::$app->getUser()->id;
            $result=$modelNewMessageForum->createTheme();
            if(isset($result))
                return $this->redirect(['view', 'id' => $result->id_post]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ForumFolder model.
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
     * Deletes an existing ForumFolder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = ForumFolder::find()->where(["id"=>$id])->one();
        $id_parent= $model->id_parent;
        ForumMessage::deleteAll('id_post = '.$id);

        $this->findModel($id)->delete();
        if(isset($id_parent))
            //return $this->redirect('index?ForumFolderSearch[id_parent]='.$id_parent);
            return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['forum/',"ForumFolderSearch[id_parent]"=>$id_parent]));
        else
            return $this->redirect(['index']);
    }

    /**
     * Finds the ForumFolder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForumFolder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForumFolder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
