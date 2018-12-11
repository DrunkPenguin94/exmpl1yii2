<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?//= Html::a('Создать пользователя', ['Создать'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            [
                'label' => 'Тип',
//                'value' => $model->new==0?"Новое":"Прочитано",
                'content'=>function($data){
                    return $data->typeUser->name;
                }
            ],
            'name:ntext',
            'surname:ntext',
            'patronymic:ntext',
            // 'telephone:ntext',
            // 'email:email',
            [
                'label' => 'Статус',
//                'value' => $model->new==0?"Новое":"Прочитано",
                'content'=>function($data){
                    return $data->status==0? "Заблокирован":"Активирован";
                }
            ],
            [
                'label' => 'Онлайн',
//                'value' => $model->new==0?"Новое":"Прочитано",
                'content'=>function($data){
                    return $data->getStatusOnline();
                }
            ],
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
