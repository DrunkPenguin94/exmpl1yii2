<?php
/**
 * Created by PhpStorm.
 * User: Drunk Penguin
 * Date: 06.10.2017
 * Time: 12:19
 */

namespace frontend\models;
use Yii;
use common\models\ForumMessage;
use common\models\ForumFolder;
use yii\base\Model;
class NewMessageForum extends Model
{
    public $id_user;
    public $theme;
    public $text;
    public $id_parent;
    public $id_post;
    public $id_user_from;
    public $mod;

    public function rules()
    {
        return [

            [['id_user','text'], 'required'],
            [['text','theme'], 'string'],
            [['id_user','id_post','id_user_from','id_parent'], 'integer'],
            ['mod','safe'],
            ['theme', function ($attribute, $params) {

                if (empty($this->$attribute) && $this->mod==0) {
                    $this->addError($attribute, 'Поле тема должно быть заполненно');
                }
            },'params'=>["mod"=>'mod']],
        ];
    }

    public function attributeLabels() {
        return [
            'id_user' => 'Отправитель',
            'theme' => 'Тема',
            'text' => 'Текст',
            'id_parent' => 'Id родителя',
            'id_post' => 'Id поста',
            'id_user_from' => 'Кому',

        ];
    }

    public function createTheme(){
        if($this->validate()){
            $modelMessage=new ForumMessage;
            $modelForumFolder=new ForumFolder;

            $modelForumFolder->name=$this->theme;
            $modelForumFolder->info=$this->text;
            $modelForumFolder->id_parent=$this->id_parent;
            $modelForumFolder->date_create=date("Y-m-d H:i:s");
            $modelParent=ForumFolder::find()->where(["id"=>$this->id_parent])->one();
            if(isset($modelParent))
                    $level=$modelParent->level+1;
            else
                $level=0;
            $modelForumFolder->level=$level;

            $modelForumFolder->type=1;
            $modelForumFolder->validate();
            Yii::trace($modelForumFolder->getErrors());
            $modelForumFolder->save();

            $modelMessage->id_user=$this->id_user;
            $modelMessage->text=nl2br($this->text);
            $modelMessage->id_post=$modelForumFolder->id;
            $modelMessage->id_user_from=null;
            $modelMessage->date_create=date("Y-m-d H:i:s");
            $modelMessage->validate();
            Yii::trace($modelMessage->getErrors());
            $modelMessage->save();
            return $modelMessage;
        }else{
            Yii::trace($this->getErrors());
            return null;
        }

    }

    public function createMessage(){
        if($this->validate()){
            $modelMessage=new ForumMessage;



            $modelMessage->id_user=$this->id_user;
            $modelMessage->text=nl2br($this->text);
            $modelMessage->id_post=$this->id_post;
            $modelMessage->id_user_from=$this->id_user_from;
            $modelMessage->date_create=date("Y-m-d H:i:s");
            $modelMessage->validate();
            Yii::trace($modelMessage->getErrors());
            $modelMessage->save();
            return $modelMessage;
        }else
            return null;
    }

}