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

class FixController extends CommonController
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
                $operator = Yii::$app->session['user']['username'];
                $fix_arr = array();
                foreach ($rows as $k=>$row) 
                {   
                    if(is_null($row[$uuid_column]) || is_null($row[$clickid_column])){
                        continue;
                    }
                    $fix_arr[$row[$clickid_column]] = $row[$uuid_column];
                }
                
                $ex_c = array();
                $deadline = time()-86400*10;
                $clickIds = "'".implode("','",array_keys($fix_arr))."'";
                $r = Fix::findBySql('select distinct p3 from mob_install_log where "timestamp" > '.$deadline.' and p3 in ('.$clickIds.')');
                if($r && ($res = $r->asArray()->all())){
                    foreach ($res as $k => $v) {
                        $ex_c[$v['p3']] = $v['p3'];
                    }
                }
                foreach($fix_arr as $c=>$u){
                    if(isset($ex_c[$c])){
                        $result[] = array($u,$c,'0','已存在');
//                        unset($fix_arr[$c]);
                        continue;
                    }
                    if($deadline >= hexdec(substr($c, 0, 8))){ //不补超过10天的click id
                        $result[] = array($u,$c,'0','已超过10天');
//                        unset($fix_arr[$c]);
                        continue;
                    }
                    $content = @file_get_contents(str_replace(array('{{uuid}}','{{clickid}}','{{operator}}'), array($u,$c,$operator), $pb));
                    $result[] = array($u,$c,'1',$content);
                }
                $model->unlink_file();
                if(isset($result)){
                    $this->exportCsv($result, array('uuid','click id','res','note'), '补数据Log['.  date("Ymd_His").']');
                    exit;
                }
                Yii::$app->getSession()->setFlash("错误提示!",'补数据操作失败');
            }else{
                Yii::$app->getSession()->setFlash("错误提示","上传文件失败");
            }
        }
        return $this->render('index',['model'=>$model]);
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