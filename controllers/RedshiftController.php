<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Redshift;
use moonland\phpexcel\Excel;
use Yii;
class RedshiftController extends Controller
{   
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
}