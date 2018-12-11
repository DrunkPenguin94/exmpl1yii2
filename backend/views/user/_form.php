<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'username')->textInput() ?>
<?= $form->field($model, 'name')->textInput() ?>

<?= $form->field($model, 'surname')->textInput() ?>

	<?= $form->field($model, 'patronymic')->textInput() ?>
    <?= $form->field($model, 'rating')->textInput() ?>


    <?= $form->field($model, 'skill')->textInput() ?>
	<?= $form->field($model, 'password')->textInput() ?>
	<div class="form-group field-editinfouser-rating">
		<label class="control-label" for="editinfouser-rating">E-mail</label>
		<input type="text" id="email" class="form-control" name="" value="<?=$model->email?>" disabled>

		<div class="help-block"></div>
	</div>
	<?= $form->field($model, 'is_send_email')->dropDownList([
                            '0' => 'Отказ от рассылки',
                            '1' => 'Согласие на рассылку ',
                            
                        ])?>
    <div class="form-group">
        <?= Html::submitButton( 'Обновить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
