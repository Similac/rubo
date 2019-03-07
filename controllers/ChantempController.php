<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Chantemp;
use Yii;
use yii\helpers\ArrayHelper;
class ChantempController extends Controller
{

	public function actionIndex()
	{

		$model =  new Chantemp();
		
		$uc_subid=$model->uc_subid;
		$channel_id=$model->channel_id;
		$platform=$model->platform;
		$weibo_subid=$model->weibo_subid;
		$tt_subid=$model->tt_subid;
		$baidu_subid=$model->baidu_subid;
		

		if($model->load(Yii::$app->request->post()) && $model->validate())
		{	
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			$url=$model->genUrl();
			return [
				'status'=>$url
			];
		}
		
		return $this->render('index',
			[
				'model'=>$model,
				'uc_subid'=>$uc_subid,
				'channel_id'=>$channel_id,
				'platform'=>$platform,
				'weibo_subid'=>$weibo_subid,
				'tt_subid'=>$tt_subid,
				'baidu_subid'=>$baidu_subid,

			]);

	}

	public function actionValidation()  
	{  
	    $model = new Chantemp();  
	    $request = Yii::$app->getRequest();  
	    if ($request->isPost && $model->load($request->post())) {  
	        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	        return \yii\widgets\ActiveForm::validate($model);  
	    }  
	} 
}