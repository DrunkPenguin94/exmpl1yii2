<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ForumFolder */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Форум'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forum-folder-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Изменить'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Удалить'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Вы уверены что хотите удалить?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name:ntext',
            'info:ntext',
            'mass',
            'date_create',
            [
                'attribute' => 'type',
                'value'=> function ($model) {
                    if($model->type == 0)
                    {
                        return 'Папка';
                    }
                    else {
                        return 'Пост';
                    }
                },
            ],
        ],
    ]) ?>

</div>
