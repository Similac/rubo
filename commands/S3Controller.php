<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/7/007
 * Time: 17:48
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use Aws;


class S3Controller extends Controller
{

    public function actionIndex(){


        $sdk = new Aws\Sdk();
        $s3 = $sdk->createS3([
            'Bucket' => 'mob-export-log-support',
            'Key' => '',
            'region'=>'us-west-2',
            'version'=>'2006-03-01',
        ]);

        $s3->listBuckets(['Bucket' => 'mob-export-log-support']);
        

    }

}