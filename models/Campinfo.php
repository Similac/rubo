<?php
namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Campinfo extends ActiveRecord
{
	
	public static function getDb()
    {
        return Yii::$app->remote_db;
    }

	public static function tableName()
    {

    	return 'mob_camp_info';
    }
}