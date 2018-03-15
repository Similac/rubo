<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/13/013
 * Time: 18:38
 */

namespace app\models;


use yii\base\Model;

class S3FIle extends Model
{
    public $date;
    public $key;
    public $size;
    public $url;

    public $bucket = 'mob-export-log-support';


}