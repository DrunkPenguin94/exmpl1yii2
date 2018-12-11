<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Advertising */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="advertising-form">

    <?php $form = ActiveForm::begin((['options' => ['enctype' => 'multipart/form-data']])); ?>

    <?= $form->field($model, 'name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'link')->textarea(['rows' => 6]) ?>

    <?if(!$model->isNewRecord){?>
        <div class="edit-file-advertising ">
            Загруженное изображение: <a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['../files/advertising/'.$model->img_url])?>" target="_blank">Просмотреть</a>
            <div><?= Html::Button('Заменить изображение', ['class' => 'change-file btn btn-warning']) ?></div>
        </div>
        <?= $form->field($modelFile, 'imageFile',[
                "options"=>[
                    'class'=>'hidden-input-file',
                    'style'=>["display"=>"none"]
                ]
                ])->fileInput()->label("Новое изображение заменить старое") ?>
    <?}else{?>
        <?= $form->field($modelFile, 'imageFile')->fileInput()->label("Заменить изображение") ?>
    <?}?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать рекламу' : 'Сохранить Рекламу', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
