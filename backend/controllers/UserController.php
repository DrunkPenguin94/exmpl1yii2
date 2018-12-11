<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserSearch;
use backend\models\EditInfoUser;
use backend\models\MailingLetters;
use backend\models\EmailingLetters;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoadEmailImg;
use yii\web\UploadedFile;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionBlockUser($id)
    {
        $model = User::find()->where(["id"=>$id])->one();
        if(isset($model)){
            $model->status=0;
            $model->save();
            Yii::$app->session->setFlash('success',"Пользователь заблокирован");
        }else{
            Yii::$app->session->setFlash('error',"Ошибка, пользователь не найден");
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionActivationUser($id)
    {

        $model = User::find()->where(["id"=>$id])->one();
        if(isset($model)){
            $model->status=10;
            $model->save();
            Yii::$app->session->setFlash('success',"Пользователь разблокирован");
        }else{
            Yii::$app->session->setFlash('error',"Ошибка, пользователь не найден");
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $modelEditInfoUser=new EditInfoUser;
        $model = $this->findModel($id);
		$modelEditInfoUser->id=$id;
        if ($modelEditInfoUser->load(Yii::$app->request->post()) && $modelEditInfoUser->validate()) {

            if($modelEditInfoUser->update($id)){
				return $this->redirect(['view', 'id' => $model->id]);
			}else{
				return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['user/update',"id"=>$id]));
			}
			
            
        } else {
            $modelEditInfoUser->loadData($model);
            return $this->render('update', [
                'model' => $model,
                'modelEditInfoUser'=>$modelEditInfoUser
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionSetAdmin($id)
    {
        $modelUser = User::findOne($id);
        if(isset($modelUser)){
            $modelUser->is_admin=1;
            $modelUser->save();
        }

        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['user/view',"id"=>$id]));
    }

    public function actionOffAdmin($id)
    {
        $modelUser = User::findOne($id);
        if(isset($modelUser)){
            $modelUser->is_admin=0;
            $modelUser->save();
        }

        return $this->redirect(Yii::$app->urlManager->createAbsoluteUrl(['user/view',"id"=>$id]));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionMailingLetters(){
		
		$post=Yii::$app->request->post();
		$mailingLetters = new MailingLetters;
		$modShow=false;
		
		if($mailingLetters->load($post) && $mailingLetters->validate()){
			if($mailingLetters->start()){
				Yii::$app->session->setFlash('success',"Рассылка запущена");
				$modShow=true;
			}else{
				Yii::$app->session->setFlash('error',"Ошибка рассылки");
			}
		}else{
			Yii::$app->session->setFlash('error',"Ошибка отправки");
		}
		
		
		
		return $this->render('mailing-letters', [
		   "mailingLetters"=>$mailingLetters,
		   "mod"=>$modShow
		]);
	}
	
	public function actionEmailingLetters(){
		
		$post=Yii::$app->request->post();
		$emailingLetters = new EmailingLetters;
		$loadEmailImg = new LoadEmailImg;
		$modShow=false;
		
		
		if($emailingLetters->load($post) && $emailingLetters->validate()){
			$loadEmailImg->imageFile = UploadedFile::getInstance($loadEmailImg, 'imageFile');
			$name=$loadEmailImg->upload();
            if ($emailingLetters->start($name)) {
               Yii::$app->session->setFlash('success',"Рассылка запущена");
			   $modShow=true;
            }else{
				Yii::$app->session->setFlash('error',"Ошибка рассылки");
			}
		}else{
			Yii::$app->session->setFlash('error',"Ошибка отправки");
		}
		
		return $this->render('emailing-letters', [
			"model"=>$emailingLetters,
			"modelFile"=>$loadEmailImg,
			"mod"=>$modShow
		]);
	}
}
