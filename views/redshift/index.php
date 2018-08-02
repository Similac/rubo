<?php
use app\assets\AppAsset;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\common\func;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Alert;
AppAsset::register($this);
?>
<style>
  .bootstrap-tagsinput input{
    width: 766.66px;
  }
</style>
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <!-- Horizontal Form -->
      <div class="box box-info">
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
                      'minuteStep'=>30
                      //'minView'=>'month'
                    ]
               ]);
              ?>
          </div>
          
          <div class="form-group">
            <?php $model->source=0;?>
            <?= $form->field($model,'source')->inline()->radioList([
                  '0'=>'conversion data',
                  '1'=>'install+reject data',
                  '2'=>'event data'
            ])?>
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
            <?= $form->field($model,'uuid')->textInput(['id'=>'uuids']);?>
          </div>
          
          <div class="form-group" id="network">
            <?= $form->field($model,'network')->textInput(['id'=>'networks']);?>
          </div>
          
          <div class="form-group" id="advertiser" style="display:none">
            <?= $form->field($model,'advertiser')->textInput(['id'=>'advertisers']);?>
          </div>
          
          <div class="form-group" id="conversion_log">
            <?php  $model->install_select = [1,2,3,4,5,7,8,9,10,12,13,20,23,24,26,27,28,86,87,88];?>
            <?= $form->field($model, 'install_select')->inline()->checkboxList(
              ArrayHelper::map($install_selects,'id','content'),['class' => 'col-sm-8','itemOptions'=>array('style'=>'margin:4px')]
            );?>
          </div>

          <div class="form-group" id="raw_install_log" style="display:none">
            <?php  $model->raw_install_select = [29,30,31,32,33,35,36,37,38,40,41,42,44,45,46,52,55,56,58,59,60,61,62];?>
            <?= $form->field($model, 'raw_install_select')->inline()->checkboxList(
              ArrayHelper::map($select_for_raw_install,'id','content'),['class' => 'col-sm-8','itemOptions'=>array('style'=>'margin:4px')]
            );?>
          </div>
          
          <div class="form-group" id="event_log" style="display:none">
            <?php  $model->event_select = [63,64,65,66,67,68,36,69,71,72,73,74,75,76,79,81,82,84,85,91,92,93,94];?>
            <?= $form->field($model, 'event_select')->inline()->checkboxList(
              ArrayHelper::map($select_for_event,'id','content'),['class' => 'col-sm-8','itemOptions'=>array('style'=>'margin:4px')]
            );?>
          </div>
          
          <div class="box-footer">
              <?= Html::submitButton('提交',[
                'class'=>'btn btn-primary center-block',
                'id'=>'btn'
              ])?>
              <a href="#" id="download_url" style="display:none" class="btn btn-info">下载log</a>
          </div>
          <?php ActiveForm::end();?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php $this->beginBlock('typeahead') ?>
  
$(document).on('beforeSubmit', 'form#loadForm', function () {
    
    var form = $(this); 
    $('#btn').attr('disabled',"true").text("Loaing....");
    
    $.ajax({
      url  : form.attr('action'), 
      type  : 'post', 
      data  : form.serialize(),
      beforeSend: function () {
        // 禁用按钮防止重复提交
          $("#btn").attr({ disabled: "disabled" }).text("Loaing....");
          $("#download_url").hide();
      },
      success: function (response){
        if(response.status){
          $("#download_url").show().attr("href",response.url);
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            type: 'success',
            timer:3000
          })
        }
        else{
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            type: 'warning',
            timer:3000
          })
        }
      },
      complete: function () {
        $("#btn").removeAttr("disabled").text("提交");
      },
      error : function (){
        $('#btn').removeAttr("disabled").text("提交");
        swal({
            title: '系统错误',
            text: '3秒后自动关闭',
            type: 'error',
            timer:3000
        })
      } 
    }); 
    return false; 
  }); 

  $(document).ready(function(){
    $('#uuids').tagsinput({
      itemValue: 'id',
      itemText: 'uuid',
      source: function(query) {
        return $.getJSON('<?php echo Url::toRoute('redshift/getbyuuid')?>&uuid='+query);
      }
    });
  });

  $(document).ready(function(){
    $('#networks').tagsinput({
      itemValue: 'cb',
      itemText: 'network',
      source: function(query) {
        return $.getJSON('<?php echo Url::toRoute('redshift/getbynetwork')?>&network='+query);
      }
    });
  });

  $(document).ready(function(){
    $('#advertisers').tagsinput({
      source: function(query) {
        return $.getJSON('<?php echo Url::toRoute('redshift/getbyadvertiser')?>&advertiser='+query);
      }
    });
  });
  
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

  $('input:radio[name="Redshift[source]"]').click(function(){
    if($(this).val()==0)
    {
      $("#conversion_log").show();
      $("#raw_install_log").hide();
      $("#event_log").hide();
    }
    
    if($(this).val()==1)
    {
      $("#raw_install_log").show();
      $("#event_log").hide();
      $("#conversion_log").hide();
    }
    if($(this).val()==2)
    {
      $("#conversion_log").hide();
      $("#event_log").show();
      $("#raw_install_log").hide();
    }

  })

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['typeahead'], \yii\web\View::POS_END); ?>  