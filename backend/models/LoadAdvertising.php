<?php
/**
 * Created by PhpStorm.
 * User: Drunk penguin
 * Date: 19.03.2018
 * Time: 20:14
 */

namespace backend\models;
use Imagine\Image\Box;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;

class LoadAdvertising extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
			['imageFile','required'],
            [['imageFile'], 'file',  'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg','maxSize'=>1500000],
        ];
    }
	
	public function attributeLabels() {
        return [
			'imageFile' => 'Изображение',
			

        ];
    }

    public function upload($model)
    {
        if ($this->validate()) {
            if(isset($model->img_url)){
                if(file_exists(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' . $model->img_url)){
                    unlink(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' . $model->img_url);
                }

            }
            $new_name=$model->id."_".strtotime(date("d.m.Y H:i"));
            $this->imageFile->saveAs(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '.' . $this->imageFile->extension);

           // Yii::trace(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '.' . $this->imageFile->extension);
            Image::thumbnail(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '.' . $this->imageFile->extension,
                295,
                190)
                ->save(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '1.' . $this->imageFile->extension);


            if(file_exists(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '.' . $this->imageFile->extension)){
                unlink(dirname(dirname(__DIR__)) . '/frontend/web/files/advertising/' .$new_name . '.' . $this->imageFile->extension);
            }

            $model->img_url=$new_name . '1.' . $this->imageFile->extension;
            $model->save();
            return true;
        } else {
            Yii::trace($this->getErrors());
            return false;
        }
    }
}