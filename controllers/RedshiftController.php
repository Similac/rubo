<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Redshift;
use Yii;
use app\models\Deducted;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use app\models\Channel_map;
use app\common\func;
use app\models\Select;
use yii\helpers\Url;
use app\models\Campinfo;
use moonland\phpexcel\Excel;
class RedshiftController extends CommonController
{   
    //上传文件路径
    const FILE_PATH=__DIR__.'/../web/uploads/';
    
    // public $all=[
    //     "fix/index",
    //     "load/index",
    //     "load/list",
    //     "redshift/deducted",
    //     "redshift/index",
    //     "A"
    // ];
    public $table=['mob_install_log','mob_raw_install_log','mob_event_log'];

    //生成csv的路径
    public $csv_path=__DIR__.'/../web/general_csv/';

    //表单ajax验证
    public function actionValidation()
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Redshift();   //这里要替换成自己的模型类
        

        $model->load(Yii::$app->request->post());
        return ActiveForm::validate($model);
    }

    public function exportByuuid($start_time,$end_time,$uuids,$networks,$selects,$source)
    {
        $sql="select $selects
            from $source a
             left join mob_camp_info b on a.uuid=b.id
             left join channel_map c on a.network=c.cb
             where a.timestamp>$start_time and a.timestamp<$end_time and a.uuid in($uuids) and a.network>0 ".$networks;
        $data=Redshift::findBySql($sql)->asArray()->all();
        return $data;
    }

    public function exportByadvertiser($start_time,$end_time,$advertiser,$selects,$source)
    {
        $sql="select $selects
            from $source a
             left join mob_camp_info b on a.uuid=b.id
             left join channel_map c on a.network=c.cb
             where a.timestamp>$start_time and a.timestamp<$end_time and b.adv_name in ($advertiser) and a.network>0";
        $data=Redshift::findBySql($sql)->asArray()->all();
        return $data;
    }

    //根据广告主和clickid去匹配扣量信息
    public function matchTool($start_time,$end_time,$advertiser,$matchData,$match_column)
    {
        $sql="select test2.uuid,test3.network as qudao,test3.alias,test1.mb_subid,'mob'||SUBSTRING(md5(test1.network||'_'||test1.mb_subid),0,17) as no_bt_encodeid,test1.defraud,test1.mb_af_1,test1.mb_af_2,test3.manager,test1.$match_column
            from mob_install_log test1
             left join mob_camp_info test2 on test1.uuid=test2.id
             left join channel_map test3 on test1.network=test3.cb
             where test1.timestamp>$start_time and test1.timestamp<$end_time and test2.adv_name =:advertiser and test1.network>0 and lower(test1.$match_column) in ($matchData)";
            $data=Redshift::findBySql($sql,[':advertiser'=>$advertiser])->asArray()->all();
        //return $sql;
        return $data;
        //$data = Redshift::find()->joinWith('campaigns')->select('mob_camp_info.uuid,mob_install_log.network')->where('mob_camp_info.adv_name=:adv_name',[':adv_name'=>$advertiser])->asArray()->all();
    }

    
    public function actionDeducted()
    {   
        set_time_limit(0);
        $model = new Deducted();
        $sql='select distinct(adv_name) from mob_camp_info';
        $camp=Campinfo::findBySql($sql)->asArray()->all();
        $advs=[];
        foreach ($camp as $key => $v) {
            $advs[]=$v['adv_name'];
        }
        
        if(Yii::$app->request->isPost)
        {   
            $post=Yii::$app->request->post();
            
            $model->upload_file = UploadedFile::getInstance($model,'upload_file');
            //$model->upload_file->name = iconv("UTF-8","gb2312",$model->upload_file->name);//这里是处理中文的问题，非中文不需要
            $post['Deducted']['upload_file']=$model->upload_file;

            if($model->load($post) && $model->upload())
            {   
                $file_name=$model->upload_file->name;
                $excel = new Excel();
                $rows=$excel->readFile(self::FILE_PATH.$file_name);
                $matchData=array();
                $start_time=strtotime($post['Deducted']['start_time']);
                $end_time=strtotime($post['Deducted']['end_time']);
                $advertiser=$post['Deducted']['advertiser'];
                $column_name=$post['Deducted']['clickid_column'];
                // var_dump($rows);
                // die();
                foreach ($rows as $k=>$row) 
                {   
                    
                    if(is_null($row[$column_name]))
                    {   
                        continue;
                    }
                    
                    $matchData[] = "'".$row[$column_name]."'";
                    //$clickids.="'".$row[$column_name]."'".(is_null($row[$column_name])?'':',');
                }

                $matchData = implode(',', array_filter($matchData));
                
                switch ($post['Deducted']['match_type']) {
                    case '0':
                       $redshift_column='p3';
                        break;
                    case '1':
                       $redshift_column='devid';
                        break;
                    default:
                        $redshift_column='p3';
                        break;
                }
                
                $data=$this->matchTool($start_time,$end_time,$advertiser,strtolower($matchData),$redshift_column);
                
                if(empty($data))
                {
                    return 'redshift没有找到对应的数据';
                }

                $p3['clickid']=explode(",", $matchData);
                
                $tmp = [];
                
                foreach ($data as $val) {
                    $tmp[strtolower($val[$redshift_column])] = $val;
                }
               
                foreach ($rows as &$item) {
                    $match_data= strtolower($item[$column_name]);
                    
                    if (isset($tmp[$match_data])) {
                        $item = array_merge($item, $tmp[$match_data]);
                    } else {
                        continue;
                    }

                }
                
                func::exportCsv($rows,explode('.', $file_name)[0]);
                exit();
            }
        }
        return $this->render('deducted',['model'=>$model,'advertisers'=>$advs]);
    }

    public function actionIndex()
    {   
        set_time_limit(0);

        $all_selects=$this->getSelects("select id,content,source,name from shop_selects");
        
        foreach ($all_selects as $v) {
            if($v['source']==0)
            {
               $select_for_install[]=$v; 
            }elseif ($v['source']==1) {
                $select_for_raw_install[]=$v;
            }else
            {
                $select_for_event[]=$v;
            }
        }

        $model = new Redshift();
        
        if(Yii::$app->request->isPost)
        {   
            
            if(Yii::$app->request->isAjax)
            {
                Yii::$app->response->format=Response::FORMAT_JSON;
            }

            $post=Yii::$app->request->post();
            
            if($model->load($post) && $model->validate())
            {
                $start_time=strtotime($post['Redshift']['start_time']);
                $end_time=strtotime($post['Redshift']['end_time']);
                switch ($post['Redshift']['type']) {
                    case '0':
                        $post['Redshift']['advertiser']='';
                        //拼接uuid
                        $uuids='\''.str_replace(',','\',\'',$post['Redshift']['uuid']).'\'';
                        
                        $role=func::getRole();
                        
                        if($role['role']=='to')
                        {
                            //判断network是否为空
                            if ($post['Redshift']['network']) 
                            {
                                $networks='and a.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $manager=Yii::$app->session['user']['username'];
                                $sql='select network from channel_map where manager=:manager';
                                $channel=Channel_map::findBySql($sql,[':manager'=>$manager])->asArray()->all();
                                
                                $cbs='';
                                $str=',';
                                foreach ($channel as $k=>$v) {
                                    if(count($channel)-$k==1)
                                    {
                                        $str='';
                                    }
                                    $cbs.='\''.$v['network'].'\''.$str;
                                }
                                $networks="and a.network in ($cbs)";
                                
                            }
                        }
                        else{
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and a.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $networks='';
                            }
                        }
                        
                        //选择install log
                        if($post['Redshift']['source']==0)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['install_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[0]);
                            if($data)
                            {

                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                            
                        }
                        //选择mob_raw_install_log
                        if($post['Redshift']['source']==1)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['raw_install_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[1]);
                            if($data){

                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        //选择mob_raw_install_log
                        if($post['Redshift']['source']==2)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['event_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[2]);
                            if($data){
                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        break;
                    case '1':
                        
                        $advertiser='\''.$post['Redshift']['advertiser'].'\'';
                        
                        if($post['Redshift']['source']==0)
                        {
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['install_select']);
                            $data=$this->exportByadvertiser($start_time,$end_time,$advertiser,$select_option,$this->table[0]);
                            if($data){
                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        if($post['Redshift']['source']==1)
                        {
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['raw_install_select']);
                            $data=$this->exportByadvertiser($start_time,$end_time,$advertiser,$select_option,$this->table[1]);
                            if($data){
                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        if($post['Redshift']['source']==2)
                        {
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['event_select']);
                            $data=$this->exportByadvertiser($start_time,$end_time,$advertiser,$select_option,$this->table[2]);
                            if($data){
                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        break;
                    default:
                        $post['Redshift']['advertiser']='';
                        //拼接uuid
                        $uuids='\''.str_replace(',','\',\'',$post['Redshift']['uuid']).'\'';
                        
                        $role=func::getRole();
                        
                        if($role['role']=='to')
                        {
                            //判断network是否为空
                            if ($post['Redshift']['network']) 
                            {
                                $networks='and a.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $manager=Yii::$app->session['user']['username'];
                                $sql='select network from channel_map where manager=:manager';
                                $channel=Channel_map::findBySql($sql,[':manager'=>$manager])->asArray()->all();
                                
                                $cbs='';
                                $str=',';
                                foreach ($channel as $k=>$v) {
                                    if(count($channel)-$k==1)
                                    {
                                        $str='';
                                    }
                                    $cbs.='\''.$v['network'].'\''.$str;
                                }
                                $networks="and a.network in ($cbs)";
                                
                            }
                        }
                        else{
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and a.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $networks='';
                            }
                        }
                        
                        //选择install log
                        if($post['Redshift']['source']==0)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['install_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[0]);
                            if($data)
                            {

                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                            
                        }
                        //选择mob_raw_install_log
                        if($post['Redshift']['source']==1)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['raw_install_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[1]);
                            if($data){

                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }

                        //选择mob_raw_install_log
                        if($post['Redshift']['source']==2)
                        {   
                            $select_option=$this->getByselect($all_selects,$post['Redshift']['event_select']);
                            $data=$this->exportByuuid($start_time,$end_time,$uuids,$networks,$select_option,$this->table[2]);
                            if($data){
                                $header=array_keys($data[0]);
                                $file_name=time().'.csv';
                                $csv_path=$this->csv_path.$file_name;
                                if($this->genCsv($header,$data,$csv_path))
                                {   
                                    $url=Url::to("@web/general_csv/$file_name", true);
                                    return ['status'=>1,'msg'=>'导出成功,下方是下载链接','url'=>$url];
                                }

                            }else
                            {   
                                return ['status'=>0,'msg'=>'数据为空,请检查输入条件'];
                            }
                        }
                        break;
                }

            }
            
        }
        return $this->render('index',['model'=>$model,'install_selects'=>$select_for_install,'select_for_raw_install'=>$select_for_raw_install,'select_for_event'=>$select_for_event]);
    }

    public function getByselect($all_selects,$source_select)
    {
        //拼接select字符串
        foreach ($all_selects as $v) {
            foreach ($source_select as $option) {
                if ($option==$v['id']) {
                    $name[]=$v['name'];
                }
            }
        }
        $select_option=implode(",", $name);
        return $select_option;
    }

    public function getSelects($sql)
    {
        return $selects=Select::findBySql($sql)->asArray()->all();
    }

    public function actionGetbyuuid()
    {

        if (Yii::$app->request->isGet) {
            if (!empty(Yii::$app->request->get('uuid'))) {
                
                $uuid=Yii::$app->request->get('uuid');
            }
            else
            {
                return $uuid='';
            }
        }else
        {
            return '';
        }

        
        $result=func::getByuuid($uuid);
        echo json_encode($result);
    }

    public function actionGetbyadvertiser()
    {

        if (Yii::$app->request->isGet) {
            if (!empty(Yii::$app->request->get('advertiser'))) {
                
                $advertiser=Yii::$app->request->get('advertiser');
            }
            else
            {
                return $advertiser='';
            }
        }else
        {
            return '';
        }
        
        $advs=[];
        $result=func::getByadvertiser($advertiser);
        foreach ($result as $v) {
            $advs[]=$v['adv_name'];
        }
        $advs_str=implode('","', $advs);
        echo '["'.$advs_str.'"]';
    }

    public function actionGetbynetwork()
    {
        if (Yii::$app->request->isGet) {
            if (!empty(Yii::$app->request->get('network'))) {
                
                $network=Yii::$app->request->get('network');
            }
            else
            {
                return $network='';
            }
        }else
        {
            return '';
        }

        $result=func::getBychannel($network);
        echo json_encode($result);
    }

    public function genCsv($header,$contents,$path)
    {
        if(!file_exists($path))
        {
            mkdir($path, 0777, true);
        }
        $handle = fopen( $path, 'wb' );
        if ($handle) {
            foreach ($header as $k=>$v) {
               $header[$k]=iconv( 'UTF-8', 'GB2312//IGNORE', $v );
            }
            
            fputcsv( $handle, $header ); //写入header
            
            foreach ($contents as $k=>$v) {

                foreach ($v as $value) {
                    $new[$k][]=iconv( 'UTF-8', 'GB2312//IGNORE', $value);
                }
            fputcsv( $handle, $new[$k] );
            }
            if(fclose($handle))
            {
                return true;
            }

        }    
    }
}