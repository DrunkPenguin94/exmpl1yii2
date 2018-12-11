<?php
namespace frontend\components;

use yii\base\Widget;
use yii\helpers\Html;
use common\models\Advertising;

class AdvertisingWidget extends Widget
{
    public $count;

    public function init()
    {
        parent::init();
        if ($this->count === null) {
            $this->count = 1;
        }
    }

    public function run()
    {

        $model = Advertising::find()->orderBy('rand()')->limit($this->count)->all();
        $count_null=0;
        $count_model=count($model);
        if($this->count > $count_model){
            $count_null=$this->count-$count_model;
        }
        return $this->render('/widget/advertising',[
            "model"=>$model,
            "count"=>$this->count,
            "count_null"=>$count_null
        ]);
    }
}