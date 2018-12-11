<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?if($model->is_admin==1){?>
            <?= Html::a('Отменить права администратора', ['off-admin', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        <?}else{?>

             <?= Html::a('Сделать администратором', ['set-admin', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?}?>
<!--        --><?//= Html::a('Удалить', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Вы точно хотите удалить пользователя?',
//                'method' => 'post',
//            ],
//        ]) ?>
    </p>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
//            'auth_key',
//            'password_hash',
//            'password_reset_token',
            [
                'label' => 'Тип',
                'value' => $model->typeUser->name

            ],
            'name:ntext',
            'surname:ntext',
            'patronymic:ntext',
            'telephone:ntext',
            'email:email',
            [
                'label' => 'Статус',
                'value' => $model->status==0? "Заблокирован":"Активирован",

            ],
            'rating:ntext',
            'skill:ntext',
            [
                'label' => 'Последняя активность',
                'value' => $model->last_online. " (".$model->getStatusOnline().")",

            ],
            [
                'label' => 'Зарегистрирован',
                'value' => date("d.m.Y",$model->created_at)

            ],
            [

                'label' => 'Администратор',
                'value' => $model->is_admin==1 ?"Да":"Нет"

            ],
			[
                'label' => 'Рассылка E-mail',
                'format' => 'raw',

                "value" => $model->is_send_email=='1' ? "Согласие на рассылку" :"Отказ от рассылки" ,
            ],

        ],
    ]) ?>
    <p>
        <?php
        if($model->status==10){
            ?>
            <?= Html::a('Заблокировать пользователя', ['block-user', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
            <?
        }else{
            ?>
            <?= Html::a('Активировать пользователя', ['activation-user', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?
        }
        ?>
    </p>

</div>
