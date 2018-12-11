<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ForumFolder */
/* @var $form yii\widgets\ActiveForm */
$get=Yii::$app->request->get();
$id_parent=null;
if(isset($get["id_parent"]))
    $id_parent=$get["id_parent"];
?>

<div class="forum-folder-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'info')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'id_parent')->hiddenInput(['value'=>$id_parent])->label('') ?>


    <?= $form->field($model, 'mass')->textInput(['value'=>'0']) ?>
    <?if(Yii::$app->controller->action->id=="create"){?>
        <?= $form->field($model, 'type')->dropDownList(["1"=>"Пост","0"=>"Папка"]) ?>
    <?}?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?  'Создать' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
