<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Redshift;
use moonland\phpexcel\Excel;
use Yii;
use app\models\Deducted;
use yii\web\UploadedFile;
use johnnylei\csv\TheCsv;
use app\models\Campinfo;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use app\models\Channel_map;
use app\common\func;

class RedshiftController extends CommonController
{   
    //上传文件路径
    const FILE_PATH=__DIR__.'/../web/uploads/';
    
    protected $content="trunc( ( TIMESTAMP 'epoch' + (timestamp +(28800)) * INTERVAL '1 Second ' )) AS received_date,
            test1.timestamp as received_timstamp,test2.uuid,test3.network,test1.mb_char_5,test1.status as is_reject,test1.reject_reason,test1.mb_subid,test1.p3,test1.devid,test1.defraud,test1.mb_af_1,test1.match_timestamp";

    public $select=[
        '(test1.p1-STRTOL (SUBSTRING(p3, 0, 9), 16)) as cti',
        'test3.manager as manager',
        'STRTOL (SUBSTRING(p3, 0, 9), 16) as click_timestamp',
        'test1.p1 as install_timestamp',
        'test1.ip as click_ip',
        "(TIMESTAMP 'epoch' + (STRTOL (SUBSTRING(p3, 0, 9), 16) +(28800)) * INTERVAL '1 Second ') as click_date",
        "(TIMESTAMP 'epoch' + (test1.p1 +(28800)) * INTERVAL '1 Second ') as install_date",
        'mb_int_2 as impression_tag',
        'mb_int_5 as is_bt',
        'mb_char_1 as match_type',
    ];

    
    // public $all=[
    //     "fix/index",
    //     "load/index",
    //     "load/list",
    //     "redshift/deducted",
    //     "redshift/index",
    //     "A"
    // ];

    //public $layout=false;
    //表单ajax验证
    public function actionValidation()
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Redshift();   //这里要替换成自己的模型类
        

        $model->load(Yii::$app->request->post());
        return ActiveForm::validate($model);
    }

    public function actionIndex()
    {   

        $model= new Redshift();
        $sql='select distinct(adv_name) from mob_camp_info';
        $camp=Campinfo::findBySql($sql)->asArray()->all();
        $advs=[];
        foreach ($camp as $key => $v) {
            $advs[]=$v['adv_name'];
        }
        
        if(Yii::$app->request->isPost)
        {
            $post= Yii::$app->request->post();
            $start_time=strtotime($post['Redshift']['start_time']);
            $end_time=strtotime($post['Redshift']['end_time']);
            if($model->load($post) && $model->validate())
            {   

                //Yii::$app->response->format=Response::FORMAT_JSON;
                switch ($post['Redshift']['type']) {
                    case '0':
                        $post['Redshift']['advertiser']='';
                        //拼接uuid
                        $uuids='\''.str_replace(',','\',\'',$post['Redshift']['uuid']).'\'';
                        
                        $role=func::getRole();
                        
                        if($role['role']=='to')
                        {
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and test3.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
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
                                $networks="and test3.network in ($cbs)";
                                
                            }
                        }
                        else{
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and test3.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $networks='';
                            }
                        }
                        
                        
                        //判断select是都为空
                        if ($post['Redshift']['select']) {
                            $select_con='';
                            foreach ($post['Redshift']['select'] as $v) {
                                $select_con.=','.$this->select[$v];

                            }
                            $content=$this->content.$select_con;
                        }
                        else
                        {
                            $content=$this->content;
                        }
                        
                        $data=$this->exportbyuuidTool($start_time,$end_time,$uuids,$networks,$content);
                        

                        if(empty($data))
                        {
                            return '数据为空';
                        }
                        $keys=array_keys($data[0]);
                        $column_name=array_combine($keys, $keys);
                        $file_name=time();
                        $this->exportCsv($data,$column_name,$file_name);
                        exit();
                        break;
                    case '1':
                        $post['Redshift']['uuid']='';
                        $post['Redshift']['network']='';
                        $advertiser='\''.$post['Redshift']['advertiser'].'\'';
                        //判断select是都为空
                        if ($post['Redshift']['select']) {
                            $select_con='';
                            foreach ($post['Redshift']['select'] as $v) {
                                $select_con.=','.$this->select[$v];
                            }
                            $content=$this->content.$select_con;
                        }
                        else
                        {
                            $content=$this->content;
                        }

                        $data=$this->exportbyadvertiserTool($start_time,$end_time,$advertiser,$content);
                        if(empty($data))
                        {
                            return '数据为空';
                        }
                        $keys=array_keys($data[0]);
                        $column_name=array_combine($keys, $keys);
                        $file_name=time();
                        $this->exportCsv($data,$column_name,$file_name);
                        exit();
                        break;
                    default:
                        $post['Redshift']['advertiser']='';
                        //拼接uuid
                        $uuids='\''.str_replace(',','\',\'',$post['Redshift']['uuid']).'\'';
                        $role=func::getRole();

                        if($role['role']=='to')
                        {
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and test3.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
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
                                $networks="and test3.network in ($cbs)";
                                
                            }
                        }
                        else{
                            //判断network是否为空
                            if ($post['Redshift']['network']) {
                                $networks='and test3.network in('.'\''.str_replace(',','\',\'',$post['Redshift']['network']).'\''.')';
                            }
                            else
                            {
                                $networks='';
                            }
                        }
                        
                        
                        //判断select是都为空
                        if ($post['Redshift']['select']) {
                            $select_con='';
                            foreach ($post['Redshift']['select'] as $v) {
                                $select_con.=','.$this->select[$v];

                            }
                            $content=$this->content.$select_con;
                        }
                        else
                        {
                            $content=$this->content;
                        }
                        
                        
                        $data=$this->exportbyuuidTool($start_time,$end_time,$uuids,$networks,$content);
                        
                        if(empty($data))
                        {
                            return '数据为空';
                        }
                        $keys=array_keys($data[0]);
                        $column_name=array_combine($keys, $keys);
                        $file_name=time();
                        $this->exportCsv($data,$column_name,$file_name);
                        exit();
                        break;
                }
            }
        }

        return $this->render('index',['model'=>$model,'advertisers'=>$advs]);
    }

    public function exportbyuuidTool($start_time,$end_time,$uuids,$networks,$content,$limit="")
    {
        $sql="select $content
            from mob_raw_install_log test1
             left join mob_camp_info test2 on test1.uuid=test2.id
             left join channel_map test3 on test1.network=test3.cb
             where test1.timestamp>$start_time and test1.timestamp<$end_time and test2.uuid in($uuids)".$networks;
        $data=Redshift::findBySql($sql)->asArray()->all();
        return $data;
    }

    public function exportbyadvertiserTool($start_time,$end_time,$advertiser,$content,$limit="")
    {
        $sql="select $content
            from mob_raw_install_log test1
             left join mob_camp_info test2 on test1.uuid=test2.id
             left join channel_map test3 on test1.network=test3.cb
             where test1.timestamp>$start_time and test1.timestamp<$end_time and test2.adv_name in ($advertiser) ";
        $data=Redshift::findBySql($sql)->asArray()->all();
        return $data;
    }

    public function actionExport()
    {
        $start_time= Yii::$app->request->get('start_time');
        $end_time= Yii::$app->request->get('end_time');
        $uuid= Yii::$app->request->get('uuid');
        $result=$this->exportTool($start_time,$end_time,$uuid,$this->contents);
        if(!$result)
        {
            return '出错了';
        }


        Excel::export([
            'models'=>$result,
            'fileName'=>date('Ymd').'_'.'export',
            'columns'=>[
                'received_date','uuid','network','mb_subid','p3','defraud','mb_af_1'
            ],
            'headers'=>[
                'received_date'=>'时间',
                'uuid'=>'uuid',
                'network'=>'渠道',
                'mb_subid'=>'subid',
                'p3'=>'clickid',
                'defraud'=>'扣量标记',
                'mb_af_1'=>'渠道clickid'
            ],
        ]);
    }

    
    //根据广告主和clickid去匹配扣量信息
    public function matchTool($start_time,$end_time,$advertiser,$matchData,$match_column)
    {
        $sql="select test2.uuid,test3.network as qudao,test1.mb_subid,'mob'||SUBSTRING(md5(test1.network||'_'||test1.mb_subid),0,17) as no_bt_encodeid,test1.defraud,test1.mb_af_1,test1.mb_af_2,test3.manager,test1.$match_column
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
                
                $column=array_keys($rows[0]);
                $new_column=array_combine($column, $column);
                
                $this->exportCsv($rows,$new_column,explode('.', $file_name)[0]);
                exit();
            }
        }
        return $this->render('deducted',['model'=>$model,'advertisers'=>$advs]);
    }

    public function exportExcel($result,$column_name)
    {
        Excel::export([
            'models'=>$result,
            'fileName'=>date('Ymd').'_'.'export',
            'columns'=>$column_name,
            // 'headers'=>[
            //     'received_date'=>'时间',
            //     'uuid'=>'uuid',
            //     'network'=>'渠道',
            //     'mb_subid'=>'subid',
            //     'p3'=>'clickid',
            //     'defraud'=>'扣量标记',
            //     'mb_af_1'=>'渠道clickid'
            // ],
            'headers' => array_combine($column_name, $column_name)
        ]);
    }
    
    public function exportCsv($result,$column_name,$file_name)
    {
        $csv = new TheCsv([
            'header'=>$column_name,
            'fileName'=>$file_name.'_export.csv',
        ]);
        $csv->putRows($result);
    }

}