<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 14.09.2017
 * Time: 16:47
 */
use yii\helpers\HtmlPurifier;
use frontend\models\StatusOnline;

$text_message=HtmlPurifier::process($modelMessage["text"]);
if($mod=='prev') {
    $text_point_end = "";
    if (strlen($text_message) > 100)
        $text_point_end = "...";
    $text_message = substr($text_message, 0, 100);
    $text_message = $text_message . $text_point_end;


}
$modelStatusOnline=new StatusOnline;

$dateText=$modelStatusOnline->transformDateMessage($modelMessage["date_create"]);
?>
<?if($mod!='full') {?>
<a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/correspondence',"user"=>$modelMessage["id_user_from"]])?>">
<?}?>
    <div class="review-wrap messages"  id_from="<?=$modelMessage["id_user_from"]?>"   <?=$mod=='full' ? "id_message='".$modelMessage->id."'" : ""?>>
        <div class="user-avatar">
            <div class="user-info-icon">
                <img src="<?=$modelUser->avatarUrlMini?>" >
            </div>
        </div>
        <div class="user-review messages">
            <?if($mod=='full') {?>
                <a target="_blank" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$modelUser->id])?>">
            <?}?>
                <div class="user-name c-view active-mes"><?=$modelUser->fil?></div>
            <?if($mod=='full') {?>
                </a>
            <?}?>
            <div class="review-time messages-num messages">
                <?if(isset($modelMessage["new_count"])){?>
                <i class="fa fa-envelope" aria-hidden="true"></i><span><?=$modelMessage["new_count"]?></span>
                <?}?>

            </div>

            <div class="review-time messages"><i class="fa fa-clock-o" aria-hidden="true"></i><span><?=HtmlPurifier::process($dateText)?></span></div>
            <?if($mod=='prev') {?>
            <div class="mes-status"><?=$modelUser->statusOnline?><span></span></div>
            <?}?>
            <div class="review-text"><?=$text_message?></div>
        </div>
    </div>
<?if($mod!='full') {?>
</a>
<?}?>
