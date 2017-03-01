<?php
	$ename='d份';
	$gender='男';
	$salary='3200';
	$birthday='2012-04-03';
	$isMarried='false';
	$deptNo='50';

	//连接数据库
	$conn=mysqli_connect('127.0.0.1','root','1111','sheng');
	//告诉数据库，接下来的sql语句编码方式，只有mysql+php才这样
	mysqli_query($conn,'set names utf8');
	//执行语句
	$sql="insert into emp(ename,gender,salary,birthday,isMarried,deptNo) values('$ename','$gender','$salary','$birthday','$isMarried','$deptNo')";
	echo $sql;
	//返回结果集
	$result=mysqli_query($conn, $sql);
	$msg='';
	if($result){
		$msg='添加记录成功！新员工自增编号为：'.mysqli_insert_id($conn);
	}else{
		$msg='添加记录失败！错误编码为：'.mysqli_errno($conn);
	}

	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>button</title>
    <style type="text/css">
        
    </style>
</head>
<body>
    结果：
<?php 
    echo $msg;
?>
    <script>
     	  
         
    </script>
</body>
	