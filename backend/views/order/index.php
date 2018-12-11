<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Order;
/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name_text:ntext',
            //'info_text:ntext',
            'date_create',
            //'id_customer',


            [
                'attribute'=>'id_status',
                'label'=>'Статус',
                'filter' => Order::getStatusList(),
                'content'=>function($data){
                    return $data->status->name;
                }
            ],
            'price',
            // 'id_type',
            // 'date_deadline',
            // 'id_performer',
            // 'show_pre_source',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',],
        ],
    ]); ?>
</div>
