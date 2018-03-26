<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="zh-CN">
<?php $this->head() ?>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>导数据</title>
    
    <!-- Bootstrap core CSS -->
    <style>
        .navbar-brand p{
          color: #ddd;
        }

        .navbar-brand p b{
          color:#9948a2;
        }
    </style>
  </head>
<body>
<?php $this->beginBody() ?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
            <div class="navbar-brand">
              <p><b>Mob</b>vista</p>
            </div>
        </div>
        <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
          <ul class="nav navbar-nav navbar-right pull-left">
            <li><a href='<?php echo Url::toRoute(['load/index'])?>'>hadoop数据</a></li>
            <li><a href='<?php echo Url::toRoute(['redshift/index'])?>'>redshift数据</a></li>
            <li><a href='<?php echo Url::toRoute(['redshift/deducted'])?>'>匹配扣量</a></li>
            <li><a href='<?php echo Url::toRoute(['load/list'])?>'>查看任务列表</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <hr>
    <?= $content; ?>
    
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
