<?php
namespace Admin\Controller;
use Think\Controller;

class GoodController extends Controller {
    public function index(){
        /*$gid=12;
        */
        $f=M('goods_file');
        $g=M('goods');
        // $res=$f->field('count(*) as c')->where(array('gid'=>12))->group('gid')->select();
        // echo($res[0]['c']);//3
        //$re=$f->field('id,filepath')->where(array('gid'=>$gid))->select();
        //print_r($re);
        // $this->success('上传成功');
          // $this->success('上传成功',"good/product_list",2);
          $this->error('失败尽快解决',"good/product_list",2);
        
        // $this->display();
    }
    public function upload(){
        $m=M('goods_file');
        $upload=new \Think\Upload();
        $upload->maxSize = 31245728;   //上传大小上限，3M
        $upload->exts = array('jpg', 'png','gif','jpeg');
        $upload->rootPath = './Mall/Public/static/image/';
        // $upload->savePath = '';
        $info =array_reverse($upload->upload());
        if(!$info){
            echo json_encode($this->error($upload->getError()));
        }else{
            //echo json_encode($upload->rootPath.'fef安抚.jpg');
            //echo json_encode(array('f'=>$_FILES['name']));
            foreach($info as $file){
                $data['filepath']=date('Y-m-d').'/'.$file['savename'];
                $res=$m->add($data);
                echo json_encode(array(
                    'imagepath'=>STATIC_URL.'image/'.$data['filepath'],
                    'id'=>$res,
                )); 
            }
            // $this->success('上传成功');
        }
    }
    public function product_del_image(){
        $id=$_GET['id'];
        $m=M('goods_file');
        // $re=$m->where(array('id'=>$id))->getField('filepath');
        // unlink('./Mall/Public/static/image/'.$re);//删除选定的本地图片
        // $res=$m->delete($id);
        $res=1;
        if ($res) {
            echo json_encode($res);
        }else{
            echo json_encode($res);
        }
    }
    //产品管理
    public function product_list(){
        $m=M('goods_type');
        $zNode=$m->field('id,pid,name')->select();
        $zNode=json_encode($zNode);
        $this->assign('zNodelist',$zNode);

        $this->display();
    }
    //点击ztree分类、面包屑，显示对应商品列表
    public function tree_list($id=0){
        $m=M('goods_type');
        $g=M('goods');
        $zNode=$m->field('id,pid,name')->select();
        
        $zNode=json_encode($zNode);
        $this->assign('zNodelist',$zNode);

        $Model = new \Think\Model(); 
        $res=$Model->query("select a.id,a.curprice,a.goodsname,a.coverimg,b.filepath,a.text,a.unit,a.status,a.attributes from goods a,goods_file b where a.coverimg=b.id and a.tid=$id");

        $arr=array();
        // $ty=$m->where(array('id'=>$id))->getfield('name');
        $ty=$m->where(array('id'=>$id))->field('pid,name')->select();
        $ty[0]['id']=$id;
        $arr[]=$ty[0];
        while($ty[0]['pid']!=0){
            $t=$ty[0]['pid'];
            $ty=$m->where(array('id'=>$ty[0]['pid']))->field('pid,name')->select();
            $ty[0]['id']=$t;
            $arr[]=$ty[0];
        }
        $arr=array_reverse($arr);
        // var_dump($arr);

        $this->assign('ty',$arr);
        $this->assign('data3',$res);
        echo $this->fetch('product_list');
        // echo json_encode($res);
        // echo $co=$this->fetch('home@index:index');   //渲染前台模板
    }
    //添加商品
    public function product_add(){
        $m=M('goods_type');
        $data=$m->order('path')->select();
        // print_r($data);
        foreach ($data as $k => $v) {
            $data[$k]['name']=str_repeat('|----',$v['level']).$v['name'];
        }
        $this->assign('data',$data);
        $this->display();
    }
    //接收添加商品的属性，存入数据库
    public function goods_add(){
        $data['goodsname']=$_POST['goodsname'];
        $tid=explode(',', $_POST['tid']);
        $data['tid']=$tid[0];
        $tids=$tid[0];
        $data['tpid']=$tid[1];
        $data['unit']=$_POST['unit'];
        $data['attributes']=$_POST['abRadio'];
        $data['number']=$_POST['number'];
        $data['barcode']=$_POST['barcode'];
        $data['curprice']=$_POST['curprice'];
        $data['oriprice']=$_POST['oriprice'];
        $data['cosprice']=$_POST['cosprice'];
        $data['freight']=$_POST['freight'];
        $data['inventory']=$_POST['inventory'];
        $data['restrict']=$_POST['restrict'];
        $data['already']=$_POST['already'];
        $data['status']=$_POST['status'];
        $data['reorder']=$_POST['reorder'];
        $data['text']=$_POST['textDes'];
        
        $imgarr=$_POST['imagepath'];
        $imgarr=explode(',',$imgarr);
        // print_r($imgarr);
        $data['coverimg']=$imgarr[0];
        $m=M('goods');
        if (count($imgarr)>1) {
            $f=M('goods_file');
            $arr=array();
            foreach ($imgarr as $k=>$v) {
                if ($k!=0) {
                    $arr[]=$v;
                }
            }
            $data['imgs']=implode(',',$arr);
            // print_r($data);
            $res=$m->add($data);
            if($res){
                $arr2=array();
                foreach ($imgarr as $k=>$v) {
                    $arr2['id']=$v;
                    $arr2['isuse']=1;
                    $f->save($arr2);
                }
                echo "<script>alert('添加成功');location.href='tree_list/id/".$tids."'</script>";
            }else{
                echo '<script>alert("添加失败，因为");history.go(-1);</script>';
            }
        }elseif(count($imgarr)==1){
            $res=$m->add($data);
            if($res){
                echo "<script>alert('添加成功');location.href='tree_list/id/".$tids."'</script>";
            }else{
                echo '<script>alert("添加失败，因为");history.go(-1);</script>';
            }
        }else{
            echo '<script>alert("添加失败");history.go(-1);</script>';
        }
}
    public function product_edit_add(){
        $m=M('goods_type');
        $data=$m->order('path')->select();
        foreach ($data as $k => $v) {
            $data[$k]['name']=str_repeat('|----',$v['level']).$v['name'];
        }
        $this->assign('data',$data);

        $g=M('goods');
        $f=M('goods_file');
        $id=$_GET['id'];
        $good=$g->find($id);//只返回一行数据集合
        $coverid=$good['coverimg'];
        $good['filepath']=$f->where(array('id'=>$coverid))->getField('filepath'); //封面图片地址
        if($good['imgs']!=null){
            if(strpos($good['imgs'], ',')===false) {
                $res[0]['id']=$good['imgs'];
                $res[0]['filepath']=$f->where(array('id'=>$good['imgs']))->getField('filepath');
            }else{
                $arr=explode(',',$good['imgs']);
                foreach ($arr as $k => $v) {
                    $res[$k]['id']=$v;
                    $res[$k]['filepath']=$f->where(array('id'=>$v))->getField('filepath');
                }
            }
            $this->assign('res',$res);
        }
        $this->assign('good',$good);
        $this->display();
    }
    //接收修改后的商品属性，存数据库
    public function goods_save(){
        $data['goodsname']=$_POST['goodsname'];
        $tid=explode(',', $_POST['tid']);
        $data['tid']=$tid[0];
        $data['tpid']=$tid[1];
        $tids=$tid[0];
        $data['unit']=$_POST['unit'];
        $data['attributes']=$_POST['abRadio'];
        $data['number']=$_POST['number'];
        $data['barcode']=$_POST['barcode'];
        $data['curprice']=$_POST['curprice'];
        $data['oriprice']=$_POST['oriprice'];
        $data['cosprice']=$_POST['cosprice'];
        $data['freight']=$_POST['freight'];
        $data['inventory']=$_POST['inventory'];
        $data['restrict']=$_POST['restrict'];
        $data['already']=$_POST['already'];
        $data['status']=$_POST['status'];
        $data['reorder']=$_POST['reorder'];
        $data['text']=$_POST['textDes'];

        //$imgarr=$_POST['imagepath'];//数组   从imagepath[]提交过来的
        $imgarr=$_POST['imagepath'];
        $imgarr=explode(',',$imgarr);
        // print_r($imgarr);
        $data['coverimg']=$imgarr[0];
        $m=M('goods');
        if (count($imgarr)>1) {
            $f=M('goods_file');
            $arr=array();
            foreach ($imgarr as $k=>$v) {
                if ($k!=0) {
                    $arr[]=$v;
                }
            }
            $data['imgs']=implode(',',$arr);
            // print_r($data);
            $res=$m->where(array('id'=>$_POST['id']))->save($data);
            if($res){
                $arr2=array();
                foreach ($imgarr as $k=>$v) {
                    $arr2['id']=$v;
                    $arr2['isuse']=1;
                    $f->save($arr2);
                }
                // echo "<script> parent.layer.alert('更改成功',{icon: 1}, function(index){
                //           parent.location.href='tree_list/id/".$tids."';
                //           layer.close(index);
                //         });  </script>";
                echo "<script> parent.layer.msg('更改成功', {
                          icon: 1,
                          time: 2000
                        }, function(){
                          parent.location.href='tree_list/id/".$tids."';
                        }); </script>";        
                // echo "<script>parent.layer.close(parent.layer.getFrameIndex(window.name));</script>";
                // $this->success('更改成功',"tree_list/id/".$tids."");
            }else{
                echo '<script>alert("更改失败，因为数据没有任何改动");history.go(-1);</script>';
            }
        }else{
            echo '<script>alert("更改失败");history.go(-1);</script>';
        }




    }



}