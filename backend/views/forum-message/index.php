<?php
use yii\helpers\Url ;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ForumMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пост № '.$modelForumMessage->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forum-message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= Html::a('Назад', ['/forum/index',"ForumFolderSearch[id_parent]"=>$modelForumMessage->id_parent], ['class' => 'btn btn-primary']) ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'text',
                'contentOptions' => ['class' => 'text-center word-wrap'],
                'headerOptions' => ['class' => 'text-center word-wrap ']
            ],
            [
                'attribute' => 'id_user',
                'format'=>'raw',
                'value'=>function($data){
                    return   Html::a(
                        $data->author->username,
                        Url::to(['user/view/'.$data->id_user]),
                        [
                            'title' => '',
                            'data-pjax'=>"0"
                        ]
                    );

                }
            ],
            'id_post',
            'date_create',
            // 'id_user_from',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
