<?php

/* @var $this yii\web\View */

use yii\widgets\ActiveForm;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use common\models\ForumFolder;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
$this->title = 'Форум';



?>



<div class="fixed-footer_container forum">
    <div class="grey-container">
        <div class="main-container">

            <div class="content-container">
                <div class="content-list">

                    <div class="content-title">
                        <div class="title-forum">
                            <?if(Yii::$app->controller->action->id!="post"){?>
                                Форум <?=isset($modelBack->name)? "<br>Раздел \"".$modelBack->name."\"" : ""?>
                            <?}else{?>
                                Обсуждение (<?=$modelBack->name?>)
                            <?}?>
                        </div>
                        <?php

                        if(isset($modelBack)){
                            if(isset($modelBack->parentFolder))
                                $nameBackFolder=$modelBack->parentFolder->name;
                            else
                                $nameBackFolder=null;

                        ?>
                        <div class="back-folder" >

                            <a  href="<?=Yii::$app->urlManager->createAbsoluteUrl(['forum/index',"idFolder"=>$modelBack->id_parent])?>">Назад <?=isset($nameBackFolder)?"(".$nameBackFolder.")":""?></a>
                        </div>
                        <?}?>
                    </div>


                    <div class="reviews-cont messages">
                    <?if(Yii::$app->controller->action->id!="post"){?>
                        <?=     ListView::widget([
                            'dataProvider' => $dataProviderFolder,
                            'itemView' => '_list_folder',
                            'layout' => "\n{items}\n{pager}\n{summary}",
                            'options' => [
                                'id' => 'forum-folder',
                            ],
                            'pager' => [
                                'nextPageLabel' => '<img src="/img/str-right.png">',
                                'prevPageLabel' => '<img src="/img/str-left.png">',
                                'maxButtonCount' => 5,
                            ],
                        ])?>
                    <?}else{?>
                        <?=     ListView::widget([
                            'dataProvider' => $dataProviderMessage,
                            'itemView' => '_list_message',
                            'layout' => "\n{items}\n{pager}\n{summary}",
                            'options' => [
                                'id' => 'forum-message',
                            ],
                            'pager' => [
                                'nextPageLabel' => '<img src="/img/str-right.png">',
                                'prevPageLabel' => '<img src="/img/str-left.png">',
                                'maxButtonCount' => 5,
                            ],
                        ])?>
                    <?}?>
                    </div>

                </div>
                <?if(isset($modelBack->id)){?>
                <div class="content-list">
                    <div class="content-title title-forum-send"><?=Yii::$app->controller->action->id!="post" ? "Cоздать обсуждение в этой теме" : "Добавить сообщение к теме"?></div>
                    <div class="reviews-cont">

                        <?php $form = ActiveForm::begin(['action' => 'post','options' => ['enctype' => 'multipart/form-data','id'=>'form-forum-message']]) ?>
                        <?if(Yii::$app->controller->action->id!="post"){?>
                            <?= $form->field($modelNewMessageForum, 'theme',["template"=>"{label}{input}"])->textInput()->label("Тема") ?>
                        <?}else{?>
                            <?= $form->field($modelNewMessageForum, 'id_post',["template"=>"{input}"])->hiddenInput(["value"=>$modelMessage->id_post]) ?>
                            <?= $form->field($modelNewMessageForum, 'id_user_from',["template"=>"{input}"])->hiddenInput() ?>
                        <?}?>
                        <?= $form->field($modelNewMessageForum, 'text',["template"=>"{label}{input}"])->textarea(["wrap"=>"soft"])->label("Текст сообщения <span class='from_name'></span><i style='display:none;' class=\"fa from_name-fa fa-times\" aria-hidden=\"true\"></i>") ?>
                        <?= $form->field($modelNewMessageForum, 'id_parent',["template"=>"{input}"])->hiddenInput(["value"=>$modelBack->id]) ?>
                        <?= $form->field($modelNewMessageForum, 'mod',["template"=>"{input}"])->hiddenInput() ?>
                        <?$get=Yii::$app->request->get();?>

                        <div class="block_bottom-save-reviews">
                            <?= $form->errorSummary($modelNewMessageForum)?>
                        </div>
                        <div class="block-save-revies green-button">

                            <button class="revies-button-save">Сохранить</button>
                        </div>

                        <?php ActiveForm::end() ?>
                    </div>
                </div>
                <?}?>

            </div>


            <?= frontend\components\AdvertisingWidget::widget(["count"=>3]) ?>


        </div>

    </div>
</div>