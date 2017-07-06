<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

//定义后台css,js目录
$base_path = $_SERVER['SERVER_NAME'];

if(strstr($base_path, 'localhost')){
	$base_path = 'http://localhost/www.guizhou.xin/public/';
}

define('STATIC_SOURCE_PATH',  $base_path.'static/');

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
