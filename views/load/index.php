<?php
use app\assets\AppAsset;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
AppAsset::register($this);

?>

<div class="container">
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        
        <!-- form start -->
        
        <?php $form=ActiveForm::begin([

            'enableAjaxValidation'=>true,
            'validationUrl' => Url::to(['load/validation']),
            'options'=>[
              'id'=>'loadForm',
              'class'=>'form-horizontal',
            ],
            'fieldConfig'=>[
              'template'=>"{label}\n<div class='col-sm-8'>{input}</div><div class='col-sm-2'>{error}</div>",
              'labelOptions'=>['class'=>'col-sm-2 control-label']
            ]
        ]);?>
          <div class="box-body">

            <div class="box-header with-border">
              <h3 class="box-title">选择数据源</h3>
            </div><!-- /.box-header -->
            <!-- radio -->
            <div class="form-group">
              
              <?php
                $model->source ? $model->source=$model->source:$model->source=0;
                echo $form->field($model,'source')->inline(true)->radioList([
                  0=>'install_log',
                  1=>'click_log',
                  2=>'event_log',
                  3=>'injectcode'
                ]);
              ?>
              
            </div>
           
            <div class="box-header with-border">
              <h3 class="box-title">筛选条件</h3>
            </div><!-- /.box-header -->

            <div class="form-group">
 
              <?= $form->field($model, 'start_time')->widget(DateTimePicker::classname(), [
                   'options' => ['placeholder' => '每天的开始时间为00:00'],
                   'pluginOptions' => [
                       'format' => 'yyyy-mm-dd hh:ii',
                       'startDate' => '01-Mar-2014 12:00 AM',
                       'todayHighlight' => true,
                       'autoclose'=>true,
                       //'todayBtn'=>true,
                       'minuteStep'=>60
                     ]
               ]);
              ?>
            </div>

            <div class="form-group">
              <?= $form->field($model, 'end_time')->widget(DateTimePicker::classname(), [
                   'options' => ['placeholder' => '每天的结束时间为23:00'],
                   'pluginOptions' => [
                       'format' => 'yyyy-mm-dd hh:ii',
                       'startDate' => '01-Mar-2014 12:00 AM',
                       'todayHighlight' => true,
                       'autoclose'=>true,
                       //'todayBtn'=>true,
                       'minuteStep'=>60,
                     ]
               ]);
              ?>
              
            </div>

            <?php if(isset($model->created_at)):?>
              <div class="form-group">
                <?= $form->field($model,'uuid')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入uuid换行隔开',
                  'readonly'=>'true'
                ])?>       
              </div>
            <?php else:?>
              <div class="form-group">
                <?= $form->field($model,'uuid')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入uuid换行隔开,uuid和network必须填写其中一个',
                  'id'=>'uuid'
                ])?>     
              </div>
            <?php endif;?>

            <?php if(isset($model->created_at)):?>
              <div class="form-group" id="network">
                <?= $form->field($model,'network')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入渠道换行隔开',
                  'readonly'=>'true'
                ])?>
              </div>
            <?php else:?>
              <div class="form-group" id="network">
                <?= $form->field($model,'network')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入渠道换行隔开,uuid和network必须填写其中一个',
                  'id'=>'network'
                ])?>
              </div>
            <?php endif;?>

            <div class="form-group" id="match_type">
              <?php
                $model->match_type ? $model->match_type = $model->match_type:$model->match_type='0';
                echo $form->field($model,'match_type')->inline(true)->radioList([
                  0=>'不匹配',
                  1=>'输入clickid',
                  2=>'输入idfa'
                ]);
              ?>
            </div>

            <?php if(isset($model->created_at)):?>
              <div class="form-group" id="clickid">
                <?= $form->field($model,'clickid')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入clickid换行隔开',
                  'readonly'=>'true'
                ])?>
              </div>
            <?php else:?> 
             
              <div class="form-group" id="clickid">
                <?= $form->field($model,'clickid')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入clickid换行隔开',
                ])?>
              </div>
            <?php endif;?>
  
            <?php if(isset($model->created_at)):?>
              <div class="form-group" id="idfa">
                <?= $form->field($model,'idfa')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入idfa换行隔开',
                  'readonly'=>'true'
                ])?>
              </div>
            <?php else:?>
              <div class="form-group" id="idfa">
                <?= $form->field($model,'idfa')->textArea([
                  'class'=>'form-control',
                  'placeholder'=>'输入idfa换行隔开',
                ])?>
              </div>
            <?php endif;?>  
          </div><!-- /.box-body -->
          <div class="box-header with-border">
            <h3 class="box-title">输出类型</h3>
          </div><!-- /.box-header -->
          <div class="form-group">
              <?php
                $model->export_type=1;
                echo $form->field($model,'export_type')->inline(true)->radioList([
                  
                  1=>'json',
                ]);
              ?>
          </div>

          <?php if(!isset($model->created_at)):?>
          <div class="box-footer">
            <?= Html::submitButton('提交',[
              'class'=>'btn btn-primary center-block',
              'id'=>'btn'
            ])?>
          </div><!-- /.box-footer -->
        <?php endif;?>
          
        <?php ActiveForm::end();?>
      </div><!-- /.box -->
  </div>
  </section>
</div>

<?php

$success_img=Url::to('@web/static/img/weiwei.jpg',true);
$warning_img=Url::to('@web/static/img/jinjian.jpg',true);
$js=<<<js
$("#idfa").hide();
$("#clickid").hide();

$('input:radio[name="Load[source]"]').click(function(){
  if($(this).val()==3)
  {
    $("#clickid").hide();
    $("#idfa").hide();
    $("#network").hide();
    $("#match_type").hide();
  }else{
    $("#network").show();
    $("#match_type").show();
    
  }
})


$('input:radio[name="Load[match_type]"]').click(function(){
  if($(this).val()==0)
  {
    $("#idfa").hide();
    $("#clickid").hide();
  }
  if($(this).val()==1)
  {
    $("#idfa").hide();
    $("#clickid").show();
  }
  if($(this).val()==2)
  {
    $("#clickid").hide();
    $("#idfa").show();
  }
})

$(document).on('beforeSubmit', 'form#loadForm', function () {
    // if($("#uuid").val()=='' && $("#network").val()=='')
    // {
    //   swal({
    //         title: '<small style="color:red">uuid和network必须填写其中一个</small>',
    //         text: '3秒后自动关闭',
    //         //type: 'info',
    //         timer:3000,
    //         imageUrl: "$warning_img"
    //       })
    //   return false;
    // }
    var form = $(this); 
    $('#btn').attr('disabled',"true").text("Loaing....");

    $.ajax({
      url  : form.attr('action'), 
      type  : 'post', 
      data  : form.serialize(), 
      success: function (response){
        if(response.status){
          $('#btn').removeAttr("disabled").text("提交");
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            //type: 'success',
            timer:3000,
            imageUrl:"$success_img"
          })
          
        }
        else{
          $('#btn').removeAttr("disabled").text("提交");
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            //type: 'warning',
            timer:3000,
            imageUrl:"$warning_img"
          })
        }
      }, 
      error : function (){
        $('#btn').removeAttr("disabled").text("提交");
        swal({
            title: '系统错误',
            text: '3秒后自动关闭',
            //type: 'error',
            timer:3000,
            imageUrl:"$warning_img"
        })
      } 
    }); 
    return false; 
  }); 

js;

$this->registerJs($js); 
?>