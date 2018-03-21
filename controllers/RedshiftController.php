<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Redshift;
use moonland\phpexcel\Excel;
use Yii;
use app\models\Deducted;
use yii\web\UploadedFile;
use johnnylei\csv\TheCsv;
use app\models\Advertiser;

class RedshiftController extends Controller
{   
    //上传文件路径
    const FILE_PATH=__DIR__.'/../web/uploads/';

    protected $count="count(*)";
    protected $contents="trunc( ( TIMESTAMP 'epoch' + (timestamp +(28800)) * INTERVAL '1 Second ' )) AS received_date,
            test1.timestamp,test2.uuid,test3.network,test1.mb_subid,test1.p3,test1.defraud,test1.mb_af_1";
    protected $limit="limit 10";
    //public $layout=false;
    public function actionIndex()
    {	

    	//$model=Redshift::find()->select('timestamp,uuid')->where(['and',['between','timestamp',1514736000,1516550400],['uuid'=>489507],['>','network','0']])->limit(10)->asArray()->all();
    	// $model=Redshift::findBySql(
    	//    'select test1.timestamp,test2.uuid from 
    	// 	mob_install_log test1 
    	// 	left join mob_camp_info test2 
    	// 	on test1.uuid=test2.id where time limit 1')->asArray()->all();
        $model= new Redshift();
        $data='';
        $start_time='';
        $end_time='';
        $uuid='';
        $count='';
        if(Yii::$app->request->isPost)
        {   
            $post= Yii::$app->request->post();
            $uuid=$post['Redshift']['uuid'];
            
            $start_time=strtotime($post['Redshift']['start_time']);
            $end_time=strtotime($post['Redshift']['end_time']);
            
            //$model=Redshift::find()->select('timestamp,uuid')->where(['and',['between','timestamp',1514736000,1516550400],['uuid'=>489507],['>','network','0']])->limit(10)->asArray()->all();
            $count=$this->exportTool($start_time,$end_time,$uuid,$this->count);

            $data=$this->exportTool($start_time,$end_time,$uuid,$this->contents,$this->limit);
            
        }
    	//var_dump($model);
        return $this->render('index',['model'=>$model,'data'=>$data,'start_time'=>$start_time,'end_time'=>$end_time,'uuid'=>$uuid,'count'=>$count]);
    }

    public function exportTool($start_time,$end_time,$uuid,$content,$limit="")
    {
        $sql="select $content
            from mob_install_log test1
             left join mob_camp_info test2 on test1.uuid=test2.id
             left join channel_map test3 on test1.network=test3.cb
             where test1.timestamp>$start_time and test1.timestamp<$end_time and test2.uuid in(:uuid) and test1.network>0 $limit";
            $data=Redshift::findBySql($sql,[':uuid'=>$uuid])->asArray()->all();
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
        $sql="select test2.uuid,test3.network as qudao,test1.mb_subid,test1.defraud,test1.mb_af_1,test1.mb_af_2,test3.manager,test1.$match_column
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
        $advertisers=Advertiser::find()->all();
        foreach ($advertisers as $v) {
            $client[]=$v->adv_name;
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
                
                if(!isset($data))
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
                // var_dump($rows);
                // die();
                $this->exportCsv($rows,$new_column,explode('.', $file_name)[0]);
                exit();
            }
        }
        return $this->render('deducted',['model'=>$model,'advertisers'=>$client]);
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