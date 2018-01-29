<?php
namespace app\modules\controllers;

use app\modules\controllers\CommonController;
use app\modules\models\Admin;
use Yii;
use yii\data\Pagination;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class ManageController extends CommonController
{
	public $layout='layout1';

	public function actionMailchangepass()
	{	
		$layout=false;
		$model = new Admin();
		$model->scenario='changepass';
		$adminuser=Yii::$app->request->get('adminuser');
		$token=Yii::$app->request->get('token');
		$time=Yii::$app->request->get('timestamp');
		$mytoken=$model->createToken($adminuser,$time);

		if(time()-$time>5*60)
		{
			$this->redirect(["public/login"]);
		}

		if($token!=$mytoken)
		{
			$this->redirect(["public/login"]);
		}

		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			
			if($model->load($post))
			{		
				if(Yii::$app->request->isAjax)
				{	
					Yii::$app->response->format = Response::FORMAT_JSON;
					if($model->changepass($post))
					{
						return ['code'=>1,'msg'=>'修改成功'];
					}
					return ['code'=>0,'msg'=>'修改失败'];
				}

				if($model->validate())
				{
					if($model->changepass($post))
					{
						Yii::$app->session->setFlash('info','修改成功');
					}
					Yii::$app->session->setFlash('info','修改失败');
				}
			}
		}
		return $this->render("mailchangepass",['model'=>$model]);
	}

	public function actionChangemail()
	{
		$model = new Admin;
		$model->scenario='changemail';
		$model->adminuser=Yii::$app->session['admin']['adminuser'];
		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			$post['Admin']['adminuser']=Yii::$app->session['admin']['adminuser'];
			
			if($model->load($post) && $model->validate())
			{
				if($model->changemail($post))
				{
					Yii::$app->session->setFlash('info','修改成功');
				}
				else
				{
					Yii::$app->session->setFlash('info','修改失败');
				}
			}
		}

		return $this->render('changemail',['model'=>$model]);
	}

	//管理员列表
	public function actionIndex()
	{
		$admin= Admin::find();
		$pager=new Pagination(['totalCount'=>$admin->count(),'pageSize'=>2]);
		$models= $admin->offset($pager->offset)->limit($pager->limit)->all();
		return $this->render('userlist',['models'=>$models,'pages'=>$pager]);
	}

	//添加管理员
	public function actionReg()
	{	
		$model= new Admin;
		$model->scenario='adminadd';

		if(Yii::$app->request->isPost)
		{
			$post= Yii::$app->request->post();
			$pass= $post['Admin']['adminpass'];
			if($model->load($post))
			{
				if(Yii::$app->request->isAjax)
				{
					Yii::$app->response->format=Response::FORMAT_JSON;
					return ActiveForm::validate($model);
				}

				if($model->validate())
				{	
					$model->useradd($post)?Yii::$app->session->setFlash('info','添加成功'):Yii::$app->session->setFlash('info','添加失败');
				}
			}	
			$model->adminpass = $pass;
		}
		
		return $this->render('adminadd',['model'=>$model]);
	}

	//删除管理员
	public function actionDel()
	{
		$adminid = (int)Yii::$app->request->get('adminid');
		if(empty($adminid) || $adminid==1)
		{
			$this->redirect(["manage/index"]);
			return false;
		}

		$model=new Admin();
		if($model->deleteAll('adminid=:id',[':id'=>$adminid]))
		{
			Yii::$app->session->setFlash('info','删除成功');
			$this->redirect(['manage/index']);
		}
	}
}