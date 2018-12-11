<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 14.09.2017
 * Time: 16:43
 */
use common\models\User;
use frontend\models\LastMessage;


?>


<div class="content-list">
    <div class="content-title">Сообщения (<span><?=$countAllMessage?></span>)</div>
    <div class="messages-search" >
        <label class="messages-search-label">Поиск</label>
        <input type="search"  name="messages-search-line" class="messages-search-input" required id="messages-search-input"/>
        <button class="messages-search_b"><i class="fa fa-search" aria-hidden="true"></i></button>
    </div>

    <div class="reviews-cont messages">
        <?if(isset($modelMessage)) {

            foreach ($modelMessage as $modelMessageOne) {

                $modelUser=User::find()->where(["id"=>$modelMessageOne["id_user_from"]])->one();
        ?>

                <?= $this->render("one_message_prev", [
                    "modelMessage"=>$modelMessageOne,
                    "modelUser"=>$modelUser,
                    "mod"=>"prev"
                ]) ?>
        <?

            }

        }
        ?>

    </div>
    <div class="green-button block-load-message-all">
        <button class="load-message-all" style="<?= $countAllMessage<=5 ? "display:none;":""?>">Еще</button>
    </div>
</div>
