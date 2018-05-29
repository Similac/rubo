<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;
use app\models\Campinfo;
use app\models\Channel_map;
use app\common\func;
 class Redshift extends ActiveRecord
 {
 	public $start_time;
 	public $end_time;
 	public $uuid;
 	public $network;
 	public $advertiser;
    public $select;
    public $type;
    public $defraud_tag;
    
    // public $all=[
    //     "fix/index",
    //     "load/index",
    //     "load/list",
    //     "redshift/deducted",
    //     "redshift/index",
    //     "A"
    // ];

 	public static function getDb()
    {
        return Yii::$app->remote_db;
    }

    public static function tableName()
    {

    	return 'mob_install_log';
    }

    public function rules()
    {	
    	return [
    		[['start_time','end_time'],'required'],
	    	['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>'],
            ['type','integer'],
            ['uuid','requiredBytype1','skipOnEmpty' => false, 'skipOnError' => false],
            ['advertiser','requiredBytype2','skipOnEmpty' => false, 'skipOnError' => false],
            ['uuid','checkPermissionByPm','skipOnEmpty' => false, 'skipOnError' => false],
            ['network','checkPermissionByOm','skipOnEmpty' => true, 'skipOnError' => false]
    	];
    	
    }


    public function attributeLabels()
    {
    	return [
    		'start_time'=>'开始时间',
    		'end_time'=>'结束时间',
    		'uuid'=>'uuid',
    		'network'=>'渠道名称',
    		'advertiser'=>'广告主',
            'upload_file'=>'上传文件',
            'timezone'=>'时区',
            'select'=>'添加字段',
            'type'=>'维度类型',
            'defraud_tag'=>'扣量标记'
    	];
    }

    public function requiredBytype1($attribute,$params)
    {
        if ($this->type==0) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "uuid不能为空");
            }
        }
    }

    public function requiredBytype2($attribute,$params)
    {
        if ($this->type==1) {
            if (empty($this->advertiser)) {
                $this->addError($attribute,'广告主不能为空');
            }
        }
    }

    public function checkPermissionByPm()
    {
        if ($this->type==0) {
            
            //拼接uuid
            $uuids='\''.str_replace(',','\',\'',trim($this->uuid)).'\'';
            
            
            //A:pm B:to C:tech
            if(in_array("redshift_data_forPM", func::getPermissions()))
            {
                $username=Yii::$app->session['user']['username'];
                $pms=$this->checkPm($uuids);

                foreach ($pms as $v) {
                    
                    if($v['pm']!==$username)
                    {
                        $this->addError('uuid','您没有权限查看'.$v['uuid']);
                    }
                }
            }
        }
    }

    //检查om的输入渠道
    public function checkPermissionByOm()
    {
        if ($this->type==0)
        {
            if(in_array("redshift_data_forTO", func::getPermissions()))
            {

                if(!empty($this->network)){
                    //拼接network
                    $networks='\''.str_replace(',','\',\'',trim($this->network)).'\'';
                    $username=Yii::$app->session['user']['username'];
                    $oms=$this->checkOm($networks);
                    foreach ($oms as $v) {
                        
                        if($v['manager']!==$username)
                        {
                            $this->addError('network','您没有权限查看'.$v['network']);
                        }
                    }
                }
            }
        }
    }

    //查询manager和network
    public function checkOm($networks)
    {
        $sql="select
            network,manager
        from
            channel_map
        where
            network in ($networks)";

        $oms=Channel_map::findBySql($sql)->asArray()->all();
        return $oms;
    }

    public function checkPm($uuids)
    {
        $sql="select
            uuid,pm
        from
            mob_camp_info
        where
            uuid in ($uuids)";
        $pms=Campinfo::findBySql($sql)->asArray()->all();
        return $pms;
    }

 }