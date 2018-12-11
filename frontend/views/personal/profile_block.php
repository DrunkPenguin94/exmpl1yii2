<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 10.09.2017
 * Time: 18:32
 */
use yii\helpers\Html;

use yii\widgets\ActiveForm;
use yii\helpers\HtmlPurifier;

$sumReviews=$userModel->sumReviews;
$countReviews =$userModel->countReviews;
?>
<div class="profile-user-block">

    <div class="profile-user-photo">
        <img id="image_pre_download" src="<?=$userModel->avatarUrl?>"/>


        <?if($userModel->id!=Yii::$app->getUser()->id && isset(Yii::$app->getUser()->id)){ ?>
        <div class="send-mess_b-wrap green-button">
            <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['personal/correspondence',"user"=>$userModel->id])?>">
                <button class="send-mess_b"><i class="fa fa-envelope" aria-hidden="true"></i> Отправить сообщение</button>
            </a>
        </div>
        <?}?>
    </div>

    <div class="profile-user-block-info">
        <div class="top-line">
            <div class="name-info">
                <div class="name-info-name"><?=$userModel->fil?></div>
                <div class="user-status <?=$userModel->statusOnline=="Онлайн" ? "green":"" ?>"><?=$userModel->statusOnline ?></div>

                <div class="name-info-experience"><?=$userModel->skillLevelName?></div>

                <div class="name-info-reviews">
                    <span>Отзывы: </span><i class="fa fa-thumbs-o-up fa-1" aria-hidden="true"></i>&nbsp;
                    <div class="user-reviews"><?=$sumReviews?></div>
                </div>

                <div class="name-info-rating">
                    <span>Рейтинг: </span><i class="fa fa-bar-chart fa-1" aria-hidden="true"></i>&nbsp;
                    <div class="user-rating"><?=$userModel->rating ?></div>
                </div>
            </div>


        </div>

        <div class="mes-wrap mes-wrap-view">
            <div class="triangle"></div>
            <div class="message message-edit content-list">
            <div class="message-text-area block"><?=HtmlPurifier::process($userModel->short_info)?></div>
            <?if($userModel->id==Yii::$app->getUser()->id){?>
                <textarea class="message-text-area textarea" style="display:none;"><?=  strip_tags($userModel->short_info)?></textarea>

                <div class="right-btn-wrap">
                    <button class="message-edit_b button_info_mini">Редактировать</button>
                    <button class="message-edit_b button_info_mini_save" style="display:none;" id_user=" <?=$userModel->id?>">Cохранить</button>
                </div>
            <?}?>
            </div>
        </div>

    </div>
</div>

<?if($userModel->id==Yii::$app->getUser()->id){?>
    <div class="content-list" style="">
        <div class="block_under_img">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','id'=>'form-load-avatar']]) ?>

            <div class="block-load-avatar green-button block-load">
                <button class="avatar-button-load">Загрузить изображение</button>
            </div>

            <?= $form->field($modelUploadFile, 'imageFile',["template"=>"{input}"])->fileInput() ?>
            <div class="block-load" style="display:none;">
                <img id="image_pre_download" src="#" alt="" />
            </div>
            <div class="block-save-avatar green-button block-load">
                <button class="avatar-button-save">Сохранить</button>
                <a href="/personal/profile" class="avatar-button-clear orange" style="display:none;">Отмена</a>
            </div>
            <div class="name-file-avatar block-load">

            </div>

            <?= $form->errorSummary($modelUploadFile)?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
<?}?>

<div class="content-list">
    <div class="content-title">О себе</div>
    <?if($userModel->id==Yii::$app->getUser()->id){?>
        <textarea class="requirements requirements-edit textarea" id_user="<?=$userModel->id ?>"><?=strip_tags($userModel->requirements)?></textarea>
        <div class="green-button right-btn-wrap">
            <button class="requirements-edit_b"><span class="edit" style="display:none;">Редактировать</span><span class="save" >Сохранить</span></button>
        </div>
        <div class="message-system requirement"></div>
    <?}else{?>
        <div class="requirements requirements-edit block">
           <?=HtmlPurifier::process($userModel->requirements)?>
        </div>
    <?}?>

</div>

<?if($userModel->isRetusher()):?>
    <div class="content-list">
        <div class="content-title">Портфолио</div>
        <div class="portfolio-content">
            <?foreach($userModel->portfolio as $modelPortfolio){?>
            <div class="portfolio-img">
                <?if($userModel->id==Yii::$app->getUser()->id){?>
                <div class="del-block-portfolio" id_portfolio="<?=$modelPortfolio->id?>" style="">
                    <img src="/img/cancel.png" class="big-img-cancel" style=""/>
                </div>
                <?}?>
                <img big_src="<?="/".$modelPortfolio->path.$modelPortfolio->name.".".$modelPortfolio->format?>" src="<?="/".$modelPortfolio->path.$modelPortfolio->name_mini.".".$modelPortfolio->format_mini?>"/>

                <div class="hover-effect">

                    <div class="hover-effect-text">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                        <span>Обзор</span>
                    </div>
                </div>
            </div>

            <?}?>
            <?if($userModel->id==Yii::$app->getUser()->id){?>
            <div class="portfolio-img add-portfolio">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','id'=>'form-load-portfolio']]) ?>



                <?= $form->field($modelUploadPortfolio, 'imageFile',["template"=>"{input}"])->fileInput() ?>
                <?= $form->errorSummary($modelUploadPortfolio)?>

                <?php ActiveForm::end() ?>

                <div class="add-portfolio-text">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    <span>
                        Добавить проект
                    </span>
                   <img src="#" alt=""/>
                </div>


            </div>

            <?}?>

        </div>
        <?if($userModel->id==Yii::$app->getUser()->id){?>
        <div class="green-button right-btn-wrap">
            <div class="portfolio-load-name"></div>
            <div class="portfolio-load-error"></div>
            <button class="portfolio-edit_b">Сохранить</button>
        </div>
        <?}?>
    </div>
<?endif;?>

<div class="content-list">
    <div class="content-title">Отзывы (<span class="reviews-number"><?=$countReviews?></span>)</div>
    <div class="reviews-cont"  this_user="<?=$userModel->id?>">
     <?   $modelReviews=$userModel->getReviews(5,0);?>

    <?=$this->render("_review",[
        "userModel"=>$userModel,
        "modelReviews"=>$modelReviews

    ])?>


    </div>
    <div class="green-button right-btn-wrap">
        <button class="all-reviews_b" style="<?=$countReviews<=5? "display:none;":""?>">Еще</button>
    </div>
</div>
<?//if($userModel->id!=Yii::$app->getUser()->id && isset(Yii::$app->getUser()->id)){?>
<!--<div class="content-list">-->
<!--    <div class="content-title">Оставить отзыв </div>-->
<!--    <div class="reviews-cont"  this_user="--><?//=$userModel->id?><!--"> $fo-->
<!---->
<!--        --><?php //$form = ActiveForm::begin(['action' => 'view-people','options' => ['enctype' => 'multipart/form-data','id'=>'form-load-reviews']]) ?>
<!---->
<!--        --><?//=rm->field($modelReview, 'id_from_user',["template"=>"{input}"])->hiddenInput(['value'=>Yii::$app->getUser()->id]) ?>
<!--        --><?//= $form->field($modelReview, 'id_to_user',["template"=>"{input}"])->hiddenInput(['value'=>$userModel->id]) ?>
<!--        --><?//= $form->field($modelReview, 'text',["template"=>"{input}"])->textarea() ?>
<!--        <div class="block_bottom-save-reviews">-->
<!--            --><?//= $form->errorSummary($modelReview)?>
<!--        </div>-->
<!--        <div class="block-save-revies green-button">-->
<!---->
<!--            <button class="revies-button-save">Сохранить</button>-->
<!--        </div>-->
<!---->
<!--        --><?php //ActiveForm::end() ?>
<!--    </div>-->
<!--</div>-->
<?//}?>

<div class="big-img-fon hidden">
    <div class="content-list big-img-container">
        <div class="content-title">Портфолио

        </div>
        <img src="/img/cancel.png" class="big-img-cancel"/>
        <div class="big-img-wrap">
            <img class="big-img" src=""/>

        </div>
        <img class="str-left" src="/img/str-left.png"/>
            <img class="str-right"  src="/img/str-right.png"/>

    </div>
</div>
