<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;
class Fix extends ActiveRecord
{
    
    public $upload_file;
    public $uuid_column;
    public $clickid_column;
    public $operator;

    public static function getDb()
    {
        return Yii::$app->remote_db;
    }
    
    public function attributeLabels()
    {
    	return [
            'upload_file'=>'上传文件',
            'uuid_column'=>'UUID列名',
            'clickid_column'=>'Click ID列名',
            'operator'=>'你的英文名字',
    	];
    }
    
    public function rules()
    {
    	return [
            [['uuid_column','clickid_column','operator'],'required'],
            [['upload_file'], 'file', 'skipOnEmpty' => false],
            [['upload_file'], 'file', 'extensions' => 'csv, xlsx,xls'],
            ['upload_file','checkChinese'],
    	];
    }
    
    
    public function checkChinese($attribute,$params)
    {
    	if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $this->upload_file->name))
    	{
    		$this->addError('upload_file','文件名有中文名');
    	}
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->upload_file->saveAs('uploads/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
            return true;
        } else {
            return false;
        }
    }
    
    public function unlink_file() {
        return @unlink('uploads/' . $this->upload_file->baseName . '.' . $this->upload_file->extension);
    }
    
}