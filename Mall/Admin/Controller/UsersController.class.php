<?php
namespace Admin\Controller;
use Think\Controller;


// class UsersController extends CommonController {
class UsersController extends Controller {
    public function index(){

        
        $this->display();
    }

    //点击ztree分类,显示对应商品列表
    public function ztree($id=0){
    	$m=M('admin_node');
    	$ar=M('auth_rule');
    	
        $zNode=Array('0'=>Array('id'=>'0','pid'=>'0','name'=>'全部','open'=>'true'));
        $zNode2=$m->field('id,pid,name')->select();
        for($i=0;$i<count($zNode2);$i++) {
        	$zNode[$i+1]=$zNode2[$i];
        }
        $zNode=json_encode($zNode);
        $this->assign('zNodelist',$zNode);

        $ty=$m->field('name')->find($id);
        $ty=$ty['name'];
        $this->assign('ty',$ty);

        $data=$ar->where(array('an_id'=>$id))->select();
        $this->assign('data',$data);
        echo $this->fetch('admin_permission');
    }

    public function admin_permission(){
    	$m=M('admin_node');
        //{'id:0, pid:0, name:"根 Root", open:true'};
        $zNode=Array('0'=>Array('id'=>'0','pid'=>'0','name'=>'全部','open'=>'true'));
        $zNode2=$m->field('id,pid,name')->select();
        for($i=0;$i<count($zNode2);$i++) {
        	$zNode[$i+1]=$zNode2[$i];
        }
        $zNode=json_encode($zNode);
        // print_r($zNode);
        $this->assign('zNodelist',$zNode);

    	$m=M('auth_rule');
        $data=$m->select();
        $this->assign('data',$data);

        $this->display();
    }
    public function tree_add(){
    	$m=M('admin_node');
    	$data['pid']=$_POST['npid'];
    	$res=$m->add($data);
    	echo $res;
    }
    public function tree_rename(){
    	$m=M('admin_node');
    	$data['id']=$_POST['rid'];
    	$data['pid']=$_POST['rpid'];
    	$data['name']=$_POST['rname'];
    	$data['weight']=$_POST['rid'];
    	$res=$m->save($data);
    	echo $res;
    }
    public function tree_rename2(){
    	$m=M('admin_node');
    	$data['id']=$_POST['tid'];
    	$data['name']=$_POST['tname'];
    	$res=$m->save($data);
    	echo $res;
    }
    //删除数据库分类
    public function tree_remove(){
        $m=M('admin_node');
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
    public function admin_permission_add(){
    	$m=M('admin_node');
    	$r=M('auth_rule');

    	$role0=$m->where(array('pid' =>0))->field(array('id','name'))->select();
    	$r_arr=array();
    	foreach ($role0 as $k => $v) {
    		$role0[$k]['child']=$m->where(array('pid' =>$role0[$k]['id']))->select();
    		foreach ($role0[$k]['child'] as $k1 => $v1) {
	    		$r_arr=explode(',',$role0[$k]['child'][$k1]['rids']);
	    		foreach ($r_arr as $k2 => $v2) {
		    		$role0[$k]['child'][$k1]['rule'][]=$r->where(array('id' =>$r_arr[$k2]))->find();
	    		}
    		}
    	}
        $this->assign('role0',$role0);
        $this->assign('role1',$role1);
        $this->display();
    }
    //添加权限到数据库
    public function admin_permission_add_data(){
        $data['title']=$_POST['roleName'];
        $data['name']=$_POST['roleContent'];
        $data['an_id']=$_POST['user_Character'];
        $data['type']=1;
        $data['status']=1;
        $m=M('auth_rule');
        $res=$m->add($data);

        if(false === $res){
            echo $m->getDbError();
        }else{
        	$m2=M('admin_node');
        	$rids=$m2->where(array('id'=>$data['an_id']))->getField('rids');
        	if ($rids) {
	        	$rids=$rids.",$res";
        	}else{
	        	$rids=$rids."$res";
        	}
        	$data2['rids']=$rids;
        	$data2['id']=$data['an_id'];
        	$re=$m2->save($data2);
        	if (false === $re) {
        		echo $m2->getDbError();
        	}else{
				echo 1;
        	}
			// $this->success("成功","admin_permission",2);
			
       	}
       
    }
    public function permission_sort(){
    	$m=M('admin_node');
    	$r=M('auth_rule');

    	$role0=$m->where(array('pid' =>0))->field(array('id','name','weight'))->order('weight')->select();
    	$r_arr=array();
    	foreach ($role0 as $k => $v) {
    		$role0[$k]['child']=$m->where(array('pid' =>$role0[$k]['id']))->order('weight')->select();
    		foreach ($role0[$k]['child'] as $k1 => $v1) {
	    		$r_arr=explode(',',$role0[$k]['child'][$k1]['rids']);
	    		foreach ($r_arr as $k2 => $v2) {
		    		$role0[$k]['child'][$k1]['rule'][]=$r->where(array('id' =>$r_arr[$k2]))->find();
	    		}
    		}
    	}
        $this->assign('role0',$role0);
        $this->assign('role1',$role1);
 		$this->display();
 	}
 	public function sort_save(){
 		$name1=I('post.name1');
 		$name2=I('post.name2');
 		$name3=I('post.name3');
 		$arrnid1=I('post.arrnid1');
 		$arrnid2=I('post.arrnid2');

		$m=M('admin_node');
		$arrnid1=explode(',', $arrnid1);
		$arrnid2=explode(',', $arrnid2);
		$arr1=explode(',', $name1);
		$arr2=explode(',', $name2);
		for($i=0;$i<count($arrnid1);$i++){
			$data1['weight']=$arr1[$i];
			$m->where(array('id'=>$arrnid1[$i]))->save($data1);
		}
		for($j=0;$j<count($arrnid2);$j++){
			$data2['weight']=$arr2[$j];
			$m->where(array('id'=>$arrnid2[$j]))->save($data2);
		}

 		$tid=explode(' ', $name3);
 		$a1=$tid[1];
		foreach ($tid as $k => $v) {
			$cc[$k]=explode(':',$tid[$k]);
			$data['rids']=$cc[$k][1];
			$m->where(array('id'=>$cc[$k][0]))->save($data);
		}
 		// echo json_encode($name3);
 		echo json_encode($arrp1[0]);
 		// echo json_encode($arr2[0]);
 	}
 	public function admin_role_add_data(){
 		$m=M('admin_node');
 		$data['name']=$_POST['roleName'];
		$res=$m->where(array('id'=>1))->save($data);
 		// echo "<script>var index = parent.layer.getFrameIndex(window.name);
 		// 	parent.$('#btn-refresh').click();
 		// 	parent.layer.close(index);
			// </script>";
		// $this->redirect('admin_role');
		// $this->success('cg','admin_role');
 	}

	public function admin_role(){
		$m=M('admin_node');
		$res=$m->where(array('id'=>1))->find();
		$this->assign('data',$res);
 		$this->display();
 	}



}
