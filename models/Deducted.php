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

    public function rules()
    {	
    	return [
    		[['start_time','end_time','advertiser','clickid_column'],'required'],
	    	['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>'],
	    	[['upload_file'], 'file', 'skipOnEmpty' => false],
            [['upload_file'], 'file', 'extensions' => 'xlsx'],
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

    public function attributeLabels()
    {
    	return [
    		'start_time'=>'开始时间',
    		'end_time'=>'结束时间',
    		'advertiser'=>'广告主',
            'upload_file'=>'上传文件',
            'clickid_column'=>'clickid或devid所在列名',
            'match_type'=>'匹配类型'
    	];
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
}