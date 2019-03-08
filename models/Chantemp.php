<?php
namespace app\models;

use yii\base\Model;

class Chantemp extends Model
{
	public $uuid;
	public $platform=['android'=>'android','ios'=>'ios'];
	public $channel_id=[
	'13557'=>'13557--micro_media_uc',
	'13948'=>'13948--micro_media_baidu_sem',
	'13388'=>'13388--micro_media_baidu',
	'13353'=>'13353--micro_media_cn_tencent',
	'13951'=>'13951--micro_media_toutiao_android',
	'13512'=>'13512--micro_media_cn_weibo',
	'13390'=>'13390--micro_media_toutiao'];
	//UC subid,{AID}广告单元id,{CID}广告创意id
	public $uc_subid=['{AID}'=>'{AID}','{CID}'=>'{CID}'];
	//微博subid,需手动填写
	public $weibo_subid=['1'=>'1'];
	//头条subid,__AID__头条广告计划id,__CID__创意ID
	public $tt_subid=['__AID__'=>'__AID__','__CID__'=>'__CID__'];
	//百度subid,{{IDEA_ID}} 创意id,{{PLAN_ID}} 计划id,{{UNIT_ID}} 单元id,{{WORDID}} 关键词id
	public $baidu_subid=['{{IDEA_ID}}'=>'{{IDEA_ID}}','{{PLAN_ID}}'=>'{{PLAN_ID}}','{{UNIT_ID}}'=>'{{UNIT_ID}}'];

	public $akey;
	//广点通
	public $account_id;
	//广点通
	public $user_action_set_id;
	//广点通
	public $refresh_token;
	//广点通event数目,决定前台生成多少个event输入框
	public $gdt_event_nums=6;
	//广点通event_name
	public $gdt_event_name;
	//头条event数目,决定页面生成多少个event输入框
	public $tt_event_nums=3;
	//头条event_name
	public $tt_event_name;
	//微博event_name
	public $weibo_event_name;
	//微博event数目,决定页面生成多少个event输入框
	public $weibo_event_nums=3;
	public $gdt_action_type=[
		'REGISTER'=>'REGISTER',
		'ADD_TO_CART'=>'ADD_TO_CART',
		'PURCHASE'=>'PURCHASE',
		'APPLY'=>'APPLY',
		'START_APP'=>'START_APP',
		'COMPLATE_ORDER'=>'COMPLATE_ORDER'
	];
	public $tt_event_type=[
		'1'=>'1--注册',
		'2'=>'2--付费',
		'6'=>'6--次留',
	];
	public $weibo_action_type=[
		'2'=>'2--下单购买',
		'3'=>'3--注册',
		'4'=>'4--付费'
	];

	//uc tracking link template
	public $uc_tracking_link='http://tracking.lenzmx.com/async_click?idfa_sum={IDFA_SUM}&mb_imei={IMEI_SUM}&android_sum={ANDROIDID_SUM}&ip={IP}&ts={TS}&os={OS}&callback={CALLBACK_PARAM}&mb_ua={UA}';
	//uc postback
	public $uc_callback='http://huichuan.sm.cn/td?tp_type=roi&callback_param={$callback}';
	
	//百度tracking link
	public $baidu_tracking_link='http://tracking.lenzmx.com/async_click?mb_auth=EmHXHy_uV1zBj-SV&idfa={{IDFA}}&mb_imei={{IMEI_MD5}}&mb_devid={{ANDROID_ID}}&os={{OS}}&ip={{IP}}&ua={{UA}}&ts={{TS}}&click_id={{CLICK_ID}}&callback_url={{CALLBACK_URL}}&sign={{SIGN}}';
	//百度callback
	public $baidu_callback='http://fwdstat.mobvista.com/baidu?callback="+encodeURIComponent($callback_url)+"&key="+encodeURIComponent("{akey}")+"';

	//广点通tracking link
	public $gdt_tracking_link='https://trackingsh.rayjump.com/async_click?&mb_auth=REnl5yAtd8Na2HF6&mb_ip=';
	//广点通callback
	public $gdt_callback='http://rp.mobvista.com/gdt?log={$campid}&timeout=5&retry=10&client_id=1107820842&client_secret=qkfp1EEfGkiyoBcn&clickid={$mb_clkid}&action_type=ACTIVATE_APP';
	public $gdt_event_callback='rp.mobvista.com/gdt?log={$campid}&timeout=5&retry=10&client_id=1107820842&client_secret=qkfp1EEfGkiyoBcn';

	//头条tracking link
	public $tt_tracking_link='https://tracking.lenzmx.com/async_click?mb_auth=HhTlzUDRhy6u1hGw&idfa=__IDFA__&mb_ip=__IP__&mb_devid=__ANDROIDID1__&mb_imei=__IMEI__&aff_sub=__IMEI__&callback=__CALLBACK_PARAM__&os=__OS__&amptimestamp=__TS__';
	//头条callback
	public $tt_callback='http://rp.mobvista.com/pb?callback=http%3a%2f%2fad.toutiao.com%2ftrack%2factivate%2f%3fcallback%3d{$callback}%26os%3d{$os}&timeout=3&retry=8&log={$campid}%26muid%3d';
	public $tt_event_callback='rp.mobvista.com/pb?timeout=3&retry=8&log={$campid}&callback=http%3a%2f%2fad.toutiao.com%2ftrack%2factivate%2f%3fcallback%3d"+info.match_result.data.query["callback"]+"%26os%3d"+info.match_result.data.query["os"]+"';

	//微博
	public $weibo_tracking_link='https://tracking.lenzmx.com/async_click?mb_auth=Z8EudvjHilT8jQk7&mb_ip={ip}&clicktime={clicktime}&idfa_md5={idfa_MD5}&mb_imei={imei_MD5}&mb_os={osversion}&mb_device={devicetype}&IMP={IMP}';
	//weibo postback
	public $weibo_callback='http://appmonitor.biz.weibo.com/sdkserver/active?company=huiliangxinxi&IMP={$IMP}';
	public $weibo_event_callback='appmonitor.biz.weibo.com/sdkserver/active?company=huiliangxinxi&IMP={$IMP}&action_type=';


	
	public function rules()
	{
		return [
			[['uuid','platform','channel_id'],'required'],
			['uc_subid','ucrequiredBynetwork','skipOnEmpty' => false, 'skipOnError' => false],
			[['akey','baidu_subid'],'baidurequiredBynetwork','skipOnEmpty' => false, 'skipOnError' => false],
			[['account_id','user_action_set_id','refresh_token'],'gdtrequiredBynetwork','skipOnEmpty' => false, 'skipOnError' => false],
			['tt_subid','ttrequiredBynetwork','skipOnEmpty' => false, 'skipOnError' => false],
			['weibo_subid','weiborequiredBynetwork','skipOnEmpty' => false, 'skipOnError' => false],
			[['gdt_event_name','gdt_action_type','tt_event_name','tt_event_type','weibo_event_name','weibo_action_type'],'safe']
		];
	}

	// public function attributeLabels()
	// {
	// 	return [
			
	// 	];
	// }

	public function ucrequiredBynetwork($attribute,$params)
	{
		if($this->channel_id=='13557' && $this->uc_subid=='')
		{
			$this->addError($attribute,$attribute.'不能为空');
		}
	}

	public function baidurequiredBynetwork($attribute,$params)
	{
		if(($this->channel_id=='13948'||$this->channel_id=='13388') && ($this->akey=='' || $this->baidu_subid==''))
		{
			$this->addError($attribute,$attribute.'不能为空');
		}
	}

	public function gdtrequiredBynetwork($attribute)
	{
		if($this->channel_id=='13353' && ($this->account_id=='' || $this->user_action_set_id==''|| $this->refresh_token==''))
		{
			$this->addError($attribute,$attribute.'不能为空');
		}
	}

	public function ttrequiredBynetwork($attribute)
	{
		if(($this->channel_id=='13951'|| $this->channel_id=='13390') && $this->tt_subid=='')
		{
			$this->addError($attribute,$attribute.'不能为空');
		}
	}

	public function weiborequiredBynetwork($attribute)
	{
		if($this->channel_id=='13512' && $this->weibo_subid=='')
		{
			$this->addError($attribute,$attribute.'不能为空');
		}
	}
	
	//生成tracking link和postback
	public function genUrl()
	{
		switch ($this->channel_id) {
			//uc
			case '13557':
				
				$url[]=$this->uc_tracking_link.'&mb_subid='.$this->uc_subid.'&mb_auth=Wiy8dRGqkZcc-Tvf'.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id;
				$url[]=$this->uc_callback;
				return $url;

				break;
			//百度	
			case '13948':
				
				$url[]=$this->baidu_tracking_link.'&mb_subid='.$this->baidu_subid.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id;
				$url[]=str_replace('{akey}', $this->akey, $this->baidu_callback);
				return $url;

				break;
			//百度	
			case '13388':
				
				$url[]=$this->baidu_tracking_link.'&mb_subid='.$this->baidu_subid.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id;
				$url[]=str_replace('{akey}', $this->akey, $this->baidu_callback);
				return $url;

				break;
			//广点通	
			case '13353':

				if($this->platform=='android')
				{
					$params="&hash_imei={\$muid}&hash_idfa=";
					$event_params='&hash_imei="+info.match_result.data.query["muid"]+"&hash_idfa=';
				}
				else
				{
					$params="&hash_imei=&hash_idfa={\$muid}";
					$event_params='&hash_imei=&hash_idfa="+info.match_result.data.query["muid"]+"';
				}

				$url[]=$this->baidu_tracking_link.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id;
				$url[]=$this->gdt_callback.'&os='.$this->platform.'&refresh_token='.$this->refresh_token.'&account_id='.$this->account_id.'&user_action_set_id='.$this->user_action_set_id.$params;


				//如果gdt_event_name不为空,生成event postback
				if (!empty($this->gdt_event_name)) {
					$link='';
					//最后面拼接链接的括号
					$nums=count(array_filter($this->gdt_event_name));
					foreach (array_filter($this->gdt_event_name) as $k=>$v) {
						
						$link.='($event_name=='.'\''.$v.'\''.'?('.'"'.$this->gdt_event_callback.'os='.$this->platform.$event_params.'&refresh_token='.$this->refresh_token.'&account_id='.$this->account_id.'&user_action_set_id='.$this->user_action_set_id.'&action_type='.$this->gdt_action_type[$k].'"):';
						
					}

					$url[]='http://{'.$link.'\''.'baidu.com?aaa=1'.'\''.str_repeat(')',$nums).'}';

				}
				return $url;

				break;

			//头条
			case '13951':

				if($this->platform=='android')
				{
					$params="{\$aff_sub}";
					$event_params='["aff_sub"]';
				}
				else
				{
					$params="{\$idfa}";
					$event_params='["idfa"]';
				}

				$url[]=$this->tt_tracking_link.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id.'&mb_subid='.$this->tt_subid;
				$url[]=$this->tt_callback.$params;


				//如果gdt_event_name不为空,生成event postback
				if (!empty($this->tt_event_name)) {
					$link='';
					//最后面拼接链接的括号
					$nums=count(array_filter($this->tt_event_name));
					foreach (array_filter($this->tt_event_name) as $k=>$v) {
						
						$link.='($event_name=='.'\''.$v.'\''.'?('.'"'.$this->tt_event_callback.
							'%26event_type%3d'.$this->tt_event_type[$k].'%26muid%3d"+info.match_result.data.query'.$event_params.'"):';
						
					}

					$url[]='http://{'.$link.'\''.'baidu.com?aaa=1'.'\''.str_repeat(')',$nums).'}';

				}

				return $url;

				break;
			//微博
			case '13512':

				
				$url[]=$this->weibo_tracking_link.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id.'&mb_subid='.$this->weibo_subid;
				$url[]=$this->weibo_callback;


				//如果gdt_event_name不为空,生成event postback
				if (!empty($this->weibo_event_name)) {
					$link='';
					//最后面拼接链接的括号
					$nums=count(array_filter($this->weibo_event_name));
					foreach (array_filter($this->weibo_event_name) as $k=>$v) {
						
						$link.='($event_name=='.'\''.$v.'\''.'?('.'"'.$this->weibo_event_callback.
							'&action_type='.$this->weibo_action_type[$k].'"):';
						
					}

					$url[]='http://{'.$link.'\''.'baidu.com?aaa=1'.'\''.str_repeat(')',$nums).'}';

				}

				return $url;

				break;

			//头条
			case '13390':

				if($this->platform=='android')
				{
					$params="{\$aff_sub}";
					$event_params='["aff_sub"]';
				}
				else
				{
					$params="{\$idfa}";
					$event_params='["idfa"]';
				}

				$url[]=$this->tt_tracking_link.'&mb_campid='.$this->uuid.'&mb_pl='.$this->platform.'&mb_nt=cb'.$this->channel_id.'&mb_subid='.$this->tt_subid;
				$url[]=$this->tt_callback.$params;


				//如果gdt_event_name不为空,生成event postback
				if (!empty($this->tt_event_name)) {
					$link='';
					//最后面拼接链接的括号
					$nums=count(array_filter($this->tt_event_name));
					foreach (array_filter($this->tt_event_name) as $k=>$v) {
						
						$link.='($event_name=='.'\''.$v.'\''.'?('.'"'.$this->tt_event_callback.
							'%26event_type%3d'.$this->tt_event_type[$k].'%26muid%3d"+info.match_result.data.query'.$event_params.'"):';
						
					}

					$url[]='http://{'.$link.'\''.'baidu.com?aaa=1'.'\''.str_repeat(')',$nums).'}';

				}

				return $url;

				break;

			default:
				# code...
				break;
		}
	}
}