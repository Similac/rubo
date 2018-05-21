<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use yii;
use yii\web\Controller;
use app\models\User;
use linslin\yii2\curl;

/**
 * Description of SiteController
 *
 * @author poonleo
 */
class SiteController extends Controller{
    
    protected $client_id = 1010; //应用id
    protected $client_secret = '968a4d76a0bdbaf4c07adbb783ad22c4'; //应用密钥

    protected $auth_domain = 'https://accounts.mobvista.com';
    protected $url_authorize = '/authorize';
    protected $url_token = '/token';
    protected $url_userinfo = '/userinfo';
    protected $url_logout = '/logout';
       
    function actionIndex(){
        $this->actionLogin();
    }
    
    function actionLogin(){
        $params = array();
        $params['theme'] = 'as';
        $params['response_type'] = 'code';
        $params['client_id'] = $this->client_id;
        $params['state'] = 'xyz';
        $params['redirect_uri'] = \Yii::$app->request->getHostInfo().yii\helpers\Url::toRoute('site/postback');
        $query = http_build_query($params);
        $loginUrl = $this->auth_domain.$this->url_authorize.'?'.$query;
        $this->redirect($loginUrl);
    }
    
    function actionPostback(){
        $get = \Yii::$app->getRequest()->get();
        if(isset($get['error']) && 'access_denied'==$get['error']){
            echo 'denied login!';
            exit;
        }elseif(!isset($get['code']) && empty($get['code'])){
            echo 'error!';
            exit;
        }
        $token_url = $this->auth_domain.$this->url_token;
        $code = trim($get['code']);
        $state = trim($get['state']);
        $post_data = array();
        $post_data['code'] = $code;
        $post_data['client_id'] = $this->client_id;
        $post_data['client_secret'] = $this->client_secret;
        $post_data['redirect_uri'] = \Yii::$app->request->getHostInfo().yii\helpers\Url::toRoute('site/postback');
        $post_data['grant_type'] = 'authorization_code';
        $curl = new curl\Curl();
        $res = $curl->reset()
            ->setOption(
                CURLOPT_POSTFIELDS, 
                http_build_query($post_data))
            ->post($token_url);
        $res = @json_decode($res,1);
        if(isset($res['access_token']) && !empty($res['access_token'])){
            $params = array();
            $params['access_token'] = $res['access_token'];
            $params['fields'] = 'profiles,permissions';
            $user_info = $curl->reset()->get($this->auth_domain.$this->url_userinfo.'?'.http_build_query($params));
            $user_info = @json_decode($user_info);
            if(empty($user_info->profiles->id)){
                echo 'Auth Error!';
                exit;
            }
            $user = array();
            $user['id'] = $user_info->profiles->id;
            $user['username'] = $user_info->profiles->username;
            $user['real_name'] = $user_info->profiles->real_name;
            $user['email'] = $user_info->profiles->email;
            $user['permissions'] = $user_info->permissions;
            $session = \Yii::$app->session;
            $session->set('user',$user);
            $this->goHome();
        }else{
            echo 'Auth Error! Please try again.';
            exit;
        }
    }
    
    function actionLogout(){
        \Yii::$app->session->set('user',null);
        $params = array();
        $params['client_id'] = $this->client_id;
        $params['redirect_uri'] = \Yii::$app->request->getHostInfo().yii\helpers\Url::toRoute('site/login');
        $query = http_build_query($params);
        $this->redirect($this->auth_domain.$this->url_logout.'?'.$query);
    }
    
    
}
