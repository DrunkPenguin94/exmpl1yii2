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

$fi=mb_ucfirst(HtmlPurifier::process($modelUser->surname))." ".mb_ucfirst(HtmlPurifier::process($modelUser->name));
if(!isset($modelUser->surname)||!isset($modelUser->name))
    $fi="&nbsp;";
Yii::trace($fi);

?>

<div class="content-list">
    <div class="content-title" id="user_dialog" id_user="<?=$modelUser->id?>">Диалог с <?=$modelUser->fil?></div>

    <div class="interlocutor-status"><?=$modelStatus->status?></div>
    <div class="yet-message" style="<?=count($modelMessage)<5 ? "display:none;":""?>">Еще</div>
    <div class="reviews-cont messages correspondence">
        <?if(isset($modelMessage)) {
            foreach ($modelMessage as $modelMessageOne) {
                if($modelMessageOne->id_user_from==$modelMy->id)
                    $modelUserMessage=$modelMy;
                else
                    $modelUserMessage=$modelUser;
                ?>

                <?= $this->render("one_message_prev", [
                    "modelMessage"=>$modelMessageOne,
                    "modelUser"=>$modelUserMessage,
                    "mod"=>"full"
                ]) ?>
                <?
            }
        }
        ?>


    </div>
    <div class="block-send-message">
        <div class="user-avatar user-info-icon">
            <img src="<?=$modelMy->avatarUrlMini?>"/>
        </div>
        <div class="user-review  messages">
            <div class="answer-form">
                <textarea id="write-message" type="text" name="answer-input" class="answer-input" value=""></textarea>
                <div class="submit-wrap green-button correspondence">
                    <button class="feedback_b send_message">Отправить</button>
                </div>
            </div>
        </div>
    </div>
</div>