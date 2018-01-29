<?php
namespace app\modules\models;

use yii\db\ActiveRecord;
use Yii;

class Admin extends ActiveRecord
{
	public $rememberMe=true;
	public $repass;

	public static function tableName()
	{
		return "{{%admin}}";
	}

	public function rules()
	{
		return [
			// ['adminuser','required','message'=>'管理员账号不能为空','on'=>['login','seekpass']],
			// ['adminpass','required','message'=>'管理员密码不能为空','on'=>'login'],
			// ['rememberMe','boolean'],
			// ['adminpass','validatePass','on'=>'login'],
			// ['adminemail','required','message'=>'管理员邮箱不能为空'],
			// ['adminemail','email','message'=>'邮箱格式不正确'],
			// ['adminemail','validateEmail','on'=>'seekpass'],
			[['adminuser','adminpass'],'required','on'=>['login','changemail']],
			['adminpass','validatePass','on'=>['login','changemail']],
			[['adminuser','adminemail'],'required','on'=>['seekpass','adminadd','changemail']],
			['adminemail','email','on'=>['seekpass','adminadd','changemail']],
			['adminemail','validateEmail','on'=>'seekpass'],
			[['adminpass','repass'],'required','on'=>['changepass','adminadd']],
			['repass','compare','compareAttribute'=>'adminpass','on'=>['changepass','adminadd']],
			['adminuser','unique','on'=>'adminadd'],
			['adminemail','unique','on'=>'changemail'],
		];
	}

	public function attributeLabels()
	{
		return [
			'adminuser'=>'管理员账号',
			'adminpass'=>'管理员密码',
			'adminemail'=>'管理员邮箱',
			'repass'=>'确认密码',
		];
	}

	//验证邮箱
	public function validateEmail()
	{
		if(!$this->hasErrors())
		{
			$data=self::find()->where('adminuser= :user and adminemail= :email',[':user'=>$this->adminuser,':email'=>$this->adminemail])->one();
			if(is_null($data))
			{
				$this->addError('adminemail','管理员账号和邮箱不匹配');
			}
		}
	}

	//验证密码
	public function validatePass()
	{
		if(!$this->hasErrors())
		{
			$data= self::find()->where('adminuser = :user and adminpass= :pass',[':user'=>$this->adminuser,':pass'=>md5($this->adminpass)])->one();
			if(is_null($data))
			{
				$this->addError('adminpass','账号或密码错误');
			}
		}
	}

	public function login($data)
	{	
		$lifetime= $this->rememberMe ? 24*3600 :0;
		//1.开启session
		$session= Yii::$app->session;
		//2.设置cookie时间
		session_set_cookie_params($lifetime);
		//3.存session
		$session['admin']=[
			'adminuser'=>$this->adminuser,
			'isLogin'=>1,
		];
		//4.更新登录时间和ip
		$this->updateAll(['logintime' => time(),], 'adminuser = :user', [':user' => $this->adminuser]);
		return (bool)$session['admin']['isLogin'];
	}

	public function seekpass($data)
	{
		$time=time();
		$token=$this->createToken($data['Admin']['adminuser'],$time);
		$mailer=Yii::$app->mailer->compose('seekpass',['adminuser'=>$data['Admin']['adminuser'],'token'=>$token,'time'=>$time]);
		$mailer->setFrom('13535413258@163.com');
		$mailer->setTo($data['Admin']['adminemail']);
		$mailer->setSubject('测试over');
		if($mailer->send())
		{
			return true;
		}
	}

	public function createToken($a,$b)
	{
		return md5(md5($a).md5($b));
	}

	//修改密码
	public function changepass($data)
	{
		if($this->updateAll(['adminpass'=>md5($this->adminpass)],'adminuser= :user',[':user' => $this->adminuser]))
		{
			return true;
		}
	}

	public function useradd($data)
	{	
		$this->adminuser=$data['Admin']['adminuser'];
		$this->adminpass=md5($data['Admin']['adminpass']);
		$this->adminemail=$data['Admin']['adminemail'];
		$this->createtime=time();
		if($this->save(false))
		{
			return true;
		}
		return false;
	}

	//修改管理员邮箱
	public function changemail($data)
	{
		if($this->updateAll(['adminemail'=>$this->adminemail],'adminuser=:user',[':user'=>$this->adminuser]))
		{
			return true;
		}
		return false;
	}
}