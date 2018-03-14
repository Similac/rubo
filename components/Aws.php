<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/14/014
 * Time: 13:03
 */

namespace app\components;



use Aws\Sdk;
use yii\base\Component;

class Aws extends Component
{
    public $accessKeyId;
    public $secretAccessKey;
    public $region = 'us-east-1';
    public $version = 'latest';


    /**
     * @var Sdk
     */
    protected $client = null;


    public function init()
    {
        parent::init();

        $option['region'] = $this->region;
        $option['version'] = $this->version;
        $option['credentials'] = [
            'key' => $this->accessKeyId,
            'secret' => $this->secretAccessKey,
        ];
        $this->client = new Sdk($option);
    }

    public function getClient(){
        return $this->client;
    }

    public function getName(){
        return "good";
    }

}