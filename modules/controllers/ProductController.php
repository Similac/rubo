<?php
namespace app\modules\controllers;
use app\modules\controllers\CommonController;
use Yii;
use app\models\Category;
use app\models\Product;

class ProductController extends CommonController
{
	public $layout='layout1';

	public function actionAdd()
	{	
		$model= new Product;
		$cate = new Category;
		$cates=$cate->getOptions();
		unset($cates[0]);

		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			var_dump($post);
		}

		return $this->render('add',['opts'=>$cates,'model'=>$model]);
	}


}