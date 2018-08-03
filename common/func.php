<?php
namespace app\common;
use Yii;
use app\models\Campinfo;
use johnnylei\csv\TheCsv;
use app\models\Channel_map;
class func{

	public static function getPermissions()
	{
        //return (array) \Yii::$app->session['user']['permissions']->all;
        return [
            "fix/index",
            "load/index",
            "load/list",
            "redshift/deducted",
            "redshift/index",
            "redshift_data_forPM"
        ];
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

	public static function exportCsv($result,$file_name)
	{	

		$keys=array_keys($result[0]);
        $column_name=array_combine($keys, $keys);

		$csv = new TheCsv([
            'header'=>$column_name,
            'fileName'=>$file_name.'_export.csv',
        ]);
        $csv->putRows($result);
        exit();
	}

	public static function getByuuid($uuid)
	{	
		if(empty($uuid))
		{
			return '';
		}
		$sql="select id,uuid from mob_camp_info where uuid like '%$uuid%' limit 6";
        $camp=Campinfo::findBySql($sql)->asArray()->all();
		return $camp;
	}

	public static function getByadvertiser($advertiser)
	{
		if(empty($advertiser))
		{
			return '';
		}
		$sql="select distinct(adv_name) from mob_camp_info where adv_name like '%$advertiser%' limit 6";
        $camp=Campinfo::findBySql($sql)->asArray()->all();
		return $camp;
	}

	public static function getBychannel($channel)
	{
		if(empty($channel))
		{
			return '';
		}
		$sql="select cb,network from channel_map where network like '%$channel%' limit 6";
        $camp=Channel_map::findBySql($sql)->asArray()->all();
		return $camp;
	}

}