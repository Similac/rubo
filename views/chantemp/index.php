<?php
use app\assets\AppAsset;
use yii\web\View;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
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
          'validationUrl' => Url::to(['chantemp/validation']),
          'id'=>'chantempForm',
           
        ]);?> 

        <div class="box-body">  
          <div style="height:100px">    
            <div class="form-group col-sm-4">
              <?= $form->field($model,'uuid')->textInput()?>     
            </div>
              
            <div class="form-group col-sm-4">
              <?= $form->field($model,'platform')->dropdownList($platform)?>     
            </div>
              
            <div class="form-group col-sm-4">
              <?= $form->field($model,'channel_id')->dropdownList($channel_id,['prompt'=>'请选择'])?>
            </div>
          
          </div>
          <hr>
          <!--百度_subid-->
          <div id="baidu" style="display:none">
            <div class="form-group col-sm-4" >
              <?= $form->field($model,'akey')->textInput()?>     
            </div>
            <div class="form-group col-sm-4">
              <?= $form->field($model,'baidu_subid')->dropdownList($baidu_subid,['prompt'=>'请选择'])?>
            </div>
          </div>
          <!--广点通-->
          <div id="gdt" style="display:none">
            <div class="form-group col-sm-4">
              <?= $form->field($model,'account_id')->textInput()?>     
            </div>

            <div class="form-group col-sm-4">
              <?= $form->field($model,'user_action_set_id')->textInput()?>     
            </div>

            <div class="form-group col-sm-4">
              <?= $form->field($model,'refresh_token')->textInput()?>     
            </div>
            <div>
              <?php for($i=1;$i<=$model->gdt_event_nums;$i++):?>
              <div class="form-group col-sm-6">
                <?= $form->field($model,"gdt_event_name[]")->textInput()?>     
              </div>
              
              <div class="form-group col-sm-6">
                <?= $form->field($model,"gdt_action_type[]")->dropdownList($model->gdt_action_type,["prompt"=>"请选择"])?>
              </div>
              <?php endfor;?>
            </div>
          </div>
          
          <!--头条-->
          <div id="tt" style="display:none">

            <div class="form-group form-inline">
              <?= $form->field($model,'tt_subid')->dropdownList($tt_subid,['prompt'=>'请选择'])?>
            </div>
            <?php for($i=1;$i<=$model->tt_event_nums;$i++):?>
            <div class="form-group col-sm-6">
              <?= $form->field($model,'tt_event_name[]')->textInput()?>     
            </div>

            <div class="form-group col-sm-6">
              <?= $form->field($model,'tt_event_type[]')->dropdownList($model->tt_event_type,['prompt'=>'请选择'])?>     
            </div>
            <?php endfor;?>
          </div>
          
          <!--uc-->
          <div class="form-group col-sm-4" id='uc' style="display:none">
            <?= $form->field($model,'uc_subid')->dropdownList($uc_subid,['prompt'=>'请选择'],[])?>
          </div>
          <!--weibo-->
          <div id="weibo" style="display:none">
            <div class="form-group form-inline">
              <?= $form->field($model,'weibo_subid')->dropdownList($weibo_subid,['prompt'=>'请选择'])?>
            </div>
            <?php for($i=1;$i<=$model->weibo_event_nums;$i++):?>
              <div class="form-group col-sm-6">
                <?= $form->field($model,'weibo_event_name[]')->textInput()?>     
              </div>
              
              <div class="form-group col-sm-6">
                <?= $form->field($model,'weibo_action_type[]')->dropdownList($model->weibo_action_type,['prompt'=>'请选择'])?>     
              </div>
            <?php endfor;?>
          </div>

          <div class="box-footer col-sm-12">
            <?= Html::submitButton('提交',[
                'class'=>'btn btn-primary center-block',
                'id'=>'btn'
            ])?>
          </div>
        </div>

        <?php ActiveForm::end();?>
      </div>
      <div class="panel panel-default" id="url">
        
      </div>
    </div>
  </div>
</section> 
<?php
$js = <<<js
  $(document).ready(function(){
    $('#chantemp-channel_id').change(function(){
      
      if($("#chantemp-channel_id  option:selected").val()=='13557')
      {
        $("#uc").show();
        $("#baidu").hide();
        $("#tt").hide();
        $("#gdt").hide();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13948')
      {
        $("#uc").hide();
        $("#baidu").show();
        $("#tt").hide();
        $("#gdt").hide();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13388')
      {
        $("#uc").hide();
        $("#baidu").show();
        $("#tt").hide();
        $("#gdt").hide();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13353')
      {
        $("#uc").hide();
        $("#baidu").hide();
        $("#tt").hide();
        $("#gdt").show();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13951')
      {
        $("#uc").hide();
        $("#baidu").hide();
        $("#tt").show();
        $("#gdt").hide();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13390')
      {
        $("#uc").hide();
        $("#baidu").hide();
        $("#tt").show();
        $("#gdt").hide();
        $("#weibo").hide();
      }
      if($("#chantemp-channel_id  option:selected").val()=='13512')
      {
        $("#uc").hide();
        $("#baidu").hide();
        $("#tt").hide();
        $("#gdt").hide();
        $("#weibo").show();
      }
    });
    
    $(document).on('beforeSubmit', 'form#chantempForm', function () {
        var form = $(this);
        var title=['投放链接','install_callback','event_callback'];
        $.ajax({
          url  : form.attr('action'), 
          type  : 'post', 
          data  : form.serialize(),
          beforeSend: function(){
             $("#url").empty();
          },
          success: function (data){
            var link='';
            for(var i=0;i<data.status.length;i++)
            {
              link+=title[i]+"<br>"+data.status[i]+"<br><br>";
            }
            $("#url").append(link);
          },
          error : function (){
            alert('cc')
          } 
        }); 
        return false; 
      }).on('submit', function (e) {
          e.preventDefault();
    });
  });
js;
 $this->registerJs($js);
?>