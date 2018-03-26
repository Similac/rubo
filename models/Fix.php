<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
use app\models\Campaign;
class Fix extends ActiveRecord
{
    
    public $upload_file;
    
    public static function getDb()
    {
        return Yii::$app->remote_db;
    }
    
    public function attributeLabels()
    {
    	return [
            'upload_file'=>'上传文件',
    	];
    }
    
    
    
}