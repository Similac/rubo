<?php
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
$this->registerCssFile("@web/static/css/style.css");
?>
<body>
<div id="particles-js"></div>
    <div class="main-w3layouts">
    <h1>Astounding Error Page</h1>
        <div class="main-agile">
            <h2>Error 404</h2>
            <p>It's looking like you may have taken a wrong turn. Don't worry... It happens to the best of us.</p>
        <div class="botton-w3ls"><a href="<?php echo Url::to(['load/index'],true);?>">Back to home</a></div>
        </div>
    </div>
</body>