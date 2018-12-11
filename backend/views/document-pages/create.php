<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DocumentPages */

$this->title = 'Create Document Pages';
$this->params['breadcrumbs'][] = ['label' => 'Document Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-pages-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
