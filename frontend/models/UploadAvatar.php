<?php
namespace frontend\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use common\models\FilesImage;
use yii\imagine\Image;
class UploadAvatar extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false,'mimeTypes'=>["image/jpeg","image/png"],'minSize'=>1,'maxSize'=>1000000],

        ];
    }
    public function attributeLabels()
    {
        return [
            'imageFile' => '',

        ];
    }

    // кастомная версия первоначальная
//    public function upload()
//    {
//
//        $modelFiles=FilesImage::find()->where(["id_user"=>Yii::$app->getUser()->id,'type'=>1])->one();
//        if(!isset($modelFiles))
//            $modelFiles=new FilesImage;
//
//
//        $modelFiles->name='avatar_orig_'.Yii::$app->getUser()->id;
//        $modelFiles->name_mini='avatar_orig_mini_'.Yii::$app->getUser()->id;
//        $modelFiles->type=1;
//        $modelFiles->id_user=Yii::$app->getUser()->id;
//        $modelFiles->path='files/img/avatar/';
//        $modelFiles->format=$this->imageFile->extension;
//        $modelFiles->format_mini='jpg';
//        $modelFiles->date_create=date("Y-m-d H:i");
//
//
//
//
//        if ($this->validate() && $modelFiles->validate()) {
//            $this->imageFile->saveAs('files/img/avatar/' . 'avatar_orig_'.Yii::$app->getUser()->id . '.' . $this->imageFile->extension);
//
//
//            $modelFiles->save();
//
//         //   $globalPath=__DIR__.'\..\web\files\img\avatar\\';
//
//            $globalPath=dirname(dirname(__DIR__)).'/frontend/web/files/img/avatar/';
//
//
//            $pathMini= $globalPath.$modelFiles->name.".".$modelFiles->format;
//
//
//            list($width, $height) = getimagesize($pathMini);
//
//            if($width>=$height)
//                $procent=round (200/$width,2);
//            else
//                $procent=round (200/$height,2);
//
//
//
//            $new_width = $width * $procent;
//            $new_height = $height * $procent;
//
//
//            $image_p = imagecreatetruecolor($new_width, $new_height);
//
//            if($modelFiles->format=='jpg' || $modelFiles->format=='jpeg')
//                $image = imagecreatefromjpeg($pathMini);
//            elseif($modelFiles->format=='png')
//                $image = imagecreatefrompng($pathMini);
//
//            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);;
//            imagejpeg($image_p, $globalPath.'avatar_orig_mini_'.Yii::$app->getUser()->id.'.jpg');
//
//
//            list($width, $height) = getimagesize($globalPath.'avatar_orig_mini_'.Yii::$app->getUser()->id.'.jpg');
//
//            if($width>=$height){
//                $new_quadro_size = $height;
//                $src_x=round($width-$height)/2;
//                $src_y=0;
//            }else {
//                $new_quadro_size = $width;
//                $src_x=0;
//                $src_y=round($height-$width)/2;
//            }
//            $image_quadro = imagecreatetruecolor($new_quadro_size, $new_quadro_size);
////
//            imagecopy($image_quadro, $image_p, 0, 0, $src_x, $src_y, $new_quadro_size, $new_quadro_size);
////
//            imagejpeg($image_quadro, $globalPath.'avatar_orig_mini_'.Yii::$app->getUser()->id.'.jpg');
//
//            return true;
//        } else {
//            Yii::trace($modelFiles->getErrors());
//            return false;
//        }
//    }


    public function upload()
    {

        $modelFiles=FilesImage::find()->where(["id_user"=>Yii::$app->getUser()->id,'type'=>1])->one();
        if(!isset($modelFiles))
            $modelFiles=new FilesImage;



        $modelOldName=$modelFiles->name.".".$modelFiles->format;
        $modelOldNameMini=$modelFiles->name_mini.".".$modelFiles->format_mini;

        $modelFiles->name='orig_'.Yii::$app->getUser()->id;
        $modelFiles->name_mini='mini_'.Yii::$app->getUser()->id;
        $modelFiles->type=1;
        $modelFiles->id_user=Yii::$app->getUser()->id;
        $modelFiles->path='files/img/avatar/';
        $modelFiles->format=$this->imageFile->extension;
        $modelFiles->format_mini=$this->imageFile->extension;
        $modelFiles->date_create=date("Y-m-d H:i");


        //dirname(dirname(__DIR__)) . '/files/order/' .$oneFile->folder."/" . $oneFile->name . "." . $oneFile->format

        if ($this->validate() && $modelFiles->validate()) {
            if( !$modelFiles->isNewRecord && file_exists(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' . $modelOldName)){
                unlink(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' . $modelOldName);
            }

            if( !$modelFiles->isNewRecord && file_exists(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' . $modelOldNameMini)){
                unlink(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' . $modelOldNameMini);
            }

            $this->imageFile->saveAs(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' . $modelFiles->name. '.' . $this->imageFile->extension);


            $modelFiles->save();

            Image::thumbnail(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' .$modelFiles->name. '.' . $this->imageFile->extension,
                130,
                130)
                ->save(dirname(dirname(__DIR__)) . '/frontend/web/files/img/avatar/' .$modelFiles->name_mini . '.' . $this->imageFile->extension);

            //   $globalPath=__DIR__.'\..\web\files\img\avatar\\';



            return true;
        } else {
            Yii::trace($modelFiles->getErrors());
            return false;
        }
    }
}