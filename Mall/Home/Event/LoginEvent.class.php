<?php
namespace Home\Event;

class LoginEvent{
	public function saveToken($id,$identifier,$token,$timeout){
		$m=m('admin_user');
		$where=array(
			'id'=>$id,
		);
		$data['identifier']=$identifier;
		$data['token']=$token;
		$data['timeout']=$timeout;
		
		$res=$m->where($where)->data($data)->save();
		return $res;
	}
	
	//如果用户已经登录，则没事；如果进网页，cookie存在，那么自动登录
	public function autoLogin($cookie = 'admin_token') {
	    if (isset($_SESSION['uname'])) {
	        return;
	    }
	    if (isset($_COOKIE[$cookie])) {
	        $getC = $_COOKIE[$cookie];

	        list($identifier, $token) = explode(':', $getC);
	        $m = M('admin_user');
	        $where = array(
	        	'identifier' => $identifier,
	        );
	        $sel = $m->where($where)->find();

	        if ($sel['token'] != $token) {
	            return;
	        }
	        if (time() < $sel['timeout']) {
	            session('id', $sel['id']);
	            session('uname', $sel['admin_name']);
	            echo '<script>document.location.reload();</script>';
	        }
	    }
	}
	
}