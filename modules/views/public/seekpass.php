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
<body>


    <div class="row-fluid login-wrapper">
        <a class="brand" href="index.html"></a>
        <?php $form=ActiveForm::begin([
            'fieldConfig'=>[
                'template'=>'{input}{error}'
            ],
            'enableClientValidation'=>true,
            'id'=>'form-id',
        ])?>
        <div class="span4 box">
            <div class="content-wrap">
                <h6>慕课商城 - 找回密码</h6>
                <?php
                    if(Yii::$app->session->hasFlash('info'))
                    {
                        echo Yii::$app->session->getFlash('info');
                    }
                ?>
                <?= $form->field($model,'adminuser')->textInput(['class'=>'span12','placeholder'=>'管理员账号'])?>
                <?= $form->field($model,'adminemail')->textInput(['class'=>'span12','placeholder'=>'管理员邮箱']) ?>
                <a href='<?php echo Url::to(["public/login"])?>' class="forgot">返回登录</a>
                <?= Html::submitButton('发送邮件',['class'=>'btn-glow primary login','id'=>'btn',['enableAjaxValidation'=>true]])?>
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

    <script type="text/javascript">
        $(function () {
            jQuery('form#form-id').on('beforeSubmit', function (e) {
                var $form = $(this);
                $.ajax({
                    url: '<?php echo Url::to(["public/seekpass"]);?>',
                    type: 'post',
                    data: $form.serialize(),
                    success: function (data) 
                    {   
                        if(data.code==0)
                        {
                            $(".field-admin-adminemail p").text(data.msg);
                        }else
                        {
                            towait();
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

    <script>
        var wait = 60;

        function towait() {
            if (wait == 0) {
                $("#btn").removeAttr("disabled");
                $("#btn").text("value", "发送邮件");
                wait = 60;
            } else {
                $("#btn").attr("disabled", true);
                $("#btn").text("已发送,重新发送(" + wait + "s)");
                wait--;
                // setTimeout() 方法用于在指定的毫秒数后调用函数或计算表达式
                setTimeout(function() {         // 定时执行
                    towait();
                }, 1000);
            }
        }
    </script>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>