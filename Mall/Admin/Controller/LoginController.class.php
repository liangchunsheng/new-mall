<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Verify;
use Home\Event\LoginEvent;

class LoginController extends Controller {
    public function index(){
        // $this->display();
    }
    public function login(){

        $this->display();
    }

    public function dologin(){
        $uname=i('post.uname');
        $pwd=i('post.pwd');
        $checked=I('post.checkbox');//字符串布尔
        
        $m=M('admin_user');
        $where=(array(
            'admin_name'=>$uname,
            'admin_pwd'=>$pwd,
        ));
        $id=$m->where($where)->getField('id');

        if ($id) {
            if ($checked=='1'){
                //第二身份标识
                $identifier = md5(salt() . md5($uname . salt()));
                //永久登录标识
                $token=md5(uniqid(mt_rand(),true));
                //过期时间
                $timeout=time()+3600*24*7;
                setCookie('admin_token',"$identifier:$token",$timeout,'/');
// cookie('token',"$identifier:$token",array('prefix'=>'aook_','expire'=>$timeout,));//时间无法设置bug
                $ev=new LoginEvent();
                //保存token到数据库
                $ev->saveToken($id,$identifier,$token,$timeout);
            }
            session('id',$id);
            session('uname',$uname);
            $this->success('登录成功',U('index/index'));
        }else{
            // $this->error($m->getDbError());
            $this->error('登录失败,用户名或密码错误');
        }
    }

    //生成验证码
    public function verify(){
        $config=array(
            'fontSize' => 25, // 验证码字体大小(px)
            'useCurve' => false, // 是否画混淆曲线
            'useNoise' => false, // 是否添加杂点
            'length' => 4, // 验证码位数
            'bg' => array (243,251,254 ), // 背景颜色 ，默认白色
            'expire'    =>  100,            // 验证码过期时间（s）
        );
        $verify = new Verify($config); // 验证码
        $verify->entry ();             //生成
    }

    //ajax检查验证码是否正确
    public function checkVerify(){
        if (!IS_AJAX){
            echo '非法访问';exit;
        }
        $code=I('verifycode');
        $verify=new Verify();
        $bol=$verify->check($code);
        // $this->ajaxReturn(array(
        //     'info'=>$bol,
        // ));
        echo json_encode($bol);
    }
    public function check_error(){
        $this->error("没有权限",U('nopermission'),2);
    }
    public function nopermission(){
        $this->display();
    }
    public function loginout(){
        session(null);
        setcookie('admin_token', '', time()-1,'/');
        $this->redirect('login/login');
    }

}