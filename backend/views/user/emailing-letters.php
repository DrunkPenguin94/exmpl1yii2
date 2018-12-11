<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\ckeditor\CKEditor;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рассылка e-mail сообщений';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-form">

<?if($mod){?>
	<a href="/admin/user/emailing-letters">Новая рассылка</a>
<?}else{?>

    <?php $form = ActiveForm::begin((['options' => ['enctype' => 'multipart/form-data']])); ?>
	
	
	  <?= $form->field($model, 'theme')->textInput() ?>
	
	<?= $form->field($model, 'text')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full',

        'clientOptions' => [
            //  'filebrowserUploadUrl' => Url::to(["/".Yii::$app->controller->id."/upload"])
        ]
    ]) ?>
	
	 <?= $form->field($modelFile, 'imageFile')->fileInput()->label("Изображение (загрузить если нужно)") ?>
	 
	<div class="form-group">
        <?= Html::submitButton( 'Начать рассылку', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>
<?}?>
</div>