<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Select extends ActiveRecord
{


	public static function tableName()
	{
		return '{{selects}}';
	}
}