<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DocumentPages */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Страница с документами', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-pages-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title:ntext',
            'version',
            'text',
        ],
    ]) ?>

</div>
