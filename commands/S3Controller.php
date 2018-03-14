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

    protected $bucket = 'mob-export-log-support';

    public function actionIndex(){


        $sdk = new Aws\Sdk();
        $s3 = $sdk->createS3([
            'Bucket' => 'mob-export-log-support',
            'endpoint'=>'https://s3-external-1.amazonaws.com',
            'region'=>'us-east-1',
            'version'=>'latest',
            'key' => 'AKIAILWFAYXTE72526SA',
            'secret' => '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',
        ]);

        $path = 'test/log/run-tests.py';
       // echo $s3->getObjectUrl( $this->bucket,$path);

        $args = [
            'Bucket' => $this->bucket,
            'Key' => $path,
            'ResponseContentType' => 'application/octet-stream'
        ];

        $cmd = $s3->getCommand('GetObject',$args);
        //echo $s3->createPresignedRequest($cmd,'+1 minutes')->getUri()."\n";

        $objects = ($s3->listObjects([
            'Bucket' => $this->bucket,
            'Prefix' => 'test/log/1087531/',
        ]));

        var_dump($objects['Contents']);

    }

}