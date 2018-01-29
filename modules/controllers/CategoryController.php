<?php
namespace app\modules\controllers;

use app\modules\controllers\CommonController;
use Yii;
use app\models\Category;

class CategoryController extends CommonController
{	
	public $layout='layout1';
	public function actionAdd()
	{
		$model = new Category;
		$list=$model->getOptions();

		if(Yii::$app->request->isPost)
		{
			$post= Yii::$app->request->post();
			$post['Category']['createtime']=time();
			if($model->load($post) && $model->validate())
			{
				if($model->add($post))
				{
					Yii::$app->session->setFlash('info','添加成功');
				}
				else
				{
					Yii::$app->session->setFlash('info','添加失败');
				}
			}
		}
		
		return $this->render('cateadd',['model'=>$model,'list'=>$list]);
	}

	public function actionList()
	{	
		$model = new Category;
		$cates=$model->getTreeList();
		return $this->render('cates',['cates'=>$cates]);
	}

	//修改分类
	public function actionMod()
	{	
		$cateid=Yii::$app->request->get('cateid');
		$model=Category::find()->where('cateid =:id',[':id'=>$cateid])->one();
		$list=$model->getOptions();
		if(Yii::$app->request->isPost)
		{
			$post= Yii::$app->request->post();
			if($model->load($post) && $model->save())
			{
				Yii::$app->session->setFlash('info','修改成功');
			}else
			{
				Yii::$app->session->setFlash('info','修改失败');
			}
		}

		return $this->render('cateadd',['model'=>$model,'list'=>$list]);
	}

	public function actionDel()
	{
		try
		{
			$cateid=Yii::$app->request->get('cateid');
			if(empty($cateid))
			{
				throw new \Exception("参数错误");
			}
			$cate=Category::find()->where('parentid=:id',[':id'=>$cateid])->count();
			if($cate)
			{
				throw new \Exception("该分类下有子类，不能删除");
			}
			if(!Category::deleteAll('cateid=:id',[':id'=>$cateid]))
			{
				throw new \Exception("删除失败");
			}
		}
		catch(\Exception $e)
		{
			Yii::$app->session->setFlash('info',$e->getMessage());
		}
		$this->redirect(['category/list']);
	}
}