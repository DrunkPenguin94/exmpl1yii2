<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Order */
/* @var $form ActiveForm */
?>
<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name_text') ?>
        <?= $form->field($model, 'info_text') ?>
        <?= $form->field($model, 'id_customer') ?>
        <?= $form->field($model, 'price') ?>
        <?= $form->field($model, 'id_status') ?>
        <?= $form->field($model, 'id_type') ?>
        <?= $form->field($model, 'date_deadline') ?>
        <?= $form->field($model, 'date_create') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- order-form -->
