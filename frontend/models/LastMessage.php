<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 15.09.2017
 * Time: 12:34
 */

namespace frontend\models;
use common\models\Message;
use Yii;
use common\models\User;

use yii\base\Model;
class LastMessage extends Model
{

    public function load($id_user_to,$limit=10,$arrNotId=null){
        if(isset($id_user_to)) {
            $modelMessageMax = Message::find()
                ->select("id_user_from as id_user_from_sub,max(date_create) as max_date");

            if(isset($arrNotId)) {
                $modelMessageMax = $modelMessageMax->Where(["not in", 'id_user_from', $arrNotId])->andWhere(['id_user_to' => $id_user_to]);
            }else{
                $modelMessageMax = $modelMessageMax->andWhere(['id_user_to' => $id_user_to]);
            }
            $modelMessageMax=$modelMessageMax->groupBy("id_user_from");



            $modelMessageCount = Message::find()
                ->select("id_user_from as id_user_from_count,count(id_user_from) as new_count")
                ->where(['id_user_to' => $id_user_to, "new" => 0])
                ->groupBy("id_user_from");

            $modelMessage = Message::find()->where(['id_user_to' => $id_user_to]);


            $modelMessageFull = (new \yii\db\Query)
                ->select("message.id_user_from as id_user_from,
                    message.text as text,
                    message.date_create as date_create,
                    message_count.new_count as new_count"
                )
                ->from(['message_max' => $modelMessageMax,
                    "message" => $modelMessage,
                    "message_count" => $modelMessageCount,

                ])
                ->where('message.id_user_to=' . $id_user_to .
                    " and message_max.id_user_from_sub=message.id_user_from" .
                    " and message_max.max_date=message.date_create " .
                    " and message_max.id_user_from_sub=message_count.id_user_from_count "

                )
                ->orderBy("message.date_create Desc");
                if($limit!=0)
                    $modelMessageFull=$modelMessageFull->limit($limit);

            $modelMessageFull = $modelMessageFull->all();

            return $modelMessageFull;
        }else{
            return null;
        }
    }

    public function countAllMessage($id_user_to,$limit=10){
        $count=count($this->load($id_user_to,0));
        return $count==0 ? "":$count;
    }




    public function search($id_user_to,$str){

        $model=User::find()
            ->where(['like','name',$str])
            ->orWhere(['like','surname',$str])
            ->all();
        Yii::trace($str);
        if(isset($model)){
            $arrId=[];
            foreach($model as $valueId){
                $arrId[]=$valueId->id;
            }
            $modelReturn=$this->allMessageCommon($id_user_to,0,null,$arrId);


            return $modelReturn;
        }else{
            return null;
        }
       // $model=$this->load($id_user_to,0);
    }


    //cтарая версия
//    public function allMessageCommonTest($id_user_to,$limit=10,$arrNotId=null){
//        if(isset($id_user_to)) {
//            $modelMessageMax = Message::find()
//                ->select("id_user_from as id_user_from_sub,max(date_create) as max_date");
//
//
//            $modelMessageMax = $modelMessageMax->andWhere(['id_user_to' => $id_user_to]);
//            $modelMessageMax=$modelMessageMax->groupBy("id_user_from");
//
//
//
//            $modelMessageCount = Message::find()
//                ->select("id_user_from as id_user_from_count,count(id_user_from) as new_count")
//                ->where(['id_user_to' => $id_user_to, "new" => 0])
//                ->groupBy("id_user_from");
//
//            $modelMessage = Message::find()->where(['id_user_to' => $id_user_to]);
//
//
//            $modelMessageFull = (new \yii\db\Query)
//                ->select("message.id_user_from as id_user_from,
//                    message.text as text,
//                    message.date_create as date_create"
//                //    message_count.new_count as new_count"
//                )
//                ->from(['message_max' => $modelMessageMax,
//                    "message" => $modelMessage,
//                //    "message_count" => $modelMessageCount,
//
//                ])
//                ->where('message.id_user_to=' . $id_user_to .
//                    " and message_max.id_user_from_sub=message.id_user_from" .
//                    " and message_max.max_date=message.date_create "// .
//                  //  " and message_max.id_user_from_sub=message_count.id_user_from_count "
//
//                )
//                ->orderBy("message.date_create Desc");
//
//
//            $modelMessageFull = (new \yii\db\Query)
//                ->select("message_full.id_user_from as id_user_from,
//                    message_full.text as text,
//                    message_full.date_create as date_create,
//                    message_count.new_count as new_count")
//                ->from(["message_full"=>$modelMessageFull])
//                ->leftJoin(["message_count"=>$modelMessageCount]," message_full.id_user_from=message_count.id_user_from_count ");
//
//                if(isset($arrNotId))
//                    $modelMessageFull->Where(["not in", 'message_full.id_user_from', $arrNotId]);
//
//                if($limit!=0)
//                    $modelMessageFull=$modelMessageFull->limit($limit);
//
//                $modelMessageFull->orderBy("new_count Desc,date_create");
//            $modelMessageFull = $modelMessageFull->all();
//
//            return $modelMessageFull;
//        }else{
//            return null;
//        }
//    }

    public function allMessageCommon($id_user_to,$limit=10,$arrNotId=null,$arrId=null){
        if(isset($id_user_to)) {

            //получение сообщений с последней датой
            $modelMessageMaxTo = Message::find()
                ->select("id_user_from as id_user, date_create,id")
                ->where(["id_user_to"=>$id_user_to]);
            $modelMessageMaxFrom = Message::find()
                ->select("id_user_to as id_user, date_create,id")
                ->where(["id_user_from"=>$id_user_to]);
            $modelMessageMaxTo->union($modelMessageMaxFrom);

            $modelMessageMax=(new \yii\db\Query)
                ->select("max(message_max.id) as id_message,message_max.id_user as id_user")
                ->from([
                    'message_max' => $modelMessageMaxTo
                ])
                ->groupBy(" message_max.id_user");


        //получение непрочитанных новых количество
            $modelMessageCount = Message::find()
                ->select("id_user_from as id_user_from_count,count(id_user_from) as new_count")
                ->where(['id_user_to' => $id_user_to, "new" => 0])
                ->groupBy("id_user_from");

            //получение всех сообщений пользователя от и кому
            $modelMessage = Message::find()->where(['or','id_user_to' => $id_user_to,'id_user_from'=>$id_user_to]);


            $modelMessageFull = (new \yii\db\Query)
                ->select("message_max.id_user as id_user_from,
                    message.text as text,
                    message.date_create as date_create"
                )
                ->from(['message_max' => $modelMessageMax,
                    "message" => $modelMessage,

                ])
                ->where("  message_max.id_message=message.id ".
                    "and (".
                        "message_max.id_user=message.id_user_from or message_max.id_user=message.id_user_to ".
                    ")"
                 )
                ->orderBy("message.date_create Desc");


            $modelMessageFull = (new \yii\db\Query)
                ->select("message_full.id_user_from as id_user_from,
                    message_full.text as text,
                    message_full.date_create as date_create,
                    message_count.new_count as new_count")
                ->from(["message_full"=>$modelMessageFull])
                ->leftJoin(["message_count"=>$modelMessageCount]," message_full.id_user_from=message_count.id_user_from_count ");

            if(isset($arrNotId))
                $modelMessageFull->Where(["not in", 'message_full.id_user_from', $arrNotId]);

            if(isset($arrId))
                $modelMessageFull->Where(["in", 'message_full.id_user_from', $arrId]);

            if($limit!=0)
                $modelMessageFull=$modelMessageFull->limit($limit);

            $modelMessageFull->orderBy("new_count Desc,date_create Desc");
            $modelMessageFull = $modelMessageFull->all();

            return $modelMessageFull;
        }else{
            return null;
        }
    }

    public function correspondence($id_my,$id_user,$limit=5,$since=null,$to=null){
        $modelMessage=Message::find()
            ->where(["id_user_to"=>$id_my,"id_user_from"=>$id_user])
            ->orWhere(["id_user_to"=>$id_user,"id_user_from"=>$id_my]);

        if(isset($since))
            $modelMessage->andWhere(["<","id",$since]);

        if(isset($to))
            $modelMessage->andWhere([">","id",$to]);

        $modelMessage->orderBy("date_create DESC,id Desc")
            ->limit($limit);

        $modelMessage=$modelMessage->all();



        $modelMessage=array_reverse($modelMessage);
        return $modelMessage;

    }

}