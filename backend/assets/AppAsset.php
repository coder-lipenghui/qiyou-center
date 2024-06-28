<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://unpkg.com/element-ui/lib/theme-chalk/index.css',
        'css/site.css',
        'bootstrap-select/css/bootstrap-select.css',
    ];
    public $js = [
        'js/vue.min.js',
        'bootstrap-select/js/bootstrap-select.js',
        'https://unpkg.com/element-ui/lib/index.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapThemeAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

//    //定义按需加载JS方法，注意加载顺序在最后
//    public static function addScript($view, $jsfile) {
//        $view->registerJsFile($jsfile);
//    }
//
//    //定义按需加载css方法，注意加载顺序在最后
//    public static function addCss($view, $cssfile) {
//        $view->registerCssFile($cssfile);
//    }
}
