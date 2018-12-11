<?php
/* @var $this yii\web\View */
$this->title = $model->title;
?>

<div class="fixed-footer_container">
    <div class="grey-container">
        <div class="main-container">
            <div class="content-container">

                <div class="content-list">
                    <div class="content-title"><?=$model->title?></div>
                    <div class="rules-cont">
                        <?=$model->text?>
                    </div>
                </div>


            </div>

            <?= frontend\components\AdvertisingWidget::widget(["count"=>3]) ?>
        </div>

    </div>
</div>
