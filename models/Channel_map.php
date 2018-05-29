<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Channel_map extends ActiveRecord
{
	public static function getDb()
	{
		return Yii::$app->remote_db;
	}

	public static function tableName()
	{
		return "channel_map";
	}
}