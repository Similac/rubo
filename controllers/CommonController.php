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
        }elseif('site' != substr($route, 0, 4)){
            if(!in_array($route, $this->getPermissions())){
                \Yii::$app->session->set('user',null);
                echo "You don't have permission to access this page,Please contact tech support.";
                exit;
            }
        }
        return true;
    }
    
    public function getPermissions(){
        return (array) \Yii::$app->session['user']['permissions']->all;
    }
    
    
}
