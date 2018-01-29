<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html class="login-bg">
<head>
	<title>慕课商城 - 后台管理</title>
    
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	
    <!-- bootstrap -->
    <link href="static/admin/css/bootstrap/bootstrap.css" rel="stylesheet" />
    <link href="static/admin/css/bootstrap/bootstrap-responsive.css" rel="stylesheet" />
    <link href="static/admin/css/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />

    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="static/admin/css/layout.css" />
    <link rel="stylesheet" type="text/css" href="static/admin/css/elements.css" />
    <link rel="stylesheet" type="text/css" href="static/admin/css/icons.css" />

    <!-- libraries -->
    <link rel="stylesheet" type="text/css" href="static/admin/css/lib/font-awesome.css" />
    
    <!-- this page specific styles -->
    <link rel="stylesheet" href="static/admin/css/compiled/signin.css" type="text/css" media="screen" />
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
<?php $this->beginBody() ?>
<body>
    <div class="row-fluid login-wrapper">
        <a class="brand" href="index.html"></a>
        <?php $form=ActiveForm::begin([
            'fieldConfig'=>[
                'template'=>'{input}{error}'
            ],
            //'enableAjaxValidation'=>true,
            'enableClientValidation'=>true,
            //'validationUrl'=>Url::to(['manage/validation']),
            'id'=>'form-id',
        ])?>
        <div class="span4 box">
            <div class="content-wrap">
                <h6>慕课商城 - 修改密码</h6>
                <?php
                    if(Yii::$app->session->hasFlash('info'))
                    {
                        echo Yii::$app->session->getFlash('info');
                    }
                ?>
                <?= $form->field($model,'adminpass')->passwordInput(['class'=>'span12','placeholder'=>'新密码'])?>
                <?= $form->field($model,'repass')->textInput(['class'=>'span12','placeholder'=>'确认密码']) ?>
                <a href='<?php echo Url::to(["public/login"])?>' class="forgot">返回登录</a>
                <?= Html::submitButton('登录',['class'=>'btn-glow primary login','id'=>'btn'])?>
            </div>
        </div>
        <?php ActiveForm::end()?>
    </div>

	<!-- scripts -->
    <script src="static/admin/js/jquery-latest.js"></script>
    <script src="static/admin/js/bootstrap.min.js"></script>
    <script src="static/admin/js/theme.js"></script>

    <!-- pre load bg imgs -->
    <script type="text/javascript">
        $(function () {
            // bg switcher
            var $btns = $(".bg-switch .bg");
            $btns.click(function (e) {
                e.preventDefault();
                $btns.removeClass("active");
                $(this).addClass("active");
                var bg = $(this).data("img");

                $("html").css("background-image", "url('img/bgs/" + bg + "')");
            });

        });
    </script>
    <script>
        $(function () {
            jQuery('form#form-id').on('beforeSubmit', function (e) {
                var $form = $(this);
                $.ajax({
                    url: '<?php echo Url::to(["manage/mailchangepass"]);?>',
                    type: 'post',
                    data: $form.serialize(),
                    success: function (data) 
                    {
                        if(data.code==1)
                        {
                            alert(data.msg);
                            window.location.reload(true);
                        }
                        else
                        {
                            alert(data.msg);
                        }
                    },
                    error: function()
                    {
                        alert('网络错误');
                    }
                });
            }).on('submit', function (e) {
                e.preventDefault();
            });
           });
    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>