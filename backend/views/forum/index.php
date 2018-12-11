<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url ;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ForumFolderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Форум';
$this->params['breadcrumbs'][] = $this->title;

$get=Yii::$app->request->get();
$id_parent=null;
if(isset($get["ForumFolderSearch"]["id_parent"]))
   $id_parent=$get["ForumFolderSearch"]["id_parent"];

if(isset($backFolder))
    $id_back_page=$backFolder->id_parent;
?>
<div class="forum-folder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать пост / папку', ['create',"id_parent"=>$id_parent], ['class' => 'btn btn-success']) ?>
    </p>
    <?if(isset($backFolder)){?>
    <p>
        <?= Html::a('Назад', ['index',"id_parent"=>$id_back_page], ['class' => 'btn btn-primary']) ?>
    </p>
    <?}?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format'=>'raw',
                'value'=>function($data){
                        if($data->type==0)
                            $link=Url::to(['?ForumFolderSearch[id_parent]='.$data->id]);
                        else
                            $link= Url::to(['/forum-message?id_post='.$data->id]);
                        return  Html::a(
                            $data->name,
                            $link,
                            [
                                'title' => '',
                                'data-pjax'=>"0"
                            ]
                        );

                }
            ],
            [
                'attribute' => 'info',
                'format'=>'raw',
                'value'=>function($data){

                    return  Html::a(
                        mb_strimwidth($data->info, 0, 30, "..."),
                        Url::to(['?ForumFolderSearch[id_parent]='.$data->id]),
                        [
                            'title' => '',
                            'data-pjax'=>"0"
                        ]
                    );
                }
            ],
            'level',
             'mass',
             'date_create',
            [
                'label'=>'Тип',
                'value'=>function($data) {
                    if($data->type == 1) return "Пост";
                    else return "Папка";
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>"{view}{update}{delete}"
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
