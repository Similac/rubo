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
        
        <!-- form start -->
        
        <?php $form=ActiveForm::begin([

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
              <?= $form->field($model,'uuid')->textInput([
                'class'=>'form-control',
                'placeholder'=>'输入uuid换行隔开,uuid和network必须填写其中一个',
                'id'=>'uuid'
              ])?>     
            </div>
            
            <div class="form-group">
              <?= $form->field($model,'network')->textInput([
                'class'=>'form-control',
                'placeholder'=>'输入渠道名称换行隔开,uuid和network必须填写其中一个',
                'id'=>'uuid'
              ])?>     
            </div>
            <div class="form-group">
              <?= $form->field($model,'advertiser')->textInput([
                'class'=>'form-control',
                'placeholder'=>'输入广告主名称换行隔开',
                'id'=>'uuid'
              ])?>     
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
  <div class="box-body table-responsive no-padding">
    <table class="table table-hover" style="border-collapse:separate; border-spacing:0px 20px;">
      共有<?php echo isset($count[0]['count'])?$count[0]['count']:'0';?>条数据,只显示10条数据
      <?php if(!empty($data)):?>
      <a class="btn btn-primary pull-right" href="<?php echo Url::to(['redshift/export','start_time'=>$start_time,'end_time'=>$end_time,'uuid'=>$uuid],true)?>">导出</a>
      <?php endif;?>
      <tr>
        <th>时间</th>
        <th>uuid</th>
        <th>渠道</th>
        <th>subid</th>
        <th>clickid</th>
        <th>扣量</th>
        <th>渠道clickid</th>
      </tr>
      <?php if(!empty($data)):?>
      <?php foreach ($data as $v):?>
        <tr>
          <td><?php echo $v['received_date'];?></td>
          <td><?php echo $v['uuid'];?></td>
          <td><?php echo $v['network'];?></td>
          <td><?php echo $v['mb_subid'];?></td>
          <td><?php echo $v['p3'];?></td>
          <td><?php echo $v['defraud'];?></td>
          <td><?php echo substr($v['mb_af_1'], 0,15).'...';?></td>
        </tr>
      <?php endforeach;?>
      <?php else:?>
      <tr><td>数据为空</td></tr>
    <?php endif;?>
    </table>
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