<?php
use think\Route;
//后台-首页
Route::get('admin', 			'admin/index/index');

//用户
Route::get('admin/user/login', 			'admin/Users/user_login_form');
Route::post('admin/user/login_submit', 	'admin/Users/user_login_form_submit');
Route::get('admin/user/logout', 		'admin/Users/user_logout');

//公司
Route::get('admin/company/list', 			'admin/company/company_list');
Route::get('admin/company/info', 			'admin/company/company_info');
Route::get('admin/company/edit', 			'admin/company/edit_form');
Route::get('admin/company/delete', 			'admin/company/company_delete');
Route::get('admin/company/add', 			'admin/company/add_form');

//分类
Route::get('admin/category/list', 			'admin/category/category_list');
Route::get('admin/category/add', 			'admin/category/add_form');
Route::get('admin/category/edit', 			'admin/category/edit_form');
Route::get('admin/category/delete', 		'admin/category/category_delete');
Route::post('admin/category/form_submit', 	'admin/category/form_submit');


