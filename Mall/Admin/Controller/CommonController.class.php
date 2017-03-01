<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;

class CommonController extends Controller {
    public function index(){

        
        // $this->display();
    }

    //当任何函数加载时候  会调用此函数
    public function _initialize(){
	    $uid = session('id');
        if($uid!=3){//id=3为超级管理员
            if(empty($uid)){
                echo '<script>alert("没有登陆");parent.location.href="'.U('login/login').'"</script>';
            }
            $AUTH = new \Think\Auth();
    	    if(!$AUTH->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME, session('id'))){
            	echo '<script>location.href="'.U('login/check_error').'"</script>';
    	    }
        }


    }
}