<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Fix;
use moonland\phpexcel\Excel;
use Yii;
use yii\web\Response;
use yii\data\Pagination;
use yii\bootstrap\ActiveForm;
use ZipArchive;
use yii\helpers\Url;
use yii\web\UploadedFile;
class FixController extends Controller
{
    //上传文件路径
    const FILE_PATH=__DIR__.'/../web/uploads/';
    
    public function actions()
    {
            return [
                    'error' => [
            'class' => 'yii\web\ErrorAction',
        ],
            ];
    }



    public function actionIndex()
    {
        $model= new Fix();         


        print_r($model);exit;

        
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            
        }

        return $this->render('index',['model'=>$model]);

    }
    
    
    
    
    
    
}