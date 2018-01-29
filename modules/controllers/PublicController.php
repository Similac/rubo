<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\modules\models\Admin;
use Yii;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class PublicController extends Controller
{

	public $layout=false;
	public function actionLogin()
	{	
		if(Yii::$app->session['admin']['isLogin'])
		{
			$this->redirect(["default/index"]);
		}

		$model= new Admin();
		$model->scenario='login';
		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			
			if($model->load($post) && $model->validate())
			{
				if($model->login($post))
				{
					$this->redirect(["default/index"]);
				}
			}
		}

		return $this->render('login',['model'=>$model]);
	}

	public function actionLogout()
	{
		Yii::$app->session->removeAll();
		if(!isset(Yii::$app->session['admin']['isLogin']))
		{
			$this->redirect(["public/login"]);
			Yii::$app->end();
		}
		$this->goback();
	}

	public function actionSeekpass()
	{	
		
		$model= new Admin();
		$model->scenario='seekpass';
		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			if($model->load($post))
			{
				if(Yii::$app->request->isAjax)
				{
					Yii::$app->response->format=Response::FORMAT_JSON;
					if($model->validate())
					{
						if($model->seekpass($post))
						{
							return ['code'=>1];
						}
					}
					return ['code'=>0,'msg'=>'账号和邮箱不匹配'];
				}

				if($model->validate())
				{
					if($model->seekpass($post))
					{
						Yii::$app->session->setFlash('info','邮件已发送,请查收');
					}
				}
			}

		}

		return $this->render('seekpass',['model'=>$model]);
	}

}