<?php
namespace app\models;

use yii\base\Model;
use Yii;
class Deducted extends Model
{
	public $start_time;
 	public $end_time;
 	public $advertiser;
    public $upload_file;
    public $clickid_column;
    public $match_type;
    public $uuid;

    public function rules()
    {	
    	return [
    		[['start_time','end_time','clickid_column','upload_file'],'required'],
            [['start_time','end_time'],'date', 'format'=>'yyyy-MM-dd HH:mm'],
	    	['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>'],
	    	[['upload_file'], 'file'],
            [['upload_file'], 'file','extensions' => ['xlsx','xls','csv'], 'checkExtensionByMimeType' => false],
            ['upload_file','checkChinese'],
            ['advertiser','requiredByadv','skipOnEmpty' => false, 'skipOnError' => false],
            ['uuid','requiredByuuid','skipOnEmpty' => false, 'skipOnError' => false]
    	];
    	
    }

    public function checkChinese($attribute,$params)
    {
    	if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $this->upload_file->name))
    	{
    		$this->addError('upload_file','文件名有中文名');
    	}
    }

    public function requiredByadv($attribute,$params)
    {
        if ($this->match_type==0) {
            if (empty($this->$attribute)) {
                $this->addError($attribute,'广告主不能为空');
            }
        } 
    }

    public function requiredByuuid($attribute,$params)
    {
    
        if ($this->match_type==1) {
            if (empty($this->$attribute)){
                $this->addError($attribute, "uuid不能为空");
            }
        }
    }

    public function attributeLabels()
    {
    	return [
    		'start_time'=>'开始时间',
    		'end_time'=>'结束时间',
    		'advertiser'=>'广告主',
            'upload_file'=>'上传文件',
            'clickid_column'=>'clickid或devid所在列名',
            'match_type'=>'匹配类型',
            'uuid'=>'uuid'
    	];
    }

    public function upload()
    {
        if ($this->validate()) {
            $des_url='uploads/' . $this->upload_file->baseName . '.' . $this->upload_file->extension;
            $this->upload_file->saveAs($des_url);
            return true;
        } else {
            return false;
        }
    }
}