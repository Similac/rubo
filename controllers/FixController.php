<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Fix;
use moonland\phpexcel\Excel;
use Yii;
use yii\web\UploadedFile;
use johnnylei\csv\TheCsv;
use app\models\Advertiser;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
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
        
//        $r = Fix::findBySql('select * from mob_install_log where "timestamp" > 1522252800 limit 50');
//        var_dump($r->asArray()->all());exit;
        
        $pb = 'http://next.mobvista.com/install?mobvista_campuuid={{uuid}}&mobvista_clickid={{clickid}}&mobvista_mark=mobvista_resent&mobvista_operator={{operator}}';
        $model= new Fix();         
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $model->upload_file = $post['Fix']['upload_file'] = UploadedFile::getInstance($model,'upload_file');
            if($model->load($post) && $model->upload()){
                $file_name = $model->upload_file->name;
                $excel = new Excel();
                $rows = $excel->readFile(self::FILE_PATH.$file_name);
                $uuid_column = $post['Fix']['uuid_column'];
                $clickid_column = $post['Fix']['clickid_column'];
                $operator = $post['Fix']['operator'];
                $fix_arr = array();
                foreach ($rows as $k=>$row) 
                {   
                    if(is_null($row[$uuid_column]) || is_null($row[$clickid_column])){
                        continue;
                    }
                    $fix_arr[$row[$clickid_column]] = $row[$uuid_column];
                }
                
                foreach($fix_arr as $c=>$u){
                    $content = @file_get_contents(str_replace(array('{{uuid}}','{{clickid}}','{{operator}}'), array($u,$c,$operator), $pb));
//                    echo $content."<br/>";
                }
                $model->unlink_file();
            }
        }
        return $this->render('index',['model'=>$model]);
    }
    
}