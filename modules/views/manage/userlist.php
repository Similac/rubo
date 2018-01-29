<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\widgets\LinkPager;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>

	<!-- main container -->
    <div class="content">
      
        <div class="container-fluid">
            <div id="pad-wrapper" class="users-list">
                <div class="row-fluid header">
                    <h3>Users</h3>
                    <div class="span10 pull-right">
                        <input type="text" class="span5 search" placeholder="Type a user's name..." />
                        
                        <!-- custom popup filter -->
                        <!-- styles are located in css/elements.css -->
                        <!-- script that enables this dropdown is located in js/theme.js -->
                        <div class="ui-dropdown">
                            <div class="head" data-toggle="tooltip" title="Click me!">
                                Filter users
                                <i class="arrow-down"></i>
                            </div>  
                            <div class="dialog">
                                <div class="pointer">
                                    <div class="arrow"></div>
                                    <div class="arrow_border"></div>
                                </div>
                                <div class="body">
                                    <p class="title">
                                        Show users where:
                                    </p>
                                    <div class="form">
                                        <select>
                                            <option />Name
                                            <option />Email
                                            <option />Number of orders
                                            <option />Signed up
                                            <option />Last seen
                                        </select>
                                        <select>
                                            <option />is equal to
                                            <option />is not equal to
                                            <option />is greater than
                                            <option />starts with
                                            <option />contains
                                        </select>
                                        <input type="text" />
                                        <a class="btn-flat small">Add filter</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href='<?php echo Url::to(["user/useradd"])?>' class="btn-flat success pull-right" id='test'>
                            <span>&#43;</span>
                            添加管理员
                        </a>
                    </div>
                </div>

                <!-- Users table -->
                <div class="row-fluid table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="span2">
                                    管理员ID
                                </th>
                                <th class="span2">
                                    <span class="line"></span>管理员账号
                                </th>
                                <th class="span2">
                                    <span class="line"></span>管理员邮箱
                                </th>
                                <th class="span3">
                                    <span class="line"></span>最后登录时间
                                </th>
                                <th class="span3">
                                    <span class="line"></span>最后登录IP
                                </th>
                                <th class="span2">
                                    <span class="line"></span>添加时间
                                </th>
                                <th class="span2">
                                    <span class="line"></span>操作
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- row -->
                        <?php foreach ($models as $model):?>
                        <tr class="first">
                            <td>
                                <?php echo $model->adminid;?>
                            </td>
                            <td>
                                <?php echo $model->adminuser; ?>
                            </td>
                            <td>
                                <?php echo $model->adminemail; ?>
                            </td>
                            <td class="align-right">
                                <?php echo date('Y-m-d H:i:s', $model->logintime); ?>
                            </td>
                            <td class="align-right">
                                <?php echo long2ip($model->loginip); ?>
                            </td>
                            <td>
                                <?php echo date("Y-m-d H:i:s", $model->createtime); ?>
                            </td>
                            <td class="align-right">
                            <?php if ($model->adminid != 1): ?>
                            <a href="<?php echo Url::to(['user/del', 'adminid' => $model->adminid]) ?>">删除</a>
                            <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                        <!-- row -->
                        </tbody>
                    </table>
                    <?php
                        if(Yii::$app->session->hasFlash('info'))
                        {
                            echo Yii::$app->session->getFlash('info');
                        }
                    ?>
                </div>
                <div class="pagination pull-right">
                    <?php
                        echo LinkPager::widget([
                            'pagination'=>$pages,
                        ]);
                    ?>
                </div>
                <!-- end users table -->
            </div>
        </div>
    </div>
    <!-- end main container -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>