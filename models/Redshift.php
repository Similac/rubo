<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;
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

 }