<?php
namespace app\modules\controllers;

use Yii;
use app\models\Users;
use app\models\profile;
use app\modules\controllers\CommonController;
use yii\data\Pagination;

class UserController extends CommonController
{
	public $layout='layout1';
	
	public function actionUsers()
	{
		$model=Users::find()->joinWith('profile');
		$count=$model->count();
		$pageSize=Yii::$app->params['pageSize']['user'];
		$pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
		$users = $model->offset($pager->offset)->limit($pager->limit)->all();
		return $this->render('users',['users'=>$users,'pager'=>$pager]);
	}
	
	public function actionReg()
	{
		$model = new Users;
		$model->scenario='useradd';
		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			if($model->load($post) && $model->validate())
			{
				if($model->reg())
				{
					Yii::$app->session->setFlash('info','添加成功');
				}
				else
				{
					Yii::$app->session->setFlash('info','添加失败');
				}
			}

		}

		return $this->render('reg',['model'=>$model]);
	}
	

	public function actionDel()
	{
		try
		{
			$userid=(int)Yii::$app->request->get('userid');
			if(empty($userid))
			{
				throw new \Exception();
			}
			$trans=Yii::$app->db->beginTransaction();
			if(Profile::find()->where('userid =:id',[':id'=>$userid])->one())
			{
				if(!Profile::deleteAll('userid=:id',[':id'=>$userid]))
				{
					throw new \Exception();
				}
			}
			if(!Users::deleteAll('userid=:id',[':id'=>$userid]))
			{
				throw new \Exception();
			}
			$trans->commit();
		}
		catch(\Exception $e)
		{
			if (Yii::$app->db->getTransaction()) {
				$trans->rollback();
			}
		}
		$this->redirect(['user/users']);
	}
}