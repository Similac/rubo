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
    public $type;
    public $defraud_tag;
    public $source;
    public $install_select;
    public $raw_install_select;
    public $event_select;
    public $click_select;
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
            [['start_time','end_time'],'date', 'format'=>'yyyy-MM-dd HH:mm'],
	    	['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>'],
            ['type','integer'],
            ['source','integer'],
            ['uuid','requiredBytype1','skipOnEmpty' => false, 'skipOnError' => false],
            ['uuid','uuidLimit','skipOnEmpty' => false, 'skipOnError' => false],
            ['uuid','checkPermissionByPm','skipOnEmpty' => false, 'skipOnError' => false],
            ['advertiser','requiredBytype2','skipOnEmpty' => false, 'skipOnError' => false],
            ['advertiser','advertiserLimit','skipOnEmpty' => false, 'skipOnError' => false],
            ['network','checkPermissionByOm','skipOnEmpty' => true, 'skipOnError' => false],
            ['install_select','requiredByinstall','skipOnEmpty' => false, 'skipOnError' => false],
            ['raw_install_select','requiredByrawinstall','skipOnEmpty' => false, 'skipOnError' => false],
            ['event_select','requiredByevent','skipOnEmpty' => false, 'skipOnError' => false],
            ['click_select','requiredByclick','skipOnEmpty' => false, 'skipOnError' => false],
            ['type','noselectClient','skipOnEmpty' => false, 'skipOnError' => false],
            ['end_time','timeLimit','skipOnEmpty' => false, 'skipOnError' => false]
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
            'install_select'=>'install log导出字段',
            'raw_install_select'=>'raw_install_log导出字段',
            'event_select'=>'event log导出字段',
            'type'=>'维度类型',
            'defraud_tag'=>'扣量标记',
            'source'=>'数据源',
            'click_select'=>'click_log导出字段'
    	];
    }

    public function timeLimit($attribute,$params)
    {
        if($this->source==3)
        {
            
            if((time()-strtotime($this->end_time)>=4*24*60*60)||(time()-strtotime($this->start_time))>=4*24*60*60)
            {
                $this->addError('end_time','开始和结束时间必须在最近四天内');
            }
        }
    }

    public function noselectClient($attribute,$params)
    {
        if($this->source==3)
        {
            if($this->type==1)
            {
                $this->addError('type','只能导uuid维度');
            }
        }
    }

    public function requiredByinstall($attribute,$params)
    {
       if ($this->source==0) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "install导出字段不能为空");
            }
        }
    }

    public function requiredByrawinstall($attribute,$params)
    {
       if ($this->source==1) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "rawinstall导出字段不能为空");
            }
        }
    }

    public function requiredByevent($attribute,$params)
    {
       if ($this->source==2) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "event导出字段不能为空");
            }
        }
    }

    public function requiredByclick($attribute,$params)
    {
        if ($this->source==3) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "click导出字段不能为空");
            }
        }
    }    

    public function requiredBytype1($attribute,$params)
    {
        if ($this->type==0) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "uuid不能为空");
            }
        }
    }

    public function uuidLimit($attribute,$params)
    {
        
        $uuid_arr=explode(",", $this->uuid);
        if($this->source==3)
        {
            if(count($uuid_arr)>1)
            {
                $this->addError($attribute, "uuid最多只能查询1个");
            }
        }
        else
        {
            if(count($uuid_arr)>=3)
            {
                $this->addError($attribute, "uuid最多只能查询2个");
            }
        }


    }

    public function advertiserLimit($attribute,$params)
    {
        $advertiser_arr=explode(",", $this->advertiser);
        if(count($advertiser_arr)>1)
        {
            $this->addError($attribute, "advertiser最多只能查询1个");
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
            
            //A:pm B:to C:tech
            if(in_array("redshift_data_forPM", func::getPermissions()))
            {
                if(!empty($this->uuid))
                {
                    //拼接uuid
                    $uuids='\''.str_replace(',','\',\'',trim($this->uuid)).'\'';
                    $username=Yii::$app->session['user']['username'];
                    $pms=$this->checkPm($uuids);

                    foreach ($pms as $v) {
                        
                        if(strtolower($v['pm'])!==strtolower($username))
                        {
                            $this->addError('uuid','您没有权限查看'.$v['uuid']);
                        }
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
                    $cbs='\''.str_replace(',','\',\'',trim($this->network)).'\'';
                    $username=Yii::$app->session['user']['username'];
                    $oms=$this->checkOm($cbs);
                    foreach ($oms as $v) {
                        
                        if(strtolower($v['manager'])!==strtolower($username))
                        {
                            $this->addError('network','您没有权限查看'.$v['network']);
                        }
                    }
                }
            }
        }
    }

    //查询manager和network
    public function checkOm($cbs)
    {
        $sql="select
            cb,network,manager
        from
            channel_map
        where
            cb in ($cbs)";

        $oms=Channel_map::findBySql($sql)->asArray()->all();
        return $oms;
    }

    public function checkPm($ids)
    {
        $sql="select
            id,uuid,pm
        from
            mob_camp_info
        where
            id in ($ids)";
        $pms=Campinfo::findBySql($sql)->asArray()->all();
        return $pms;
    }

 }