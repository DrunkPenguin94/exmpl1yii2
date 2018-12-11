<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
//        'css/site.css',
        'css/owl.carousel.min.css',
        'css/animate.min.css',
        'css/datepicker.css',
		'css/jquery.formstyler.css',
        'css/jquery.formstyler.theme.css',
        'css/css.css',
        
    ];
    public $js = [
        'js/wow.min.js',
        'js/datepicker.js',

        'js/jquery.jscroll.min.js',
        'js/jquery.formstyler.min.js',
        "js/jquery.mousewheel.min.js",
		"js/jquery.mask.min.js",
        'js/owl.carousel.min.js',
        'js/javascript.js',
        'js/order.js',
        'js/ajax.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
      //  'yii\bootstrap\BootstrapAsset',
    ];
}
