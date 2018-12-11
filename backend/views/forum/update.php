<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ForumFolder */

$this->title = Yii::t('app', 'Форум: ', [
    'modelClass' => 'Forum Folder',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Форум'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="forum-folder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
