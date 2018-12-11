<?php
/* @var $this yii\web\View */

$get=Yii::$app->request->get();
?>
<div class="status-order <?=$model->id_status!=1 && ( empty($get["type"]) || $get["type"]=="all"  )? "work" : ""?> ">
    <div class="order-now-row-block order-now-left-block">
        <a class="link-on-order" data-pjax=0  href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$model->id])?>">
            <div class="order-now-row-title orders viewed"><?=$model->name?></div>
        </a>
        <div class="order-now-user-info">
            <div class="user-info-icon">
                    <img src="<?=$model->customer->avatarUrlMini?>" >
            </div>
            <div class="user-info-name-block">
<!--                <a class="hide-link" href="--><?//=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$model->customer->id])?><!--" target="_blank">-->
                <a class="hide-link" data-pjax=0  href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/view',"id"=>$model->id])?>">
                    <div class="user-info-name"><?=$model->customer->fil?></div>
                </a>
                <div class="user-info-reviews">
                    <span>Отзывы: </span>
                    <div class="user-reviews"><?=$model->customer->sumReviews?></div>
                </div>
                <div>
                    <span>Статус: </span><?=$model->statusForPreview()?>
                </div>
            </div>

        </div>
    </div>
    <div class="order-now-row-block order-now-right-block">
        <div class="order-details">
            <div class="order-detail-wrap">
                <img src="/img/money.png"><div class="order-details-money order-details-text"><?=$model->price?> <i class="fa fa-rub" aria-hidden="true"></i></div>
            </div>
            <div class="order-detail-wrap">
                <i class="fa fa-calendar-check-o" aria-hidden="true"></i><div class="order-details-time  order-details-text"><?=$model->dateDeadline?> <?=$model->timeDeadline?></div>
            </div>
            <?php
                if($model->id_type==1){
                    ?>
                    <div class="urgency order-detail-wrap ">
                        <img src="/img/quick.png" >
                        <div class="order-details-urgency  order-details-text"><?=$model->typeOrder->name?></div>
                    </div>
                    <?php
                }
            ?>
            <div class="period">
                <?=$model->dateCreateText?>
            </div>
        </div>
    </div>
</div>
