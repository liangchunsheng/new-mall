<?php
	//遍历所在文件夹及其下面的文件名目录
		$d = dir(dirname(__FILE__));
		print_r ($d);
		echo '<br>';
		echo  ("Handle: " . $d->handle);
		echo '<br>';
		echo ("Path: " . $d->path);
		echo '<br>';
		while ( false !== ($entry = $d->read ()) ) {
			echo $entry . "".'<br>';
		}
		$d->close ();
		
		echo '<br>';	
		echo dirname(__FILE__).'<br>';
		echo basename(__FILE__).'<br>';
		// echo phpinfo();
//-------------------------------------------------

//找出在一个字符串中出现最多的字符
$strList = str_split('rewruoewjrewmhcywerbggfaafdsrew');
//字符串变数组，一索引一个
$strCount = array_count_values($strList);
rsort($strCount);
echo $strCount[0];