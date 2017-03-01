<?php
// namespace Common\Common;//不能写命名空间！！！
use Think\Page;
use Think\Verify;

/* //截取字符串，只需要用 {$vo.title|subtext=10} 这样即可
  function subtext($text, $length){
  if(mb_strlen($text, 'utf8') > $length)
  return mb_substr($text, 0, $length, 'utf8').'...';
  return $text;
  } */

// 验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = '') {
    $verify = new Verify();
    return $verify->check($code, $id);
}

//用户密码格式，是否正确
function checkPwd($str) {
    if (preg_match('[0-9a-zA-Z]{6,20}', $str)) {
        return false;
    } else {
        return true;
    }
}

//生成随机数,用于生成salt
function salt($length = 16) {
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
    $str = '';
    $arr_len = count($arr); //62
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $str .= $arr[$rand];
    }
    return $str;
}

/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $m 模型，引用传递
 * @param $where 查询条件
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage(&$m, $where, $pagesize = 10) {
    $m1 = clone $m; //浅复制一个模型
    $count = $m->where($where)->count(); //连惯操作后会对join等操作进行重置
    $m = $m1; //为保持在为定的连惯操作，浅复制一个模型
    $p = new Page($count, $pagesize);
    $p->lastSuffix = false; // 最后一页是否显示总页数
    $p->setConfig('header', '<span class="rows">共<b>%TOTAL_ROW%</b>条记录    第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</span>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
// 	$p->parameter=I('get.');
// 	print_r ($p->parameter=I('get.'));
    $m->page($_GET['p'], $p->listRows)->select();
// 	$m->limit($p->firstRow,$p->listRows);
    return $p;
}