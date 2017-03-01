<?php
namespace Admin\Controller;
use Think\Controller;

class GoodTypeController extends CommonController {
    public function index(){

        
        $this->display();
    }
     /*public function product_category_ajax(){//不用的
        $m=M('goods_type');
        $zNode=$m->field('id,pid,name')->select();
        echo json_encode($zNode);
        // echo ($zNode);
    }*/
    //分类管理,显示ztree数据
    public function product_category(){
        $m=M('goods_type');
        $zNode=$m->field('id,pid,name')->select();
        $zNode=json_encode($zNode);
        $this->assign('zNode',$zNode);
        $this->display();
    }
    //修改数据库分类
    public function product_category_rename(){
        $m=M('goods_type');
        $data['id']=$_POST['tnid'];
        $data['name']=$_POST['name'];
        $re=$m->save($data);
        echo json_encode($re);
    }
    //删除数据库分类
    public function product_category_remove(){
        $m=M('goods_type');
        $did=$_POST['did'];
        $data=$m->where(array('pid'=>$did))->find();
        // echo json_encode($did);
        if($data){
            $str='分类下面还有子分类，不允许删除';
            echo json_encode($str);
        }else{
            $re=$m->delete($did);
            if($re){
                echo 1;
            }
        }
    }
    //无线分类,显示select
    public function product_category_add(){
        $m=M('goods_type');
        // $data=$m->where(array('id'=>1))->getfield('name');
        // $data=$m->field('*,concat(path,",",id) as paths')->order('paths')->select();
        $data=$m->order('path')->select();
        // print_r($data);
        foreach ($data as $k => $v) {
            $data[$k]['name']=str_repeat('|----',$v['level']).$v['name'];
        }

        $this->assign('data',$data);
        
        $this->display();
    }
    //添加分类信息到数据库
    public function goods_type_add(){
        $data['name']=$_POST['name'];
        $data['pid']=$_POST['pid'];
        
        $m=M('goods_type');
        if($data['name']!='' && $data['pid']!=0){
            $path=$m->field('path')->find($data['pid']);//返回数组
            $data['path']=$path['path'];
            $data['level']=substr_count($data['path'], ',');
            // print_r($data);
            $re=$m->add($data);  //返回插入id

            $path['id']=$re;
            $path['path']=$data['path'].','.$re;
            $path['level']=substr_count($path['path'], ',');
            $res=$m->save($path);

            if($res){
                echo '<script>alert("添加成功");parent.location.href="product_category";</script>';
            }else{
                echo '<script>alert("添加失败");parent.location.href="product_category";</script>';
            }
        }elseif ($data['name']!='' && $data['pid']==0) {
            $data['path']=$data['pid'];
            $data['level']=1;
            $re=$m->add($data);  //返回新增id

            $path['id']=$re;
            $path['path']=$data['path'].','.$re;
            $res=$m->save($path);
            if($res){
                echo '<script>alert("添加成功");parent.location.href="product_category";</script>';
            }else{
                echo '<script>alert("添加失败");parent.location.href="product_category";</script>';
            }
        }else{
            echo '<script>alert("添加失败，内容不能为空");parent.location.href="product_category";</script>';
        }
    }





}