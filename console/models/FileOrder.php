<?php

namespace console\models;

use Yii;
use yii\base\Model;
use common\models\Order;
use common\models\FilesOrder;
/**
 * Created by PhpStorm.
 * User: Drunk penguin
 * Date: 24.05.2018
 * Time: 0:38
 */
class FileOrder extends Model
{


    public function searchEndOrder(){
        echo "start"+"\n";
        $dateNow=date("Y-m-d H:i:s");
        $dateNow=strtotime($dateNow)-3600*24*7;

        $dateNow=date("Y-m-d H:i:s",$dateNow);


        //удаляем файлы выполненых заказов 2 месячной давности
        foreach (FilesOrder::find()
                 ->andWhere(["is not",'files_order.date_create',null])
                 ->andWhere(["<",'files_order.date_create',$dateNow])
                 ->joinWith(['idOrders'=>function($query){
                                   $query->andWhere(["in",'order.id_status',[6,7]]);
                            }
                 ])
                 ->each(10) as $file){

            $file->deleteThis();
           // echo "-".$file->id."\n";
        }


    }


    public function startClear(){

        $this->searchEndOrder();
        return 0;
    }



}