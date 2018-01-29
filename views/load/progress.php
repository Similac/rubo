<?php
use app\assets\AppAsset;
use yii\helpers\Url;
AppAsset::register($this);
?>
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      
      <div class="box-header with-border">
        <h2 class="box-title">进度查询</h2>
        <button class="btn btn-sm btn-danger pull-right" id="kill">Kill</button>
      </div><!-- /.box-header -->
      
      <div class="col-xs-12 col-content">
          <div class="log-viewer">
            <div class="panel panel-default">
              <div class="panel-heading">
                <div class="pull-right">
                  <a class="btn btn-xs btn-default" href="<?php echo Url::to(['load/progress','execute_id'=>$execute_id],true)?>">Refresh</a>
                </div>
                Job Logs
              </div>
              <div class="panel-body" >
                <?php foreach ($model as $data): ?>
                <pre style="height:450px"><?php echo $data;?></pre>
              <?php endforeach;?>
              </div>
            </div>
          </div>
        </div>
    </div><!-- /.box -->
  </div>
</div>
<?php
$url = Url::toRoute(["load/cancelflow"],true);
$js=<<<js
$("#kill").click(function(){
  $.ajax({
      url : "$url", 
      type  : 'get',
      data  : {execute_id:$execute_id},
      success: function (response){
        if(response.status)
        {
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            
            timer:3000,
            imageUrl: response.img
          })
        }
        else
        {
          swal({
            title: response.msg,
            text: '3秒后自动关闭',
            
            timer:3000,
            imageUrl: response.img
          })
        }
      
      },
      error : function (){ 
        swal({
            title: '系统错误',
            text: '3秒后自动关闭',
           
            timer:3000,
            imageUrl: response.img
        }) 
      } 
      
    });
})
js;
$this->registerJs($js);
?>