<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;
use app\models\Campaign;
 class Redshift extends ActiveRecord
 {
 	public $start_time;
 	public $end_time;
 	public $uuid;
 	public $network;
 	public $advertiser;
    public $upload_file;

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
    		[['start_time','end_time',],'required'],
            [['upload_file'], 'file', 'skipOnEmpty' => false],
            [['upload_file'], 'file', 'extensions' => 'csv, xlsx,xls'],
	    	['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>'],
            
    	];
    	
    }

    public function attributeLabels()
    {
    	return [
    		'start_time'=>'开始时间',
    		'end_time'=>'结束时间',
    		'uuid'=>'offer uuid',
    		'network'=>'渠道名称',
    		'advertiser'=>'广告主',
            'upload_file'=>'上传文件'
    	];
    }

    public function getCampaigns()
    {
        return $this->hasOne(Campaign::className(),['id'=>'uuid']);
    }
 }