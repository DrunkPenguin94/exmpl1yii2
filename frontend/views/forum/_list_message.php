<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 06.10.2017
 * Time: 9:13
 */
use yii\helpers\HtmlPurifier;
use common\models\User;
$modelUser=$model->author;

$nameForm="";
if(isset($model->id_user_from)){
    $modelUserFrom=$model->userFrom;
    $nameForm=mb_ucfirst(HtmlPurifier::process($modelUser->name)).", ";
}

?>



    <div class="review-wrap messages"  id_message="<?=$model->id?>">
        <div class="forum-folder-avatar">
            <div class="user-avatar">
                <div class="user-info-icon">
                    <img src="<?=$modelUser->avatarUrlMini?>" >
                </div>
            </div>
        </div>
        <div class="user-review messages">
            <a  href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/view-people',"id_user"=>$modelUser->id])?>">
                <div class="user-name c-view active-mes"><?=$modelUser->fil?></div>
            </a>



                <div class="forum-message-time messages">
                    <i class="fa fa-clock-o" aria-hidden="true"></i><span><?=date("d.m.Y H:i",strtotime($model->date_create))?></span>

                </div>
            <div class="review-text">
                <?=$nameForm?>
                <?=HtmlPurifier::process($model->text)?>
            </div>
        </div>
        <div class="user-message-panel messages">
            <div class="block-message-forum  block-load">
                <button class="otvet" id_people="<?=$model->id_user?>" name="<?=$modelUser->fil?>" >Ответить</button>
            </div>
            <div class="block-message-forum-rating">
                Рейтинг : <?=$modelUser->rating?>
            </div>


        </div>
    </div>

