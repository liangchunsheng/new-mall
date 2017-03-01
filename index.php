<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

define('SITE_URL','http://www.mall.com/');
define('SITE2_URL','http://www.mall.com/');
define('CSS_URL','http://www.mall.com/Mall/Public/css/');
define('JS_URL','http://www.mall.com/Mall/Public/js/');
define('IMG_URL','http://www.mall.com/Mall/Public/images/');
define('PIC_URL','http://www.mall.com/Mall/Public/picture/');
define('LIB_URL','http://www.mall.com/Mall/Public/lib/');
define('STATIC_URL','http://www.mall.com/Mall/Public/static/');
define('FILEIMG_URL','http://www.mall.com/Mall/Public/static/image/');
define('TEMP_URL','http://www.mall.com/Mall/Public/temp/');
define('TEMPLATE_URL','http://www.mall.com/Mall/Public/template/');

// 定义应用目录
define('APP_PATH','./Mall/');
header ( 'Content-Type:text/html,charset=UTF-8' );

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';