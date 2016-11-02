<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/bootstrap.min.css',
        //'css/font-awesome.min.css',
        //'css/ionicons.min.css',
        'css/AdminLTE.min.css',
        'css/_all-skins.min.css',
        'css/jquery.dataTables.min.css',
        'css/dataTables.responsive.css',
        //'//cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css',
    ];
    public $js = [
        'js/ckeditor/ckeditor.js',
        'js/bootstrap.min.js',
        'js/jquery-1.12.0.min.js',
        //'js/clipboard.js-master/dist/clipboard.js',
        'js/jquery.dataTables.min.js',
        'js/dataTables.responsive.js',
       // 'app.min.js', //// commented
        //'//code.jquery.com/jquery-1.12.0.min.js',
        //'//cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
    public $jsOptions = [ 'position' => \yii\web\View::POS_HEAD ];
}
