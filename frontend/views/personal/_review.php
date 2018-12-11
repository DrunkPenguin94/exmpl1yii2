<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 11.09.2017
 * Time: 15:32
 */
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<?php

foreach($modelReviews as $modelReview){
?>
<div class="review-wrap" id_review="<?=$modelReview->id?>">
    <div class="user-avatar">
        <div class="user-info-icon">
            <img src="<?=$modelReview->idFromUser->avatarUrlMini?>"/>
        </div>
    </div>
    <div class="user-review">
        <div class="user-name c-view"><a target="_blank" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$modelReview->idFromUser->id])?>"><?=$modelReview->idFromUser->fil?></a></div>
        <div class="review-time"><i class="fa fa-clock-o" aria-hidden="true"></i><span><?=$modelReview->dateRus?></span></div>
        <div class="review-text"><?=HtmlPurifier::process($modelReview->text)?></div>
    </div>
</div>
<?}?>