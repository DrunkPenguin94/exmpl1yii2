<?php
namespace frontend\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use common\models\FilesImage;
use common\models\Portfolio;
use common\models\LastMessage;
use yii\imagine\Image;
class UploadPortfolio extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'mimeTypes'=>["image/jpeg","image/png"],'maxSize'=>2000000],

        ];
    }
    public function attributeLabels()
    {
        return [
            'imageFile' => '',

        ];
    }




//    public function upload()
//    {
//
//
//        $modelFiles=FilesImage::find()
//            ->where(["id"=>Yii::$app->getUser()->id])
//            ->count();
//
//        if($modelFiles>25) return false;
//
//        $modelFiles=new FilesImage;
//
//        $const_w=500;
//        $const_h=320;
//
//        $nameFileNumber=strtotime(date("Y-m-d H:i"))."_".rand(0, 9999999);
//        $nameFileNumberMini=strtotime(date("Y-m-d H:i"))."_".rand(0, 9999999);
//
//        $modelFiles->name=$nameFileNumber;
//        $modelFiles->name_mini=$nameFileNumberMini;
//        $modelFiles->type=2;
//        $modelFiles->id_user=Yii::$app->getUser()->id;
//        $modelFiles->path='files/img/portfolio/';
//        $modelFiles->format=$this->imageFile->extension;
//        $modelFiles->format_mini='jpg';
//        $modelFiles->date_create=date("Y-m-d H:i");
//
//
//
//
//
//        if ($this->validate() && $modelFiles->validate()) {
//            $this->imageFile->saveAs('files/img/portfolio/' .$nameFileNumber . '.' . $this->imageFile->extension);
//
//
//            $modelFiles->save();
//
//
//
//
////            $globalPath=__DIR__.'/../web/files/img/portfolio/';
//             $globalPath=dirname(dirname(__DIR__)).'/frontend/web/files/img/portfolio/';
////
//
//            $pathMini= $globalPath.$modelFiles->name.".".$modelFiles->format;
//
//
//            list($width, $height) = getimagesize($pathMini);
//
//
//            if($width/$height> 1){
//                $procent=round ($const_h/$height,2);
//            }else{
//                $procent=round ($const_w/$width,2);
//            }
//
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
//            imagejpeg($image_p, $globalPath.$nameFileNumberMini.'.jpg');
//
//
//            list($width, $height) = getimagesize($globalPath.$nameFileNumberMini.'.jpg');
//
//            if($width/$height> 1){
//                $src_x=round($width-$const_w)/2;
//                $src_y=0;
//            }else {
//                $src_x=0;
//                $src_y=round($height-$const_h)/2;
//            }
//            $image_quadro = imagecreatetruecolor($const_w, $const_h);
//////
//            imagecopy($image_quadro, $image_p, 0, 0, $src_x, $src_y, $const_w, $const_h);
////
//            imagejpeg($image_quadro, $globalPath.$nameFileNumberMini.'.jpg');
//
//            return true;
//        } else {
//            Yii::trace($modelFiles->getErrors());
//            return false;
//        }
////    }


        public function upload()
    {


        $modelFiles=FilesImage::find()
            ->where(["id"=>Yii::$app->getUser()->id])
            ->count();

        if($modelFiles>25) return false;

        $modelFiles=new FilesImage;

        $const_w=500;
        $const_h=320;

        $nameFileNumber=strtotime(date("Y-m-d H:i"))."_".rand(0, 9999999);
        $nameFileNumberMini=strtotime(date("Y-m-d H:i"))."_".rand(0, 9999999);

        $modelFiles->name=$nameFileNumber;
        $modelFiles->name_mini=$nameFileNumberMini;
        $modelFiles->type=2;
        $modelFiles->id_user=Yii::$app->getUser()->id;
        $modelFiles->path='files/img/portfolio/';
        $modelFiles->format=$this->imageFile->extension;
        $modelFiles->format_mini=$this->imageFile->extension;
        $modelFiles->date_create=date("Y-m-d H:i");





        if ($this->validate() && $modelFiles->validate()) {
            $this->imageFile->saveAs(dirname(dirname(__DIR__)) . '/frontend/web/files/img/portfolio/' .$nameFileNumber . '.' . $this->imageFile->extension);


            $modelFiles->save();

            Image::thumbnail(dirname(dirname(__DIR__)) . '/frontend/web/files/img/portfolio/' .$nameFileNumber . '.' . $this->imageFile->extension,
                500,
                320)
                ->save(dirname(dirname(__DIR__)) . '/frontend/web/files/img/portfolio/' .$nameFileNumberMini . '.' . $this->imageFile->extension);






            return true;
        } else {
            Yii::trace($modelFiles->getErrors());
            return false;
        }
    }
}