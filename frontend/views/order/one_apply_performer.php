<?php
/* @var $this yii\web\View */


use yii\helpers\HtmlPurifier;

$modelUser=$model->performer;

?>
<div class="responded-line">
    <div class="order-holder responded">

        <div class="user-info-icon responded">
            <a class="hide-link" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$modelUser->id])?>" target="_blank">
                <img src="<?=$modelUser->avatarUrlMini?>" />
            </a>
        </div>
        <div class="user-info-name-block responded">
            <a class="hide-link" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$modelUser->id])?>" target="_blank">
                <div class="user-info-name responded"><?=$modelUser->fil?></div>
            </a>

            <div class="user-info-reviews responded">
                <span>Отзывы: </span><i class="fa fa-thumbs-o-up fa-1" aria-hidden="true"></i>&nbsp;
                <div class="user-reviews"><?=$modelUser->sumReviews?></div>
            </div>
            <div class="user-info-text responded">
                <?=HtmlPurifier::process($model->info_text)?>
            </div>
        </div>
    </div>
    <?if($modelOrder->id_status==1){?>
        <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/choice-performer',"id_performer"=>$modelUser->id,"id_order"=>$model->id_order])?>">
            <button class="appoint-b order-button">Назначить</button>
        </a>
    <?}else{
        if($model->status==1){
    ?>
        <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/not-choice-performer',"id_performer"=>$modelUser->id,"id_order"=>$model->id_order])?>">
            <button class="appoint-b order-button orange">Отменить </button>
        </a>
    <?  }

    }?>
</div>