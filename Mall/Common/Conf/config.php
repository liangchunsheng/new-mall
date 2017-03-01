<?php
return array(
	'URL_CASE_INSENSITIVE' =>true,          //URL不区分大小写
		'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
		//URL模式 ，默认PATHINFO
		'URL_MODEL'=>2,  //重写模式，地址忽略掉index.php的入口文件
		//自定义的
		'ALLOW_ACTIONS'=>array(
				u('Index/index'),
				'sg',
		),
		// 允许模块访问
		'MODULE_ALLOW_LIST'=>array('Home','Admin'),
		// 设置默认的加载模块
		'DEFAULT_MODULE'=>'Home',
		
		//默认模板主题
		'DEFAULT_THEME'=>'default',
		//定界符
		'TMPL_L_DELIM'=>'<{',
		'TMPL_R_DELIM'=>'}>',
		
		//页面Trace
		// 'SHOW_PAGE_TRACE'=>true,
		
		//全局定义PDO
		'DB_TYPE' => 'mysql',
		'DB_HOST' => '127.0.0.1',
		'DB_USER' => 'root',
		'DB_PWD' => 'root',
		'DB_NAME' => 'mall',
		'DB_PORT' => 3306,                   //默认就是，可以不写
		// 'DB_PREFIX' => 'm_',
		'DB_CHARSET'=> 'utf8',            // 字符集，默认就是，可不写
		// 	'DB_DSN'=>'mysql:host=localhost;dbname=thinkphp;charset=UTF8',
		'DB_DEBUG'  =>  TRUE,           // 数据库调试模式 开启后可以记录SQL日志
		// 		'DB_PARAMS' =>  array(),         // 数据库连接参数
		//		'DB_PARAMS' => array(PDO::ATTR_PERSISTENT => true),     //长连接);

);