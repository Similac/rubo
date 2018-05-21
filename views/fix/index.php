<?php
use app\assets\AppAsset;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
AppAsset::register($this);

?>
<!-- Main content -->
<section class="content">
  <div class="row">
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">

              <?php $form=ActiveForm::begin([

                  'options'=>[
                    'id'=>'fixForm',
                    'class'=>'form-horizontal',
                    'enctype' => 'multipart/form-data'
                  ],
                  'fieldConfig'=>[
                    'template'=>"{label}\n<div class='col-sm-8'>{input}</div><div class='col-sm-2'>{error}</div>",
                    'labelOptions'=>['class'=>'col-sm-2 control-label']
                  ]
              ]);?> 

              <div class="box-body">  
                  
                        
                <div class="form-group">
                 <?= $form->field($model,'uuid_column')->textInput([
                   'class'=>'form-control',
                   'placeholder'=>'输入uuid列名',
                   'id'=>'uuid'
                 ])?>     
               </div>
                  
                <div class="form-group">
                 <?= $form->field($model,'clickid_column')->textInput([
                   'class'=>'form-control',
                   'placeholder'=>'输入clickid列名',
                   'id'=>'clickid'
                 ])?>     
               </div>
                  
                <div class="form-group">
                  <?= $form->field($model,'upload_file')->fileInput()?>
                </div>
                  
                  
                  <div class="box-footer">
                      <?= Html::submitButton('提交',[
                        'class'=>'btn btn-primary center-block',
                        'id'=>'btn'
                      ])?>
                  </div>
             </div>

               <?php ActiveForm::end();?>
            </div>
       </div>
  </div>
</section>  
<?php

$flashes = Yii::$app->getSession()->getAllFlashes();
foreach($flashes as $k=>$v){
    $err .= $v."</br>";
}
if(isset($err)){
$err = substr($err, 0, -5);
$js=<<<JS
   $(function(){
       swal("","{$err}");
   });
JS;
$this->registerJs($js);    
}

?>