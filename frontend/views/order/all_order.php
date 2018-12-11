<?php
/* @var $this yii\web\View */
use yii\widgets\ListView;

$this->title="Список заказов";

$get=Yii::$app->request->get();
if(empty($get["sort"]))$get["sort"]="date";
if(empty($get["quantity"]))$get["quantity"]="10";
?>

<div class="content-list">
    <div class="content-title">Все заказы (<span><?=$ordersCount?></span>)</div>

    <div class="filters-container">
        <form id="select-sort-orders" method="get" action="<?=Yii::$app->urlManager->createAbsoluteUrl(['order/index'])?>">
            <select class="on_date orders-filter" name="sort">
                <option <?= $get["sort"]=="date" ? "selected": ""?> value="date">По дате</option>
                <option <?= $get["sort"]=="char" ? "selected": ""?> value="char">По алфавиту</option>
                <option <?= $get["sort"]=="price" ? "selected": ""?> value="price">По цене</option>
            </select>

            <select class="on_quantity orders-filter" name="quantity">
                <option <?= $get["quantity"]=="10" ? "selected": ""?> value="10">По 10</option>
                <option <?= $get["quantity"]=="20" ? "selected": ""?> value="20">По 20</option>
                <option <?= $get["quantity"]=="50" ? "selected": ""?> value="50">По 50</option>
            </select>
        </form>

        <a id="sort-order-a" href="" style="display:none" >Обновить фильтр</a>

    </div>

    <div class="orders_container">
        <?php
            echo ListView::widget([
                'dataProvider' => $modelOrders,
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('one_prev_order', ['model' => $model]);
                },
                'id'=>'list-order',
                'itemOptions' => [
                    'class' => 'order-line',
                ],
                'layout' => "{items}\n{pager}",
                'emptyText' => 'Список пуст',
                'pager' => [
                    'nextPageLabel' => 'Следующая >',
                    'prevPageLabel' => '< Предыдущая',
                    'maxButtonCount' => 0,
                ],
            ]);
        ?>
    </div>

</div>


