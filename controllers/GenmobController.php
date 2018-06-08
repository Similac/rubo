<?php
namespace app\controllers;

use app\controllers\CommonController;
use Yii;
use app\models\Genmob;
use johnnylei\csv\TheCsv;

class GenmobController extends CommonController
{

	

	public function actionIndex()
	{

		$model = new Genmob;

		if(Yii::$app->request->isPost)
		{
			$post = Yii::$app->request->post();
			$nums=$post['Genmob']['nums'];
			if($post['Genmob']['mob_input'])
			{	
				
				$mob_inputs=explode("\n", trim($post['Genmob']['mob_input']));

				$subids=$this->generalbyRandom($mob_inputs,$nums);
				
			}
			else
			{	
				$mob_inputs='';
				$subids=$this->generalbyRandom($mob_inputs,$nums);
			}

			$subids_str[]=array();

			$subids_str[0][]='{@(['.'\''.implode("','", $subids).'\''.']'.'[parseInt((((Math.sin(Math.random())/Math.sin(1))+((100-parseInt(parseInt(new Date().getTime()/1000/60/60/10).toString().substr(3,2)))/100))/2)*'.$nums.')]'.')@}';
			//var_dump($subids_str);

			$column_name=[
				'format'=>'format'
			];
			$file_name=date("Y/m/d/H/i");
			

			$this->exportCsv($subids_str,$column_name,$file_name);
			exit();
		}

		return $this->render('index',['model'=>$model]);
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
				$subids[]='mob'.substr(md5($i.'_'.$i), 0,16);
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