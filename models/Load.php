<?php
namespace app\models;

use yii\db\ActiveRecord;


class Load extends ActiveRecord
{


	public static function tableName()
	{
		return "{{%load}}";
	}

	public function rules()
	{
		return [

			[['source','start_time','end_time','export_type'],'required'],
			['end_time', 'compare', 'compareAttribute'=>'start_time', 'operator' => '>='],
			//[['uuid','clickid'],'match','pattern'=>'/\,/i','message'=>'uuid必须以,隔开'],
			//[['network'],'match','pattern'=>'/\,/i','message'=>'渠道必须以,隔开'],
			//[['uuid','network'],'validateEmpty','skipOnEmpty' => false, 'skipOnError' => false],
			['clickid','requiredByMatch_type1','skipOnEmpty' => false, 'skipOnError' => false],
			['idfa','requiredByMatch_type2','skipOnEmpty' => false, 'skipOnError' => false],
			
			[['uuid','network','match_type','clickid','idfa','project_name','created_at','project_id','execute_id','export_path'],'safe'],

		];
	}

	public function validateEmpty($attribute,$params)
	{
		if(empty($this->uuid) && empty($this->network))
		{
			$this->addError($attribute,"uuid和network必须输入其中一个");
		}
	}

	//验证match_type==1,clickid不能为空,且不为空长度不能超过3
	public function requiredByMatch_type1($attribute,$params)
	{
		if($this->match_type==1)
		{
			if (empty($this->$attribute)){
				$this->addError($attribute, "clickid不能为空");
			}
			else
			{
				$params=explode("\r\n", trim($this->$attribute));
				if(count($params)>100000)
				{
					$this->addError($attribute, $attribute."不能超过100000个");
					
				}
			}

		}
	}

	//如果match_type==2,idfa不能为空,且不为空长度不能超过3
	public function requiredByMatch_type2($attribute,$params)
	{	
		if($this->match_type==2)
		{
			if (empty($this->$attribute)){
				$this->addError($attribute, "idfa不能为空");
			}
			else
			{
				$params=explode("\r\n", trim($this->$attribute));
				if(count($params)>3)
				{
					$this->addError($attribute, $attribute."不能超过3个");
					
				}
			}  
		}
	}

	public function validateLength($attribute,$params)
	{
		if (!$this->hasErrors())
		{	
			if(!empty($this->$attribute))
			{
				$params=explode("\r\n", trim($this->$attribute));
				if(count($params)>3)
				{
					$this->addError($attribute, $attribute."不能超过3个");
					
				}
			}
		}
	}

	public function attributeLabels()
	{
		return [
			'source'=>'数据源',
			'start_time'=>'开始时间',
			'end_time'=>'结束时间',
			'uuid'=>'uuid',
			'network'=>'渠道',
			'clickid'=>'clickid',
			'match_type'=>'匹配类型',
			'idfa'=>'idfa',
			'export_type'=>'输出类型',
			'export_path'=>'输出路径'
		];
	}

	public function create()
	{
		if($this->save(false))
		{
			return true;
		}
		return false;
	}



}