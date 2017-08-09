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
Route::get('brand/detail', 		'index/Brand/info');

//产品
Route::get('product', 			'index/product/index');
Route::get('product_info', 		'index/product/info');
Route::get('product/ask', 		'index/product/ask');

//景区
Route::get('region', 			'index/region/index');
Route::get('region/lists', 		'index/region/lists');
Route::get('region/detail', 	'index/region/info');

//企业
Route::rule('enterprise', 		'index/enterprise/index');
Route::get('enterprise/info', 	'index/enterprise/info');

//商标馆
Route::rule('trademark', 		'index/trademark/index');
Route::get('trademark/detail', 	'index/trademark/info');

//首页
Route::get('news/:id', 				'index/news');

//诚信贵州后台
include_once('route_admin.php');

//企业后台
include_once('route_company.php');

