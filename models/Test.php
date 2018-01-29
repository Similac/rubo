<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Test extends ActiveRecord{

	public $username;  
    public $pwd;  
    public $re_pwd;  
    public $email;  
    public $bobby;  
    public $remark;  
    public $photo;  
    public $school;  
    public $info;

    public static function getDb()
    {
        return Yii::$app->remote_db;
    }

    public static function tableName()
    {

    	return '{{%mob_install_log}}';
    }

    public function rules()
    {
    	return [
    		//验证不能为空
    		[['username','pwd','email','hobby'],'required','message'=>'{attribute}不能为空'],
    		//验证用户名唯一
    		['username','unique'],
    		//验证密码不一致
    		['re_pwd','compare','compareAttribute'=>'pwd','message'=>'两次密码不一致'],
    		//验证字符串长度
    		[['username','pwd'],'string','max'=>'10','min'=>'5','tooLong'=>'{attribute}不能大于10个字符','tooShort'=>'{attribute}不能小于5个字符'],
    		//验证文件上传的格式  
            ['photo','file',  
            'extensions'=>['jpg','png','gif'],'wrongExtension'=>'只能上传{extensions}类型文件！',
            'maxSize'=>1024*1024*2,  'tooBig'=>'文件上传过大！',
            'skipOnEmpty'=>false,'uploadRequired'=>'请上传文件！',
            'message'=>'上传失败!'
            ],
            ['email','email','message'=>'{attribute}格式错误'],
            ['remark','string','max'=>'20'],
    		
    	];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'pwd'=>'密码',
            're_pwd'=>'重复密码',
            'sex'=>'性别',
            'photo'=>'头像',
            'email'=>'邮箱',
            'hobby'=>'爱好',
            'school'=>'学校',
            'remark'=>'备注信息'
        ];
    }
}