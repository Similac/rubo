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
        <h3 class="box-title">下载列表</h3>
      </div><!-- /.box-header -->
      <div class="box-body table-responsive no-padding">
        <table class="table table-hover" style="border-collapse:separate; border-spacing:0px 20px;">
          <tr>
            <th>Date</th>
            <th>Size</th>
            <th>File</th>
            <th>Action</th>
          </tr>
            <?php
                foreach ($files as $file){
                   echo "<tr>
                            <th>$file->date</th>
                            <th>$file->size</th>
                            <th>$file->key</th>
                            <th><a href='$file->url'>down</a></th>
                        </tr>";
                }
            ?>
        </table>
      </div><!-- /.box-body -->
      <div class="box-footer clearfix">
        <ul class="pagination pagination-sm no-margin pull-right">
        </ul>
      </div>
    </div><!-- /.box -->
  </div>
</div>