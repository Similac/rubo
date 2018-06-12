<?php
namespace app\models;

use yii\base\Model;

class Genmob extends Model
{
	public $mob_input;
	public $nums;
	public $is_input;
	public $format;

	public function attributeLabels()
	{
		return [
			'mob_input'=>'请输入指定的mob号',
			'nums'=>'需要生成总个数',
			'is_input'=>'是否需要指定mob号',
			'format'=>'请选择公式'
		];
	}

	public function rules()
	{
		return [
			[['nums','format','is_input'],'required'],
			['nums','integer','min'=>1,'max'=>1000],
			['mob_input','requiredbyCustom','skipOnEmpty' => false, 'skipOnError' => false],
		];
	}

	public function requiredbyCustom($attribute,$params)
	{
		if ($this->is_input==1) {
			if(empty($this->mob_input))
			{
				$this->addError($attribute, "指定mob号不能为空");
			}
			else
			{
				$mobs=explode("\n", $this->mob_input);

				if(count($mobs)>$this->nums)
				{
					$this->addError($attribute,'指定mob号不能大于总个数');
				}

				foreach ($mobs as $key => $v) {
					
					$v = preg_replace("/[^0-9a-zA-Z]/", "", $v);

					//检测输入的mob号是否19位
					if(!empty($v) && strlen($v)!=19)
					{
						$this->addError($attribute,$v.'长度必须为19位');
					}

					if(!empty($v) && substr($v, 0,3)!=='mob')
					{
						$this->addError($attribute,$v.'必须以mob开头');
					}

				}

			}

		}
	}

}