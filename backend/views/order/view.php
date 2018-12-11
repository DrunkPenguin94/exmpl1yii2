<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->title = $model->id.") ".$model->getName();
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <?
        $modelArbitration=$model->arbitration;
        if(!empty($modelArbitration)) {
            ?>
            <table class="table table-striped table-bordered detail-view <?=$model->isArbitration() ? "table-red" : ""?>">

                <th colspan="2">
                    Арбитраж
                </th>
                <tr class="text">
                    <th >
                        <span>Инициатор: </span>
                    </th>
                    <td >
                         <a target="_blank" href="<?=Yii::$app->urlManager->createAbsoluteUrl(['user/view',"id"=>$modelArbitration->id_initiator])?>"><?=$modelArbitration->initiator->fil?></a>
                    </td>
                </tr>
                <tr class="text">
                    <th >
                        <span>Причина: </span>
                    </th>
                    <td >
                        <?=$modelArbitration->getReason()?>
                    </td>
                </tr>

                <tr class="text">
                    <th >
                        <span>Результат: </span>
                    </th>
                    <td >
                        <?=$modelArbitration->gerResultText()?>
                    </td>
                </tr>
                <tr class="text">
                    <th >
                        <span>Дата: </span>
                    </th>
                    <td >
                        <?=date("d.m.Y H:i",$modelArbitration->dateUnix)?>
                    </td>
                </tr>
                <tr class="text">
                    <th >
                        <span>Комментарий к результату: </span>
                    </th>
                    <td >
                        <?=$modelArbitration->getTextResult()?>
                    </td>
                </tr>

                <?if($model->isArbitration()){?>
                <tr>
                    <td colspan="2">
                        <?php $form = ActiveForm::begin([
                                "id"=>"form-result-arbitration",
                                "action"=>"/admin/order/result-arbitration"
                        ]); ?>


                        <?= $form->field($modelCommenceArbitration, 'id_order',["template"=>"{input}"])->hiddenInput(["value"=>$model->id]) ?>
                        <?= $form->field($modelCommenceArbitration, 'result')->dropDownList([
                            '0' => 'На рассмотрении',
                            '1' => 'Исполнитель / '.$model->performer->fil,
                            '2' => 'Заказчик / '.$model->customer->fil,
                        ])->label("Решение арбитража в пользу :") ?>
                        <?= $form->field($modelCommenceArbitration, 'text')->textarea()->label("Комментарий к решению:") ?>
                        <div class="form-group">
                            <?= Html::submitButton('Закончить арбитраж', ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </td>
                </tr>
                <?}?>

            </table>
            <?
        }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Название',
                'value' => $model->getName(),
            ],

            [
                'label' => 'Название',
                'value' => $model->getText(),
            ],
            'date_create',
            [
                'label' => 'Заказчик',
                'format' => 'raw',

                "value" => Html::a($model->customer->fil, ['user/view/'.$model->id_customer], ["target" => "_blank"]),
            ],


            [
                'label' => 'Исполнитель',
                'format' => 'raw',

                "value" => empty($model->id_performer) ? "" : Html::a($model->performer->fil, ['user/view/'.$model->id_performer], ["target" => "_blank"]),
            ],
			
			

            'price',
            [
                'label' => 'Статус',
                'value' => $model->status->name,
            ],
            [
                'label' => 'Тип',
                'value' => $model->typeOrder->name,
            ],


            'date_deadline',


            [
                'label'=>'Отображать исходники при проверке',
                'value'=>function($model){
                    return $model->show_pre_source==0 ? "Нет" : "Да";
                }
            ]
        ],
    ]) ?>

    <div class="label-info-files-order">Файлы прикрепленные к заказу:</div>
    <?php
        $modelFileAttached= $model->getFileAttached(1);
        $n_file_1=0;
        foreach ($modelFileAttached as $value){
            $n_file_1++;
            echo DetailView::widget([
                'model' => $value,

                "options"=>[
                    "class"=>"attached-files table table-striped table-bordered detail-view",
                ],
                'attributes' => [
                        [
                            'attribute'=>"name",
                            "label"=>"Файл ".$n_file_1,
                            'format'=>'raw',

                            "value"=>Html::a($value->name.".".$value->format, ['/order'.$value->getLink()],["download"=>""]),


                            'contentOptions' => ['class' => 'half'],
                        ]
                    ]
                ]);
        }
    ?>



    <?php
    $modelFileAttached= $model->getFileAttached(2);
    $n_file_1=0;
    if(count($modelFileAttached)) {
        ?>
        <div class="label-info-files-order">Файлы прикрепленные к заказу для проверки:</div>
        <?
        foreach ($modelFileAttached as $value) {
            $n_file_1++;
            echo DetailView::widget([
                'model' => $value,

                "options" => [
                    "class" => "attached-files table table-striped table-bordered detail-view",
                ],
                'attributes' => [
                    [
                        'attribute' => "name",
                        "label" => "Файл " . $n_file_1,
                        'format' => 'raw',

                        "value" => Html::a($value->name . "." . $value->format, [ '/order'.$value->getLink()], ["download" => ""]),


                        'contentOptions' => ['class' => 'half'],
                    ]
                ]
            ]);
        }
    }
    ?>


    <?php
    $modelFileAttached= $model->getFileAttached(3);
    $n_file_1=0;
    if(count($modelFileAttached)) {
        ?>
        <div class="label-info-files-order">Файлы прикрепленные к заказу исходники:</div>
        <?
        foreach ($modelFileAttached as $value) {
            $n_file_1++;
            echo DetailView::widget([
                'model' => $value,

                "options" => [
                    "class" => "attached-files table table-striped table-bordered detail-view",
                ],
                'attributes' => [
                    [
                        'attribute' => "name",
                        "label" => "Файл " . $n_file_1,
                        'format' => 'raw',

                        "value" => Html::a($value->name . "." . $value->format, ['/order'.$value->getLink()."/"], ["download" => ""]),


                        'contentOptions' => ['class' => 'half'],
                    ]
                ]
            ]);
        }
    }
    ?>

    <?php
    $modelOrderRevisions= $model->orderRevisions;
    $n_file_1=0;
    if(count($modelOrderRevisions)) {
        ?>
        <div class="label-info-files-order">Доработки:</div>
        <?
        foreach ($modelOrderRevisions as $value) {
            $n_file_1++;
            echo DetailView::widget([
                'model' => $value,

                "options" => [
                    "class" => "attached-revision  table table-striped table-bordered detail-view",
                ],
                'attributes' => [
                    [
                        'attribute' => "name",
                        "label" => "Доработка " . $n_file_1,
                        'format' => 'raw',

                        "value" => $value->getDefReason(),


                        'contentOptions' => ['class' => 'half'],
                    ]
                ]
            ]);
        }
    }
    ?>


</div>
<div style="margin-top:25px;">
    <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Вы уверены что хотите удалить заказ?',
            'method' => 'post',
        ],
    ]) ?>
</div>