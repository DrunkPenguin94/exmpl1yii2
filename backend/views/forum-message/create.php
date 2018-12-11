<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ForumMessage */

$this->title = 'Создать сообщение';
$this->params['breadcrumbs'][] = ['label' => 'Форум', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forum-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
