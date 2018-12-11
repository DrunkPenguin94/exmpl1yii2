<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Рассылка сообщений';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-form">
<?if($mod){?>
	<a href="/admin/user/mailing-letters">Новая рассылка</a>
<?}else{?>
<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($mailingLetters, 'text')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton( 'Начать рассылку', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

<?}?>
</div>