<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\models\LastMessage;
use frontend\components\AdvertisingWidget;
$this->title = $title;

$modelCountMessageNew=new LastMessage;
$countNewMessage=Yii::$app->session->get('countNewMessage');
$countNewNorification=$userModel->countNewNotifications;

if(empty($stranger)) $stranger=false;

?>



<div class="fixed-footer_container">
    <div class="grey-container">
        <div class="main-container">

            <?php if(Yii::$app->session->hasFlash('success')):
                $flash=Yii::$app->session->getFlash('success');
                foreach ($flash as $value){
                    ?>
                    <div class="flash-message-block">
                        <?  echo $value;?>
                    </div>
                <?}?>
            <?  endif; ?>


            <?php if(Yii::$app->session->hasFlash('error')):
                $flash=Yii::$app->session->getFlash('error');
                foreach ($flash as $value){
                    ?>
                    <div class="flash-message-block error">
                        <? echo $value;?>
                    </div>
                <?}?>
            <?php endif; ?>

            <?if($stranger!=true){?>
            <div class="personal_area">
                <div class="content-title">Личный кабинет</div>
                <ul class="personal_area-items">
                    <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal'])?>"><li class="personal_info <?=$block=="personal_block"? " active-li" : ""?>">Личные данные</li></a>
                    <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/profile'])?>"><li class="profile <?=$block=="profile_block"? " active-li" : ""?>">Профиль</li></a>
                    <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/all-messages'])?>"> <li class="messages <?=$block=="all_message" || $block=="correspondence" || $block == "new_correspondence"? " active-li" : ""?>">Личные сообщения <span id="count-menu-personal-message"><?=$countNewMessage!=0 ? "(".$countNewMessage.")" : ""?></span></li></a>
                    <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/notifications'])?>"><li class="personal_info <?=$block=="notifications"? " active-li" : ""?>">Уведомления <span><?=$countNewNorification!=0 ? "(".$countNewNorification.")" : ""?></span></span></li></a>
                </ul>
                <div class="green-button block-create-message-lk">
                    <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/create-message'])?>" class="create-message-lk">Написать сообщение</a>
                </div>
            </div>
            <?}?>
            <div class="content-container">

               <?=$this->render($block,$arrayModel)?>


            </div>


            <div class="left-block-content">
                <?= AdvertisingWidget::widget(["count"=>3]) ?>
            </div>
        </div>
        <div class="big-img-fon hidden">
            <div class="content-list big-img-container">
                <div class="content-title">Портфолио

                </div>
                <img src="/img/cancel.png" class="big-img-cancel"/>
                <div class="big-img-wrap">
                    <img class="big-img" src=""/>

                </div>
                <img class="str-left" src="/img/str-left.png"/>
                <img class="str-right"  src="/img/str-right.png"/>

            </div>
        </div>


    </div>
</div>