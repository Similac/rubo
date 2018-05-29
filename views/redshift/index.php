<?php
use app\assets\AppAsset;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use bootui\typeahead\Typeahead;
use wbraganca\tagsinput\TagsinputWidget;
use app\common\func;
AppAsset::register($this);

?>
<style>
  .bootstrap-tagsinput
  {
    width: 100%;
  }
</style>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <!-- form start -->    
        <?php $form=ActiveForm::begin([
            'enableAjaxValidation'=>true,
            'validationUrl' => Url::to(['redshift/validation']),
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

            <div class="form-group">
              <?= $form->field($model, 'start_time')->widget(DateTimePicker::classname(), [
                   'pluginOptions' => [
                      'format' => 'yyyy-mm-dd hh:ii',
                      'startDate' => '01-Mar-2014 12:00 AM',
                      'todayHighlight' => true,
                      'autoclose'=>true,
                      //'todayBtn'=>true,
                      'minuteStep'=>30
                      //'minView'=>'month'
                    ]
               ]);
              ?>
            </div>

            <div class="form-group">
              <?= $form->field($model, 'end_time')->widget(DateTimePicker::classname(), [
                   'pluginOptions' => [
                       'format' => 'yyyy-mm-dd hh:ii',
                       'startDate' => '01-Mar-2014 12:00 AM',
                       'todayHighlight' => true,
                       'autoclose'=>true,
                       //'todayBtn'=>true,
                       'minuteStep'=>30,
                       //'minView'=>'month'
                     ]
               ]);
              ?>
            </div>
            
            <div class="form-group">
              <?php if(func::getRole()['role']=='to'):?>
                <?php $model->type=0;?>
                <?= $form->field($model,'type')->inline()->radioList([
                  '0'=>'uuid维度',
                ])?>
              <?php else:?>
                <?php $model->type=0;?>
                <?= $form->field($model,'type')->inline()->radioList([
                  '0'=>'uuid维度',
                  '1'=>'advertiser维度'
                ])?>
              <?php endif;?>   
            </div>
            
            <div class="form-group" id="uuid">
              <?= $form->field($model, 'uuid')->widget(TagsinputWidget::classname(), [
                  'clientOptions' => [
                      'trimValue' => true,
                      'allowDuplicates' => false
                  ]
              ]) ?>   
            </div>
            
            <div class="form-group" id="network">
              <?= $form->field($model, 'network')->widget(TagsinputWidget::classname(), [
                  'clientOptions' => [
                      'trimValue' => true,
                      'allowDuplicates' => false
                  ]
              ]) ?>    
            </div>
            
            <div class="form-group" id="advertiser" style="display:none">
            <?= $form->field($model, 'advertiser')
              ->widget(Typeahead::className(),[
                      'source' => $advertisers, 
                      'limit' => 10, 
                      'scrollable' => true,
                      //'addon' => ['prepend' => 'Autocomplete'],
            ]) ?>
            </div>
            
            <div class="form-group">
            <?= $form->field($model, 'select')->inline()->checkboxList([
              '0'=>'cti',
              '1'=>'channel_manager',
              '2'=>'click_timestamp',
              '3'=>'install_timestamp',
              '4'=>'click_ip',
              '5'=>'click_date',
              '6'=>'install_date',
              '7'=>'impression tag',
              '8'=>'is_bt',
              '9'=>'match_type'
            ]);?>
            </div>
                   
            <div class="box-footer">
              <?= Html::submitButton('提交',[
                'class'=>'btn btn-primary center-block',
                'id'=>'btn'
              ])?>
            </div>
          <!-- /.box-footer -->
        <?php ActiveForm::end();?>
        </div><!-- /.box -->
      </div>
    </div>
  </div>    
</section>
<?php
$uuid=$model->uuid='';
$js=<<<js

$('input:radio[name="Redshift[type]"]').click(function(){
  if($(this).val()==1)
  {
    $("#uuid").hide();
    $("#network").hide();
    $("#advertiser").show();

  }
  else
  {
    $("#advertiser").hide();
    $("#uuid").show();
    $("#network").show();
  }
})

// $(document).on('beforeSubmit', 'form#loadForm', function () {

//     var form = $(this); 
//     $('#btn').attr('disabled',"true").text("Loaing....");
    
//     $.ajax({
//       url  : form.attr('action'), 
//       type  : 'post', 
//       data  : form.serialize(), 
//       success: function (response){
//         if(response.status){
//           $('#btn').removeAttr("disabled").text("提交");
//           swal({
//             title: response.msg,
//             text: '3秒后自动关闭',
//             type: 'success',
//             timer:3000,
            
//           })
          
          
//         }
//         else{
//           $('#btn').removeAttr("disabled").text("提交");
//           swal({
//             title: response.msg,
//             text: '3秒后自动关闭',
//             type: 'warning',
//             timer:3000,
            
//           })
//         }
//       }, 
//       error : function (){
//         $('#btn').removeAttr("disabled").text("提交");
//         swal({
//             title: '系统错误',
//             text: '3秒后自动关闭',
//             type: 'error',
//             timer:3000,
            
//         })
//       } 
//     }); 
//     return false; 
//   }); 


js;
$this->registerJs($js); 
?>