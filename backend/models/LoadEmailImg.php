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

class LoadEmailImg extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
			
            [['imageFile'], 'file',  'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg','maxSize'=>1500000],
        ];
    }
	
	public function attributeLabels() {
        return [
			'imageFile' => 'Изображение',
			

        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            
			$name=randName(8);
            
			if(!empty($this->imageFile->extension)){
				$this->imageFile->saveAs(dirname(dirname(__DIR__)) . '/frontend/web/files/email/' .$name . '.' . $this->imageFile->extension);
				return '/files/email/' .$name . '.' . $this->imageFile->extension;
            }
			return null;
        } else {
            Yii::trace($this->getErrors());
            return false;
        }
    }
}