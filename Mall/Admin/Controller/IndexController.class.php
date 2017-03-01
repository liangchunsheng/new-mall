<?php
namespace Admin\Controller;
use Think\Controller;
use Home\Event\LoginEvent;

class IndexController extends Controller {
    public function index(){
        $lev=new LoginEvent;
        $lev->autoLogin();
        $this->display();
    }

    public function welcome(){

        
        $this->display();
    }

    public function article_list(){

        
        $this->display();
    }

    public function picture_list(){
                
        $this->display();
    }

    public function product_brand(){

        
        $this->display();
    }
   
    
    public function bfi(){

        
        $this->display();
    }
    public function bfi2(){

        
        $this->display();
    }
    public function upload(){
        $upload=new \Think\Upload();
        $upload->maxSize = 31245728;   //上传大小，3M
        $upload->exts = array('jpg', 'png','gif','jpeg');
        $upload->rootPath = './Mall/Public/static/image/';
        // $upload->savePath = '';
        $info =$upload->upload();
        if(!$info){
            echo json_encode($this->error($upload->getError()));
        }else{
            // echo json_encode($upload->rootPath.'fef安抚.jpg');
            echo json_encode(array('gg'=>'ff'));
            // $this->success('上传成功');
        }
        
    }
    public function feedback_list(){

        
        $this->display();
    }

    public function system_base(){

        
        $this->display();
    }

    public function system_category(){

        
        $this->display();
    }

    public function system_data(){

        
        $this->display();
    }

    public function system_shielding(){

        
        $this->display();
    }

    public function system_log(){

        
        $this->display();
    }
}