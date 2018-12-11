<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 06.10.2017
 * Time: 9:13
 */
use yii\helpers\HtmlPurifier;
$text_message=HtmlPurifier::process($model->info);

$text_point_end = "";
if (strlen($text_message) > 100)
    $text_point_end = "...";
$text_message = substr($text_message, 0, 100);
$text_message = $text_message . $text_point_end;

?>
<?if($model->type==0){?>
<a class="link-forum-folder" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['forum/index',"idFolder"=>$model->id])?>">
<?}else{?>
<a class="link-forum-folder" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['forum/post',"idPost"=>$model->id])?>">
<?}?>
    <div class="review-wrap messages"  id_folder="<?=$model->id?>">
        <div class="forum-folder-avatar">
            <div class="forum-folder-info-icon">
                <?if($model->type==0){?>
                    <img src="/img/papka.png" >
                <?}else{?>
                    <img src="/img/post.png" >
                <?}?>
            </div>
        </div>
        <div class="user-review messages">

            <div class="user-name c-view active-mes"><?=HtmlPurifier::process($model->name)?></div>



            <?if($model->type==1){?>
                <div class="forum-message-time messages">
                    <i class="fa fa-clock-o" aria-hidden="true"></i><span><?=date("d.m.Y",strtotime($model->date_create))?></span><br>
                    <i class="fa fa-envelope-open-o" aria-hidden="true"></i><span>Сообщений : <?=count($model->messages)?></span>
                </div>

            <?}?>
            <div class="review-text"><?=HtmlPurifier::process($text_message)?></div>
        </div>
    </div>
</a>