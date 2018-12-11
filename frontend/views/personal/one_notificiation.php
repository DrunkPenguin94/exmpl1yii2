<?php
/* @var $this yii\web\View */
?>

    <div class="notification-block <?=$model->viewed==1 ? "new" : ""?>">



        <?if($model->type==0){ //оповещнеипо заказу
            $arrValue=json_decode($model->value);
            $id_order=$arrValue->id_order;

            ?>
                <a class="link-on-order"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $id_order]) ?>">
                    <span class="order">Заказ № <?= $id_order ?>:</span>
                    <span class="date">(<?= date("H:i:s d.m.Y", strtotime($model->date_create)) ?>)</span>
                    <? if ($model->viewed == 1) { ?>
                        <span class="new-notification-flag">Новое</span>
                    <?
                    } ?>
                </a>
                <div class="user-info-name"><?= $model->idText->text ?></div>
                <div class="button-link-on-order"><a
                            href="<?= Yii::$app->urlManager->createAbsoluteUrl(['order/view', "id" => $id_order]) ?>">Перейти
                        к заказу</a></div>


        <?}elseif($model->type==1){
            $arrValue=json_decode($model->value);
            $rating=$arrValue->rating;
            $text=str_replace("%rating%",$rating,$model->idText->text);

            ?>
            <div class="link-on-order">
                <span class="order">Рейтинг :</span>
                <span class="date">(<?= date("H:i:s d.m.Y", strtotime($model->date_create)) ?>)</span>
                <? if ($model->viewed == 1) { ?>
                    <span class="new-notification-flag">Новое</span>
                <?}?>
            </div>
            <div class="user-info-name"><?=$text ?></div>
        <?}?>

    </div>


