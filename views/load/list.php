<?php
use yii\widgets\LinkPager;
use yii\helpers\Url;
use app\assets\AppAsset;
AppAsset::register($this);
?>
<style>
  .dropdown-menu.pull-right {
  right: 0;
  left: auto;
}
</style>
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      
      <div class="box-header with-border">
        <h3 class="box-title">任务列表</h3>
        <div class="box-tools">
          <div class="input-group" style="width: 150px;">
            <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="Search">
            <div class="input-group-btn">
              <button class="btn btn-sm btn-default">搜索</button>
            </div>
          </div>
        </div>
      </div><!-- /.box-header -->
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover" style="border-collapse:separate; border-spacing:0px 20px;">
          <tr>
            <th>ID</th>
            <th>数据源</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>uuid</th>
            <th>渠道</th>
            <th>匹配类型</th>
            <th>clickid数目</th>
            <th>idfa数目</th>
            <th>输出类型</th>
            <th>创建时间</th>
            <th>project_name</th>
            <th>project_id</th>
            <th>execute_id</th>
            <th>下载路径</th>
            <th>操作</th>
          </tr>
          <?php foreach($model as $data):?>
          <tr>
            <td><?php echo $data->id?></td>
            <?php if($data->source==0):?>
              <td><span class="label label-info">install_log</span></td>
            <?php endif;?>
            <?php if($data->source==1):?>
              <td><span class="label label-primary">click_log</span></td>
            <?php endif;?>  
            <?php if($data->source==2):?>
              <td><span class="label label-warning">event_log</span></td>
            <?php endif;?>  
            <?php if($data->source==3):?>
              <td><span class="label label-danger">inject_code</span></td>
            <?php endif;?>
            
            <td><?php echo $data->start_time?></td>
            <td><?php echo $data->end_time?></td>
            <td><?php echo $data->uuid?></td>
            <td><?php echo $data->network?></td>
            
            <?php if($data->match_type==0):?>
              <td><span class="label label-primary">不匹配</span></td>
            <?php endif;?>
            <?php if($data->match_type==1):?>
              <td><span class="label label-primary">匹配clickid</span></td>
            <?php endif;?>
            <?php if($data->match_type==2):?>
              <td><span class="label label-primary">匹配idfa</span></td>
            <?php endif;?>
            
            <td><span class="label label-primary"><?php echo $data->clickid;?></span></td>
            <td><span class="label label-primary"><?php echo $data->idfa;?></span></td>

            <?php if($data->export_type==0):?>
            <td><span class="label label-primary">csv</span></td>
            <?php else:?>
            <td><span class="label label-primary">json</span></td>
            <?php endif;?>
            <td><span class="label label-success"><?php echo date("Y-m-d H:i:s",$data->created_at)?></span></td>
            <td><span class="label label-primary"><?php echo $data->project_name;?></span></td>
            <td><span class="label label-primary"><?php echo $data->project_id;?></span></td>
            <td><span class="label label-primary"><?php echo $data->execute_id;?></span></td>
            <td><?php echo $data->export_path;?></td>
            <td>
              
              <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle">Action<span class="caret"></span>
                </button>
                <ul class="dropdown-menu pull-right">
                    <li><a href='<?php echo Url::to(["load/index","id"=>$data->id])?>' class="font-bold">任务详情</a>
                    </li>
                    <li><a href='<?php echo Url::to(["load/progress",'execute_id'=>$data->execute_id])?>'>查看进度</a>
                    </li>
                </ul>
              </div>

            </td>
          </tr>
          <?php endforeach;?>
          <tr></tr>
          <tr></tr>
        </table>
      </div><!-- /.box-body -->
      <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
          <?php echo LinkPager::widget([
            'pagination' => $pages,
          ]); ?>
        </ul>
      </div>
    </div><!-- /.box -->
  </div>
</div>
<?php
$js=<<<js
$(function(){
  // 默认显示
  //$(".dropdown-toggle").dropdown('toggle');
  var dropdown=$(".dropdown-toggle");
  for(var i=0;i<dropdown.length;i++)
  {
    dropdown[i].dropdown('toggle');
  }
});

js;
$this->registerJs($js);
?>