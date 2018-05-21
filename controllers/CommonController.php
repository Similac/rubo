<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\Url;

/**
 * Description of CommonController
 *
 * @author poonleo
 */
class CommonController extends Controller{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if($this->verifyPermission($action) == true){
                return true;
            }
        }
        return false;
    }
    
    
    private function verifyPermission($action){
        $route = $this->route;
        // 检查是否已经登录
        if(!\Yii::$app->session->get('user')){
            $this->redirect(Url::toRoute('site/index'));
            return false;
        }else{
            
            if(\Yii::$app->session['user']['username'] == 'leopoon'){
                var_dump(\Yii::$app->session['user']['permissions']);exit;
            }
            
            return true;
            
        }
    }
    
}
