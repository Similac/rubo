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
        'static/css/AdminLTE.min.css',
        'static/css/sweetalert2.min.css',
        'static/css/bootstrap-tagsinput.css'
    ];
    public $js = [
      'static/js/sweetalert2.min.js',
      'static/js/jquery.min.js',
      'static/js/bootstrap.min.js',
      'static/js/bootstrap-tagsinput.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
