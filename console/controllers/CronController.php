<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\models\CheckOrder;
use console\models\FileOrder;
/**
 * Site controller
 */
class CronController extends Controller
{


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionCheckOrder()
    {
        echo "start\n";
        $modelCheckOrder=new CheckOrder;
        $modelCheckOrder->startCheck();


        echo "end";
        return 0;
    }


    public function actionDeleteOldFile()
    {

        $modelFileOrder=new FileOrder;
        $modelFileOrder->startClear();
        //echo 0;
        return 0;
    }
}