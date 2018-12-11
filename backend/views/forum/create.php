<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ForumFolder */

$this->title =  'Создать папку / пост';
$this->params['breadcrumbs'][] = ['label' => 'Forum Folders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forum-folder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
