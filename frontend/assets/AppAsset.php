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
        'css/site.css',

        'jQuery.filer-1.3.0/css/jquery.filer.css'
    ];
    public $js = [
        'jQuery.filer-1.3.0/js/jquery.filer.min.js',
        'jQuery.filer-1.3.0/js/custom.jquery.filler.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
