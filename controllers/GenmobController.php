<?php
namespace app\controllers;

use app\controllers\CommonController;
use Yii;
use app\models\Genmob;
use johnnylei\csv\TheCsv;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class GenmobController extends CommonController
{

	public function actionIndex()
	{

		$model = new Genmob;

		if(Yii::$app->request->isPost)
		{
			$post = Yii::$app->request->post();
			if ($model->load($post) && $model->validate()) {
				
				$nums=$post['Genmob']['nums'];
				

				if($post['Genmob']['is_input']=='1')
				{
					if($post['Genmob']['mob_input'])
					{
						
						$mob_inputs=explode("\n", trim($post['Genmob']['mob_input']));
						$mob_inputs=array_filter($mob_inputs);
						$subids=$this->generalbyRandom($mob_inputs,$nums);
						
					}
				}	

				if($post['Genmob']['is_input']=='0')
				{	
					$post['Genmob']['mob_input']='';
					$mob_inputs='';
					$subids=$this->generalbyRandom($mob_inputs,$nums);
				}
				
				$subids_str[]=array();
				
				$subids_str[0][]='{@(['.'\''.implode("','", preg_replace("/[^0-9a-zA-Z]/", "", $subids)).'\''.']'.$this->formatReturn($post['Genmob']['format'],$post['Genmob']['nums']).')@}';
				
				$column_name=[
					'复制下面一行'=>'复制下面一行'
				];
				$file_name=date("Y/m/d/H/i");
				
				$this->exportCsv($subids_str,$column_name,$file_name);
				exit();
			}
		}

		return $this->render('index',['model'=>$model]);
	}

	//表单ajax验证
	public function actionValidation()
	{	
		Yii::$app->response->format = Response::FORMAT_JSON;
		$model = new Genmob();   //这里要替换成自己的模型类
	    $model->load(Yii::$app->request->post());  
	    return ActiveForm::validate($model);
	}

	public function formatReturn($format_select,$nums)
	{
		$format=[
			'0'=>'[parseInt(Math.random()*'.$nums.')]',
			'1'=>'[parseInt((((Math.sin(Math.random())/Math.sin(1))+(Math.sin(Math.random())/Math.sin(1)))/2)*'.$nums.')]',
			'2'=>'[parseInt((((Math.sin(Math.random())/Math.sin(1))+((100-parseInt(parseInt(new Date().getTime()/1000/60/60/15).toString().substr(3,2)))/100))/2)*'.$nums.')]',
			
		];
		return $format[$format_select];
	}

	public function generalbyRandom($mob_input=array(),$nums)
	{	
		if(!empty($mob_input))
		{
			$j = count($mob_input);

			if ($j > 0) {
				$start = ceil(($nums - $j) / 2);
			}

			$subids=[];
			for ($i=0; $i < $nums; $i++) {

				if(empty($mob_input))
				{
					$subids[]='mob'.substr(md5($i.'_'.$i), 0,16);
				}
				else
				{
					if ($i >= $start && $i<$start+$j) {
						$subids[] = $mob_input[$i-$start];
					} else {
						$subids[]='mob'.substr(md5($i.'_'.$i), 0,16);
					}
					
				}
				
			}
		}
		else
		{
			for ($i=0; $i < $nums; $i++) { 
				$subids[]='mob'.substr(md5(time().'_'.$i), 0,16);
			}
		}
		return $subids;
	}

	public function exportCsv($result,$column_name,$file_name)
    {
        $csv = new TheCsv([
            'header'=>$column_name,
            'fileName'=>$file_name.'_export.csv',
        ]);
        $csv->putRows($result);
    }
}