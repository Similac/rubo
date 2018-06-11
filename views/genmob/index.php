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
                'enableAjaxValidation'=>true,
                'validationUrl' => Url::to(['genmob/validation']),
                'options'=>[
                  'id'=>'fixForm',
                  'class'=>'form-horizontal',
                  'enctype' => 'multipart/form-data',
                ],
                'fieldConfig'=>[
                  'template'=>"{label}\n<div class='col-sm-8'>{input}</div><div class='col-sm-2'>{error}</div>",
                  'labelOptions'=>['class'=>'col-sm-2 control-label']
                ]
            ]);?> 

            	<div class="box-body">  
                
                <div class="form-group">
                  <?php $model->is_input='0';?>
                  <?= $form->field($model,'is_input')->inline(true)->radioList([
                    '1'=>'是',
                    '0'=>'否'
                  ]);?>
                </div>

                <div class="form-group" id='mob_input' style="display:none;">
                 <?= $form->field($model,'mob_input')->textArea([
                   'class'=>'form-control',
                   'placeholder'=>'换行输入,总共19位（例如mob1234567890123456）',
                   
                 ])?>     
               	</div>
                  
                <div class="form-group">
                 <?= $form->field($model,'nums')->textInput([
                   'class'=>'form-control',
                   'placeholder'=>'请输入共需生成的mob号数量(含上一步指定的mob号)',
                 ])?>     
               	</div>
                  
                <div class="form-group">
                  <?php $model->format='0';?>
                  <?= $form->field($model,'format')->radioList([
                    '0'=>'完全平均-每个mob号点击，转化率，流量构成比例等指标相近-BT效果极佳',
                    '1'=>'比例分配-每个mob号点击不同；但转化率，流量构成比例等指标相近-BT效果极佳',
                    '2'=>'周期分配-加入时间因素进行流量分配，每个mob号在不同时间点被选取到的几率不同，因此点击，转化率，流量构成比例等指标不同-BT效果佳'
                  ]);?>
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

$js=<<<JS
   $(function(){
      
      $('input:radio[name="Genmob[is_input]"]').click(function(){
        if($(this).val()==0)
        {
          $("#mob_input").hide();
        }else{
          $("#mob_input").show();
        }
      })
   });
JS;
$this->registerJs($js);

?>