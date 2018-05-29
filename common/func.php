<?php
namespace app\common;
use Yii;

class func{

	public static function getPermissions()
	{
        return (array) \Yii::$app->session['user']['permissions']->all;
        // return [
        //     "fix/index",
        //     "load/index",
        //     "load/list",
        //     "redshift/deducted",
        //     "redshift/index",
        //     "redshift_data_forTO"
        // ];
	}

	public static function getRole()
	{
		if(in_array('redshift_data_forTO', self::getPermissions()))
		{
			return [
				'role'=>'to',
				'username'=>Yii::$app->session['user']['username'],
			];
		}
		if(in_array('redshift_data_forPM', self::getPermissions()))
		{
			return [
				'role'=>'pm',
				'username'=>Yii::$app->session['user']['username'],
			];
		}
		if(in_array('redshift_data_forAll', self::getPermissions()))
		{
			return [
				'role'=>'all',
				'username'=>Yii::$app->session['user']['username'],
			];
		}
	}
}