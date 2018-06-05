<?php
namespace app\models;

use yii\base\Model;

class Genmob extends Model
{
	public $mob_input;
	public $nums;

	public function attributeLabels()
	{
		return [
			'mob_input'=>'输入子渠道',
			'nums'=>'需要生成总个数'
		];
	}

	public function rules()
	{
		return [
			['nums','required','message'=>'生成个数不能为空'],
			['nums','integer']
		];
	}
}