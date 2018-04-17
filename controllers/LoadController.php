<?php
namespace app\controllers;

use app\models\S3FIle;
use Aws\Api\DateTimeResult;
use Aws\AwsClient;
use Aws\Sdk;
use yii\web\Controller;
use app\models\Load;
use Yii;
use yii\web\Response;
use yii\data\Pagination;
use yii\bootstrap\ActiveForm;
use ZipArchive;
use yii\helpers\Url;
class LoadController extends CommonController
{
	/*zip文件根目录*/
	const ZIP_ROOT=__DIR__.'/../views/package/';
	/*job必须文件1*/
	const JOB_FILE1=__DIR__.'/../views/package/job/start.job';
	/*job必须文件2*/
	const JOB_FILE2=__DIR__.'/../views/package/job/uuid.channel.subid.ip.count.jar';
	
	/*content-type*/
	const CONTENT_TYPE1="application/x-www-form-urlencoded";
	const CONTENT_TYPE2="multipart/mixed";
	const SUCCESS_IMG="/basic/web/static/img/weiwei.jpg";
	const ERROR_IMG="/basic/web/static/img/jinjian.jpg";

	protected $prePath = "test/log/";

	protected $bucket = "mob-export-log-support";

	public function actions()
	{
		return [
			'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
		];
	}

	public function actionIndex($id="")
	{	
		if(!empty($id))
		{
			$model=Load::findOne($id);
			
			return $this->render('index',['model'=>$model]);
		}


		$model= new Load();

		if(Yii::$app->request->isPost)
		{
			$post= Yii::$app->request->post();
			
			
			/*判断soure对应的log*/
			switch ($post['Load']['source']) 
			{
				case '0':
					$source="install";
					break;
				case '1':
					$source="click";
					break;
				case '2':
					$source="event";
					break;
				case '3':
					$source="inject_code_log";
					$post['Load']['network']='';
					//$post['Load']['match_type']='';
					$post['Load']['clickid']='';
					$post['Load']['idfa']='';
					break;
			}

			/*判断match_type*/
			switch ($post['Load']['match_type']) 
			{
				case '0':
					$clickids="null";
					$idfas="null";
					break;
				case '1':
					/*逗号拼接clickids*/
					$post['Load']['idfa']="";
					if(!empty($post['Load']['clickid']))
					{	
						$clickids="";
						$arr=explode("\r\n",trim($post['Load']['clickid']));
						for ($i=0; $i <count($arr) ; $i++) 
						{
							if(!empty($arr[$i]))
							{
								$clickids.=$arr[$i].($i==count($arr)-1?'':',');
							}
							
						}
					}
					
					$idfas="null";
					break;
				
				case '2':
					/*逗号拼接idfas*/
					$post['Load']['clickid']="";
					if(!empty($post['Load']['idfa'])){
						
						$idfas='';
						$arr=explode("\r\n",trim($post['Load']['idfa']));
						for ($i=0; $i <count($arr) ; $i++) 
						{ 
							if(!empty($arr[$i]))
							{
								$idfas.=$arr[$i].($i==count($arr)-1?'':',');
							}
						}
					}

					$clickids="null";
					break;
			}
			
			/*截取uuid,以逗号拼接*/
			if(!empty($post['Load']['uuid']))
			{
				$uuids='';
				$arr=explode("\r\n",trim($post['Load']['uuid']));
				for ($i=0; $i <count($arr) ; $i++) 
				{ 
					if(!empty($arr[$i]))
					{
						$uuids.=$arr[$i].($i==count($arr)-1?'':',');
					}
					
				}
				
			}else
			{
				$uuids="null";
			}

			/*截取network,以逗号拼接*/
			if(!empty($post['Load']['network']))
			{
				$networks='';
				$arr=explode("\r\n",trim($post['Load']['network']));
				for ($i=0; $i <count($arr) ; $i++) 
				{ 
					if(!empty($arr[$i]))
					{
						$networks.=$arr[$i].($i==count($arr)-1?'':',');
					}
					
				}
				
			}
			else
			{
				$networks="null";
			}
			Yii::$app->response->format=Response::FORMAT_JSON;
			if(Yii::$app->request->isAjax)
			{

				if($model->load($post) && $model->validate())
				{

					//开始及结束时间转换成Y-m-d H格式
					$start_date=explode(" ", date("Y-m-d H",strtotime($post['Load']['start_time'])));
					$start_time=$start_date[0].'-'.$start_date[1];
					$end_date=explode(" ", date("Y-m-d H",strtotime($post['Load']['end_time'])));
					$end_time=$end_date[0].'-'.$end_date[1];
					$export_type=($post['Load']['export_type'])?"json":"csv";


					if($destination=$this->toTxt($source,$start_time,$end_time,$uuids,$networks,$clickids,$idfas,$export_type))
					{	
					
						$destination1=$destination.'/start.job';
						$destination2=$destination.'/uuid.channel.subid.ip.count.jar';
						if($this->copyFile(self::JOB_FILE1,self::JOB_FILE2,$destination1,$destination2))
						{	
							$zip_name=$this->toZip($destination);
							if($zip_name)
							{
								if($this->delDir($destination))
								{	
									//登录接口
									$login_url="https://dataplatform.mobvista.com:8444/";
									$data="action=login&username=rubo.chen&password=rubo.chen_psfw";
									if($response=$this->toArray($this->azkabanTool($login_url,$data,self::CONTENT_TYPE1,'post')))
									{
										//如果登录成功
										if($response['status']=='success')
										{	
											$session=Yii::$app->session;
											$session->set("session_id",$response['session.id']);
											$session_id=$session->get("session_id");
											$name='test_'.time();
											$post['Load']['project_name']=$name;
											//创建project需要的信息
											$project_info="session.id=$session_id&name=$name&description=11";
											//创建project接口
											$create_url="https://dataplatform.mobvista.com:8444/manager?action=create";
											
											if($res2=$this->toArray($this->azkabanTool($create_url,$project_info,self::CONTENT_TYPE1,'post')))
											{
												if($res2['status']=='success')
												{
													//上传接口
													$upload_url="https://dataplatform.mobvista.com:8444/manager";
													
													//zip文件路径
													//$file_des=$zip_name;
													$project=$name;
													$cFile = curl_file_create($zip_name, 'application/zip', 'test.zip');
													$upload_data = [
														'type'=>'application/zip',
														"session.id" => $session_id,
														"project" => $project,
								                        "file" => $cFile,
								                        'ajax' => 'upload'
							                        ];
						
													if($res3=$this->toArray($this->azkabanTool($upload_url,$upload_data,self::CONTENT_TYPE2,'post')))
													{
														if(!isset($res3['error']))
														{	
															$post['Load']['project_id']=$res3['projectId'];
															//开启job接口
															$execute_url="https://dataplatform.mobvista.com:8444/executor?ajax=executeFlow&session.id=$session_id&project=$project&flow=start";
															if($res4=$this->toArray($this->azkabanTool($execute_url,'',self::CONTENT_TYPE1,"get")))
															{
																if($res4['execid'])
																{	
																	$post['Load']['execute_id']=$res4['execid'];
																	$post['Load']['created_at']=time();
					 	    										$post['Load']['clickid']=empty($post['Load']['clickid'])?0:count(explode("\r\n", trim($post['Load']['clickid'])));
																	$post['Load']['idfa']=empty($post['Load']['idfa'])?0:count(explode("\r\n", trim($post['Load']['idfa'])));
														        	$post['Load']['export_path']='xxx';
																	if($model->load($post)&&$model->create())
														        	{
														        		return ['status'=>1,'msg'=>'操作成功'];
														        	}
														        	return ['status'=>0,'msg'=>'操作失败'];
																}
															}
															return ['status'=>0,'msg'=>'azkaban执行失败'];
														}
														return ['status'=>0,'msg'=>'上传zip失败'];
													}
													
												}
												return ['status'=>0,'msg'=>'创建project失败'];
											}
										}
										return ['status'=>0,'msg'=>'登录azkaban失败'];
									}
									
								}
								return ['status'=>0,'msg'=>'删除原文件失败'];
							}
							return ['status'=>0,'msg'=>'生成zip失败'];	
						}
						return ['status'=>0,'msg'=>'copy文件失败'];
					}
					return ['status'=>0,'msg'=>'写txt失败'];
				}
				
			}	
		}

		return $this->render('index',['model'=>$model]);

	}

	//表单ajax验证
	public function actionValidation()
	{	
		Yii::$app->response->format = Response::FORMAT_JSON;
		$model = new Load();   //这里要替换成自己的模型类
	    $model->load(Yii::$app->request->post());  
	    return ActiveForm::validate($model);
	}

	/*json数据转成数组*/
	public function toArray($json)
	{
		return json_decode($json,true);
	}

	/*
	*登录azkaban,创建project以及上传zip文件
	*$data 对应不同操作传值
	*return json
	*/
	protected function azkabanTool($url,$data='',$content_type=self::CONTENT_TYPE1,$method="post")
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_PORT => "8444",
		    CURLOPT_URL => $url,
		    CURLOPT_SSL_VERIFYPEER => false,
		    CURLOPT_SSL_VERIFYHOST => false,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_POST => $method == 'get' ? false : true,
		    CURLOPT_POSTFIELDS => $data,
		    CURLOPT_HTTPHEADER => array(
		        "cache-control: no-cache",
		        "content-type: $content_type",
		    ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		    return $err;
		} else {
		    return $response;
		}
		
	}

	/*
	*生成txt文件
	*return 生成文件目录路径;
	*/
	protected function toTxt($source='',$start_time,$end_time,$uuids='',$networks='',$clickids='',$idfas='',$export_type)
	{	
		$dir_name=date('Y-m-d-H-i-s',time());
		//创建文件夹
		if(mkdir(self::ZIP_ROOT.$dir_name))
		{
			
		$fp=fopen(self::ZIP_ROOT.$dir_name.'/main.sh', 'w');

$txt=<<<txt
#!/usr/bin/sh
#MR
hadoop="hadoop"
EXTIONID=`printf \$PWD | awk -F '/' '{printf \$NF}'`
jar_path="./uuid.channel.subid.ip.count.jar"
HDFS_OUT_PATH="s3://mob-export-log-support/test/log/\${EXTIONID}/"
SOURCE="$source"
UUIDS="$uuids"
CHANNELS="$networks"
CLICKIDS="$clickids"
IDFAS="$idfas"
STARTDATE="$start_time"
ENDDATE="$end_time"
hadoop jar  uuid.channel.subid.ip.count.jar \${HDFS_OUT_PATH} \${SOURCE} \${UUIDS} \${CHANNELS} \${CLICKIDS} \${IDFAS} \${STARTDATE} \${ENDDATE}

echo "DONE"
txt;
		//$txt=

// '#!/usr/bin/sh
// #MR
// hadoop="hadoop"
// jar_path="./uuid.channel.subid.ip.count.jar"
// SOURCE='."\"$source\"".'
// START_DATE='."\"$start_time\"".'
// END_DATE='."\"$end_time\"".'
// UUIDS='."\"$uuids\"".'
// CHANNELS='."\"$networks\"".'
// CLICKIDS='."\"$clickids\"".'
// IDFAS='."\"$idfas\"".'
// EXPORT_TYPE='."\"$export_type\"".'
// ${hadoop} jar  ${jar_path} ${SOURCE} ${UUIDS} ${CHANNELS} ${CLICKIDS} ${IDFAS}
// echo "INSTALL DONE"';
			
			fwrite($fp, $txt);
			return self::ZIP_ROOT.$dir_name;
			fclose($fp);
		}
	}

	/*
	*复制job必须文件到指定文件夹
	*return true/false
	*/

	protected function copyFile($file1,$file2,$destination1,$destination2)
	{	
		if(copy($file1, $destination1) && copy($file2,$destination2))
		{
			return true;
		}
		
	}
	

	/*
	*生成zip文件
	*return true/false
	*/
	protected function toZip($dir_name)
	{
		$zip = new ZipArchive;
		$zip_name=self::ZIP_ROOT.time().'.zip';
		$res = $zip->open($zip_name, ZipArchive::CREATE);
		$handler=opendir($dir_name); //打开当前文件夹由$path指定。
		
		while(($filename=readdir($handler))!==false)
		{
			if($filename != "." && $filename != "..")//文件夹文件名字为'.'和‘..’，不要对他们进行操作
			{
				//将文件加入zip对象
				$zip->addFile($dir_name."/$filename",basename($dir_name."/$filename"));
		   	}
		  	
		}
		
		
		closedir($handler);
		$zip->close();
		return $zip_name;
	}

		/*
	*生成压缩文件后删除源文件
	*return true/false
	*/
	protected function delDir($del_file)
	{
//		$fp=opendir($del_file);
//		while (($filename=readdir($fp))!==false)
//		{
//			if($filename!="." && $filename!="..")
//			{
//				unlink($del_file."/$filename");
//			}
//		}
//		closedir( $fp );
//		return rmdir($del_file);
        return true;
	}


	public function actionList()
	{
		$load=Load::find();

		$pages= new Pagination([
			'totalCount'=>$load->count(),
			'pageSize'=>7
		]);

		$model=$load->offset($pages->offset)->limit($pages->limit)->orderBy('created_at DESC')->all();

		return $this->render('list',['model'=>$model,'pages'=>$pages]);
	}

	public function actionProgress()
	{	
		$execute_id=Yii::$app->request->get('execute_id');
		if(!isset($execute_id))
		{
			$this->redirect(['load/list']);
		}
		$session=Yii::$app->session;
		$session_id=$session->get('session_id');
		//如果session失效,重新登录获取session
		if(!isset($session_id))
		{
			//登录接口
			$login_url="https://dataplatform.mobvista.com:8444/";
			$data="action=login&username=rubo.chen&password=rubo.chen_psfw";
			$response=$this->toArray($this->azkabanTool($login_url,$data,self::CONTENT_TYPE1,'post'));
			if($response['status']=='success')
			{	
				$session->set("session_id",$response['session.id']);
				$session_id=$session->get("session_id");
			}
		}
		//获取进度接口
		$progress_url="https://dataplatform.mobvista.com:8444/executor?ajax=fetchExecJobLogs&execid=$execute_id&jobId=start&session.id=$session_id&offset=0&length=5000000&attempt=0";
		
		$response=$this->azkabanTool($progress_url,'',self::CONTENT_TYPE1,'get');
		$res=json_decode($response,true);
		$shuju['data']=$res['data'];
		
		return $this->render('progress',['model'=>$shuju,'execute_id'=>$execute_id]);
	}

	public function actionCancelflow()
	{	
		Yii::$app->response->format=Response::FORMAT_JSON;
		$execute_id=Yii::$app->request->get('execute_id');
		if(!isset($execute_id))
		{
			return ['status'=>0,'msg'=>'kill失败','img'=>self::ERROR_IMG];
		}
		$session=Yii::$app->session;
		$session_id=$session->get('session_id');
		if(!isset($session_id))
		{
			//登录接口
			$login_url="https://dataplatform.mobvista.com:8444/";
			$data="action=login&username=rubo.chen&password=rubo.chen_psfw";
			$response=$this->toArray($this->azkabanTool($login_url,$data,self::CONTENT_TYPE1,'post'));
			if($response['status']=='success')
			{	
				$session->set("session_id",$response['session.id']);
				$session_id=$session->get("session_id");
			}
		}
		//取消flow接口
		$cancel_url="https://dataplatform.mobvista.com:8444/executor?ajax=cancelFlow&session.id=$session_id&execid=$execute_id";
		$result=$this->azkabanTool($cancel_url,'',self::CONTENT_TYPE1,'get');
		if(!isset($this->toArray($result)['error']))
		{
			return ['status'=>1,'msg'=>'已成功kill','img'=>self::SUCCESS_IMG];
		}
		return ['status'=>0,'msg'=>$this->toArray($result)['error'],'img'=>self::ERROR_IMG];
		
	}

	public function actionDown(){

	    $files = array();

	    if(Yii::$app->request->isGet){

	        $get = Yii::$app->request->get();
	        $execute_id = $get['execute_id'];

	        $aws = Yii::$app->aws->getClient();

            $s3 = $aws->createS3([
                'Bucket' => 'mob-export-log-support',
                'endpoint'=>'https://s3-external-1.amazonaws.com',
            ]);

            $objects = ($s3->listObjects([
                'Bucket' => $this->bucket,
                'Prefix' => "test/log/$execute_id/",
            ]));
            if($objects['Contents'] != null){

                foreach ($objects['Contents'] as $file){
                    $s3File = new S3FIle();
                    $s3File->key = $file['Key'];
                    $s3File->size = $file['Size'];
                    $s3File->date = $file['LastModified']->format(\DateTime::ISO8601);
                    $args = [
                        'Bucket' => $this->bucket,
                        'Key' => $s3File->key,
                        'ResponseContentType' => 'application/octet-stream'
                    ];

                    $cmd = $s3->getCommand('GetObject',$args);
                    $s3File->url = $s3->createPresignedRequest($cmd,'+1 minutes')->getUri()."\n";
                    $files[] = $s3File;
                }
            }
        }

	    return $this->render('down',['files'=>$files]);

    }
}