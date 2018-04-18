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

    <title>导数工据</title>
    
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
            <li><a href='<?php echo Url::toRoute(['fix/index'])?>'>补数据</a></li>
          </ul>
          <ul class="nav navbar-nav pull-right" style="margin-right:60px;">
            <li class="dropdown user-menu notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="glyphicon glyphicon-user" style="font-size: 16px" alt="User Image"></span>
                <span class="hidden-xs"><?=Yii::$app->session['user']['username']?>&nbsp;&nbsp;</span>
              </a>
                <ul class="dropdown-menu" style="width:160px;">
                <li class="user-body">
                  <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 50px;">
                      <ul class="menu" style="overflow: hidden; width: 100%; height: 50px;">
                          <li><a href="/index.php?r=site%2Flogout"></i> 退出</a></li>
                      </ul>
                  </div>
                </li>
              </ul>
            </li>
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
