<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];

use think\Route;

Route::domain('company', 'company');

//首页
Route::get('/', 				'index/index');

//品牌
Route::get('brand', 			'index/Brand/index');

//产品
Route::get('product', 			'index/product/index');
Route::get('product_info', 		'index/product/info');

//景区
Route::get('region', 			'index/region/index');

//企业查询
Route::get('enterprise', 		'index/enterprise/index');
Route::get('enterprise/info', 	'index/enterprise/info');

//商标馆
Route::get('trademark', 		'index/trademark/index');


//诚信贵州后台
include_once('route_admin.php');

//企业后台
include_once('route_company.php');

