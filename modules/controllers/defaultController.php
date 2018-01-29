<?php
namespace app\modules\controllers;

use yii\web\Controller;
use Yii;
use app\modules\controllers\CommonController;

class DefaultController extends CommonController
{	
	public $layout='layout1';
	public function actionIndex()
	{	
		
		return $this->render('index');
	}
}