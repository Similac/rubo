<?php
namespace app\common;
use Yii;
use app\models\Campinfo;
use johnnylei\csv\TheCsv;
use app\models\Channel_map;

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
        //     "redshift_data_forPM"
            
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

	//查询manager和network
    public static function checkOm($cbs)
    {
        $sql="select
            cb,network,manager
        from
            channel_map
        where
            cb in ($cbs)";

        $oms=Channel_map::findBySql($sql)->asArray()->all();
        return $oms;
    }

    public static function checkPm($ids)
    {
        $sql="select
            id,uuid,pm
        from
            mob_camp_info
        where
            id in ($ids)";
        $pms=Campinfo::findBySql($sql)->asArray()->all();
        return $pms;
    }

    public static function checkTeamleader($ids)
    {
    	$sql="select
            id,uuid,pm_team
        from
            mob_camp_info
        where
            id in ($ids)";
        $pm_team=Campinfo::findBySql($sql)->asArray()->all();
        return $pm_team;
    }

    public static function getUser($id)
    {
    	$api_url="http://3ss.mobvista.com/users/orm";
    	$client_id="jesS3DmAgEkteZYk";

    	$headers =["Authorization:Basic KyJJAFAHOscg79TA"];

    	$url=$api_url."?".http_build_query(["client_id"=>$client_id,"ac_id"=>$id]);
    	
	      //初始化
	    $curl = curl_init();
	    //设置抓取的url
	    curl_setopt($curl, CURLOPT_URL, $url);
	    //设置头文件的信息作为数据流输出
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    //设置获取的信息以文件流的形式返回，而不是直接输出。
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    //执行命令
	    $data = curl_exec($curl);
	    //关闭URL请求
	    curl_close($curl);
	    return $data;
	}
}