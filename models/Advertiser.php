<?php
namespace app\models;

use yii\db\ActiveRecord;

class Advertiser extends ActiveRecord
{
	public static function tableName()
	{
		return '{{%advertiser}}';
	}
}