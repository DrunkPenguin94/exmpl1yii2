<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\DocumentPagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страницы с документами';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-pages-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title:ntext',

            [
                'attribute' => 'version',
//                'value' => function($model){return strip_tags(substr($model->version,0,400))." ...";},
//                'headerOptions' => ['width' => '200px'],
//                //'contentOptions' => ['width' => '200px'],
//                'contentOptions' =>['style'=> 'max-width:200px;white-space:pre-wrap;'],
            ],
            [
                'attribute' => 'text',
                'headerOptions' => ['width' => '400px'],
                'value' => function($model){return strip_tags(substr($model->text,0,400))." ...";},
                //'contentOptions' => ['width' => '200px'],
                'contentOptions' =>['style'=> 'max-width:400px;white-space:pre-wrap;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}  {update}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
