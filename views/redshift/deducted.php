<?php
use app\assets\AppAsset;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use bootui\typeahead\Typeahead;
AppAsset::register($this);

?>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
        
        <!-- form start -->
        
        <?php $form=ActiveForm::begin([

            'options'=>[
              'id'=>'loadForm',
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

          <div class="form-group">
            <?= $form->field($model, 'advertiser')
              ->widget(Typeahead::className(),[
                      'source' => $advertisers, 
                      'limit' => 10, 
                      'scrollable' => true,
                      //'addon' => ['prepend' => 'Autocomplete'],
            ]) ?>
          </div>
          
          <div class="form-group">
            <?= $form->field($model,'upload_file')->fileInput()?>
          </div>
          
          <div class="form-group">
            <?= $form->field($model,'match_type')->inline()->radiolist([
              0=>'clickid',
              1=>'devid'
            ])?>
          </div>

          <div class="form-group">
            <?= $form->field($model,'clickid_column')->textInput()?>
          </div>
        <div class="box-footer">
            
            <?= Html::submitButton('提交',[
              'class'=>'btn btn-primary center-block',
              'id'=>'btn'
            ])?>
        </div><!-- /.box-footer -->

        <?php ActiveForm::end();?>
      </div><!-- /.box -->
  </div>
</section>
<?php
$js=<<<js
// $(document).on('beforeSubmit', 'form#loadForm', function () {
//     // if($("#uuid").val()=='' && $("#network").val()=='')
//     // {
//     //   swal({
//     //         title: '<small style="color:red">uuid和network必须填写其中一个</small>',
//     //         text: '3秒后自动关闭',
//     //         //type: 'info',
//     //         timer:3000,
//     //       
//     //       })
//     //   return false;
//     // }
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