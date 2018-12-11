<?php
namespace console\models;

use Yii;
use yii\base\Model;
use common\models\Order;
use common\models\PerformerOrder;
use common\models\SafeCrow;
use common\models\SafeCrowOrder;
use common\models\FilesOrder;
use common\models\OrderRevision;
/**
 * Created by PhpStorm.
 * User: Drunk penguin
 * Date: 24.05.2018
 * Time: 0:38
 */
class CheckOrder extends Model
{


    public function searchActiveOrder(){

        $dateNow=date("Y-m-d H:i:s");
        echo "date_now : ".$dateNow."\n";


        $date1day=strtotime($dateNow)-3600*24;
        $date1day=date("Y-m-d H:i:s",$date1day);

        $date2day=strtotime($dateNow)-3600*24*2;
        $date2day=date("Y-m-d H:i:s",$date2day);

        $date3day=strtotime($dateNow)-3600*24*3;
        $date3day=date("Y-m-d H:i:s",$date3day);

        $dateWeekBack=strtotime($dateNow)-3600*24*7;
        $dateWeekBack=date("Y-m-d H:i:s",$dateWeekBack);

        $modelOrder=Order::find()
            ->orWhere([ //поиск просроченныз заказов по дедлайну
                "and",
                ["in","id_status",[1,2,3,4]],
                [
                    "<",
                    "order.date_deadline",
                    $dateNow
                ]
            ])
            ->orWhere([// на проверке больше 2 дней
                "and",
                ["id_status"=>5],
                [
                    "<",
                    "order.date_update",
                    $date2day
                ]

            ])
            ->orWhere([// на доработке больше 1 дней
                "and",
                ["id_status"=>8],
                [
                    "<",
                    "order.date_update",
                    $date1day
                ]

            ])
            /*->orWhere([// на арбитраже больше 3 дней
                "and",
                ["id_status"=>9],
                [
                    "<",
                    "order.date_update",
                    $date3day
                ]

            ])*/
            ->orWhere([ //заказы больше недели в статусе просрочены
                "and",
                ["id_status"=>10],
                [
                    "<",
                    "order.date_update",
                    $dateWeekBack
                ]

            ])
        ;

        $arrStatus=[1,2,3,4,5,8,9,10];

        $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"],Yii::$app->params["safecrow_api_secret"],Yii::$app->params["safecrow_dev"]);


        foreach ($modelOrder->each(50) as $order){
            echo "id : ".$order->id.", status : ".$order->id_status." (".$order->date_deadline.") (".$order->date_update.")\n";


            switch ($order->id_status){
                case 1:


                    $this->switchToOverdue($order);

                    break;

                case 2:
                    $this->switchToOverdue($order);
                    break;

                case 3:

                    //анулирования заказа без блокированных денег у заказчика
                    $modelSafeCrawOrder=SafeCrowOrder::find()->where(["id_order"=>$order->id,"status"=>0])->one();
                    if(isset($modelSafeCrawOrder)) {
                        $result = $modelSafeCrow->annulOrder(
                            $modelSafeCrawOrder->id_order_safecrow,
                            json_encode([
                                "id_order" => $order->id,
                                "status" => "annul (auto)"
                            ])
                        );

                        $modelSafeCrawOrder->status = 10;
                        $modelSafeCrawOrder->date_update = date("Y-m-d H:i");
                        $modelSafeCrawOrder->save();

                        $this->switchToOverdue($order);
                    }
                    break;

                case 4:

                    //отмена  заказа при блокированных денег у заказчика
                    $modelSafeCrawOrder=SafeCrowOrder::find()->where(["id_order"=>$order->id,"status"=>1])->one();
                    if(isset($modelSafeCrawOrder)) {
                        $result = $modelSafeCrow->cancelOrder(
                            $modelSafeCrawOrder->id_order_safecrow,
                            json_encode([
                                "id_order" => $order->id,
                                "status" => "cansel (auto)"
                            ])
                        );
                        $modelSafeCrawOrder->status = 15;
                        $modelSafeCrawOrder->date_update = date("Y-m-d H:i");
                        $modelSafeCrawOrder->save();


                        $modelPerformer = $order->performer;
                        $modelPerformer->removeRating(20, true);//наказание

                        $this->switchToOverdue($order);
                    }
                    break;

                case 5:

                    $this->switchToEnd($order);
                    break;

                case 8:

                    //отмена  заказа при блокированных денег у заказчика
                    $modelSafeCrawOrder=SafeCrowOrder::find()->where(["id_order"=>$order->id,"status"=>1])->one();
                    if(isset($modelSafeCrawOrder)) {
                        $result = $modelSafeCrow->cancelOrder(
                            $modelSafeCrawOrder->id_order_safecrow,
                            json_encode([
                                "id_order" => $order->id,
                                "status" => "cansel (auto)"
                            ])
                        );

                        $modelSafeCrawOrder->status = 15;
                        $modelSafeCrawOrder->date_update = date("Y-m-d H:i");
                        $modelSafeCrawOrder->save();

                        $modelPerformer = $order->performer;
                        $modelPerformer->removeRating(20, true);//наказание


                        $this->switchToOverdue($order);
                    }
                    break;

                case 9:
                    $post["CommenceArbitration"]["text"]="";
                    $post["CommenceArbitration"]["result"]=2; //в пользу заказчика

                    $order->resultArbitration($post);
                    break;

                case 10:
                    $this->switchToDelete($order);
                    break;

            }


        }
    }

    public function switchToDelete($order){
        $modelFilesOrder=new FilesOrder;
        $modelFilesOrder->allDelete($order);//удаление файлов заказа


        $order->id_status=7;
        $order->save();

        $modelNotification = new \common\models\Notification;
        $modelNotification->autoComplete(0,  $order->id_customer, 2,json_encode(["id_order"=>$order->id]));

        return 0;
    }

    public function switchToEnd($order){

        $order->show_pre_source = 1;
        $order->id_status = 6;

        $performer=$order->performer;
        $customer=$order->customer;
        $ratingAdd= round($order->price /100);

        $modelSafeCrow = new SafeCrow(Yii::$app->params["safecrow_api_key"], Yii::$app->params["safecrow_api_secret"], Yii::$app->params["safecrow_dev"]);
        $modelSafeCrawOrder = SafeCrowOrder::find()->where(["id_order" => $order->id,"status"=>"1"])->one();
        echo $modelSafeCrawOrder->id."\n";

        $result = $modelSafeCrow->successOrder($modelSafeCrawOrder->id_order_safecrow);
        if($result){
            $modelSafeCrawOrder->status=5;
            $modelSafeCrawOrder->save();
            $order->save();

            $modelNotification = new \common\models\Notification;
            $modelNotification->autoComplete(0,  $order->id_performer, 18,json_encode(["id_order"=>$order->id]));

            $modelNotification = new \common\models\Notification;
            $modelNotification->autoComplete(0,  $order->id_customer, 19,json_encode(["id_order"=>$order->id]));


            $performer->addRating($ratingAdd,true);
            $performer->upSkill();
            $customer->addRating($ratingAdd,true);


            return true;
        }

        return false;
    }


    public function switchToOverdue($order){

        $old_status= $order->id_status;
        $old_performer= $order->id_performer;

        $order->id_status=10;
        $order->id_performer=null;
        PerformerOrder::deleteAll(["id_order"=>$order->id]);
        OrderRevision::deleteAll(["id_order"=>$order->id]);

        $order->save();

        if( $old_status == 4 ||  $old_status == 8){
            $modelNotification = new \common\models\Notification;
            $modelNotification->autoComplete(0, $order->id_customer, 22, json_encode(["id_order" => $order->id]));

        }else {
            $modelNotification = new \common\models\Notification;
            $modelNotification->autoComplete(0, $order->id_customer, 20, json_encode(["id_order" => $order->id]));
        }
        if( $old_status == 3 ||  $old_status == 4 ||  $old_status == 8){
            $modelNotification = new \common\models\Notification;
            $modelNotification->autoComplete(0,  $old_performer, 21,json_encode(["id_order"=>$order->id]));
        }
    }

    public function startCheck(){

        $this->searchActiveOrder();

        return 0;
    }





}