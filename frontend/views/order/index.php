<?php
/* @var $this yii\web\View */
use yii\widgets\ListView;
use yii\widgets\Pjax;
use frontend\components\AdvertisingWidget;
$this->title="Список заказов";
$get=Yii::$app->request->get();
if(empty($get["type"]))$get["type"]="all";

$isRetusher=Yii::$app->user->identity->isRetusher();
?>

<?php //Pjax::begin([
//]); ?>
<div class="fixed-footer_container">
    <div class="grey-container">
        <div class="main-container">
            <?php if(Yii::$app->session->hasFlash('success')):
                $flash=Yii::$app->session->getFlash('success');
                foreach ($flash as $value){
                ?>
                <div class="flash-message-block">
                    <?  echo $value;?>
                </div>
                <?}?>
            <?  endif; ?>


            <?php if(Yii::$app->session->hasFlash('error')):
                $flash=Yii::$app->session->getFlash('error');
                foreach ($flash as $value){
                    ?>
                <div class="flash-message-block error">
                    <? echo $value;?>
                </div>
                <?}?>
            <?php endif; ?>


            <div class="personal_area  with-create-order ">
                <?if(!$isRetusher){?>
                    <div class="create-order-wrap">
                        <a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/create'])?>"><button class="order-button orange create-order">Создать заказ</button></a>
                    </div>
                    <div class="orders">
                        <div class="content-title orders">Заказы</div>
                        <ul class="personal_area-items  orders">
                            <li class="all <?= !isset($get["type"]) || $get["type"]=="all" ?"active-li" : ""?>" type="all"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"all"])?>">Все</a></li>
                            <li class="without_artist <?= $get["type"]=="without_artist" ?"active-li" : ""?>" type="without_artist"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"without_artist"])?>">Исполнитель не назначен </a><span></span></li>
                            <li class="in_work <?= $get["type"]=="in_work" ?"active-li" : ""?>" type="in_work"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"in_work"])?>">В&nbsp;работе </a><span></span></li>
                            <li class="on_rework <?= $get["type"]=="on_rework" ?"active-li" : ""?>" type="on_rework"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"on_rework"])?>">На&nbsp;доработке </a><span></span></li>
                            <li class="completed <?= $get["type"]=="completed" ?"active-li" : ""?>" type="completed"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"completed"])?>">Выполнены </a><span></span></li>
                            <li class="other <?= $get["type"]=="other" ?"active-li" : ""?>" type="other"><a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"other"])?>">Мои </a><span></span></li>
                        </ul>
                    </div>
                <?}else{?>
                    <div class="create-order-wrap">
                        <a data-pjax=0 href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/'])?>"><button class="order-button orange create-order">Лента заказов</button></a>
                    </div>
                    <div class="orders">
                        <div class="content-title orders">Заказы</div>
                        <ul class="personal_area-items  orders">
                            <li class="all <?= !isset($get["type"]) || $get["type"]=="all" ?"active-li" : ""?>" type="all"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"all"])?>">Все</a></li>
                            <li class="without_artist <?= $get["type"]=="without_artist" ?"active-li" : ""?>" type="without_artist"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"without_artist"])?>">Подана заявка </a><span></span></li>
                            <li class="in_work <?= $get["type"]=="in_work" ?"active-li" : ""?>" type="in_work"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"in_work"])?>">В&nbsp;работе </a><span></span></li>
                            <li class="on_rework <?= $get["type"]=="on_rework" ?"active-li" : ""?>" type="on_rework"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"on_rework"])?>">На&nbsp;доработке </a><span></span></li>
                            <li class="completed <?= $get["type"]=="completed" ?"active-li" : ""?>" type="completed"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"completed"])?>">Выполнены </a><span></span></li>
                            <li class="other <?= $get["type"]=="other" ?"active-li" : ""?>" type="other"><a href="<?=Yii::$app->urlManager->createAbsoluteUrl(['order',"type"=>"other"])?>">Мои </a><span></span></li>
                        </ul>
                    </div>
                <?}?>


<!--                <div class="finances">-->
<!--                    <div class="content-title finances">Мои финансы</div>-->
<!--                    <ul class="personal_area-items  finances">-->
<!--                        <li class="available">Доступно <span></span></li>-->
<!--                        <li class="in_reserve">В&nbsp;резерве <span></span></li>-->
<!--                        <li class="withdrawal">Снятие <span></span></li>-->
<!--                        <li class="other">Прочее <span></span></li>-->
<!--                    </ul>-->
<!--                </div>-->


            </div>


            <div class="content-container">
                <?= $this->render($layouts,$arrParam);?>

            </div>

            <div class="left-block-content">
                <?= AdvertisingWidget::widget(["count"=>1]) ?>
            </div>



        </div>

    </div>
</div>
<?php //Pjax::end(); ?>