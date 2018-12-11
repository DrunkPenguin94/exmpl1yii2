<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * ContactForm is the model behind the contact form.
 */
class StatusOnline extends Model
{
    public $id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'required'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id people',
        ];
    }


    public function updateDate()
    {
        if(isset(Yii::$app->getUser()->id)){
            $modelUser=User::find()->where(["id"=>Yii::$app->getUser()->id])->one();
            $modelUser->last_online=date("Y-m-d H:i");
            if($modelUser->validate()){
                $modelUser->save();
                return true;
            }else{
                Yii::trace($modelUser->getErrors());
                return null;
            }
        }else{
            return null;
        }
    }

    public function getStatus()
    {
        if(isset($this->id)){
            $modelUser=User::find()->where(["id"=>$this->id])->one();

            $dateNow=strtotime(date("Y-m-d H:i"));

            if($dateNow-strtotime($modelUser->last_online) < 300)
                return "Онлайн";
            else {

                $sec=$dateNow-strtotime($modelUser->last_online);
                $min=floor($sec/60);
                $hour=floor($min/60) ;
                $day=floor($hour/24);

                if($day!=0) {
                    return "Офлайн ".$day." ".pluralForm($day, "день","дня","дней");
                }elseif($hour!=0)
                    return "Офлайн ".$hour." ".pluralForm($hour, "час","часа","часов");
                elseif($min!=0){
                    return "Офлайн ".$min." минут";
                }else{
                    return "Офлайн ".$min." секунд";
                }

            }
        }else{
            return null;
        }
    }


    public function transformDateMessage($date)
    {
        Yii::trace(date(date("Y-m-d H:i:s")));
        Yii::trace($date);
            $dateNow=strtotime(date("Y-m-d H:i:s"));
            $date=strtotime($date);
        Yii::trace($dateNow-$date);
            if($dateNow-$date < 60*60*24){
                Yii::trace(date("H",$dateNow));
                Yii::trace(date("H",$date));
                if(date("H",$dateNow)>= date("H",$date))
                    return date("H:i",$dateNow);
                else
                    return "Вчера";


            }else{
                $day=floor(($dateNow-$date) /(60*60*24));

                return $day." ".pluralForm($day,"день","дня","дней")." назад";
            }
//

    }
}
