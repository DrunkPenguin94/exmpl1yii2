<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 14.09.2017
 * Time: 16:43
 */
use common\models\User;
use common\models\Message;
use frontend\models\LastMessage;
use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$url = \yii\helpers\Url::to(['name-list']);

?>



<div class="content-list">
    <div class="content-title">Написать сообщение</div>

    <?php $form = ActiveForm::begin(['id' => 'form-personal-info',
        //'enableAjaxValidation' => true,
        'options' => [
            //  'data-pjax' => true,
            'class' => 'change-info-form',
            'name'=>'info',
        ],
        'fieldConfig' => [
            'template' => "{label}{input}",
        ]
    ]); ?>
        <div class="block-send-message">
<!--            <div class="user-avatar user-info-icon">-->
<!---->
<!--            </div>-->
            <div class="user-review messages new-messages ">
                <div class="error-text">
                    <?= $form->errorSummary($modelMessage); ?>
                </div>
                <div class="answer-form">
                    <?=$form->field($modelMessage, 'id_user_to')->widget(Select2::classname(), [
                        'initValueText' => 'Логин пользователя', // set the initial display text
                        'options' => ['placeholder' => 'Поиск пользователя...'],
                        'pluginOptions' => [
                            'theme' => Select2 :: THEME_DEFAULT ,

                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => 'ru',
                             'ajax' => [
                                'url' => $url,
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {q:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function(city) { return city.text; }'),
                            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                        ],
                    ])->label("Кому ( Логин )",["class"=>'registr-label'])?>
                    <?=$form->field($modelMessage, 'text',
                        [
                            'labelOptions' => [ 'class' => 'registr-label' ],
                            'options'=>['class'=>'input-wrap input-line left-line']
                        ]
                    )->textarea(["class"=>"new-message-input" ])?>
                    <div class=" green-button new-message-button-block">
                        <?=Html::submitButton("Отправить",[
                            "class"=>" new-message "
                        ])?>
                    </div>
                </div>
            </div>
        </div>



    <?php ActiveForm::end(); ?>
</div>