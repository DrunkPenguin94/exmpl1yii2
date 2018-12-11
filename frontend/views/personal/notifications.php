<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 14.09.2017
 * Time: 16:43
 */
use common\models\User;
use frontend\models\LastMessage;
use yii\helpers\HtmlPurifier;
/* @var $this yii\web\View */
use yii\widgets\ListView;


?>

<div class="content-list">
    <div class="content-title" >Уведомления</div>

    <div class="container-notification">
        <?php
        echo ListView::widget([
            'dataProvider' => $modeNotification,
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('one_notificiation.php', ['model' => $model]);
            },
            'id'=>'list-order',
            'itemOptions' => [
                'class' => 'notification-line',
            ],
            'layout' => "{items}\n{pager}",
            'emptyText' => 'Список пуст',
            'pager' => [
                'nextPageLabel' => 'Следующая >',
                'prevPageLabel' => '< Предыдущая',
                'maxButtonCount' => 0,
            ],
        ]);
        ?>
    </div>
</div>