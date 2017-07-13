<?php
use think\Route;
//后台-首页
Route::get('admin', 			'admin/index/index');

//用户
Route::get('admin/user/login', 			'admin/Users/user_login_form');
Route::post('admin/user/login_submit', 	'admin/Users/user_login_form_submit');
Route::get('admin/user/logout', 		'admin/Users/user_logout');

//公司
Route::get('admin/company/list', 		'admin/company/company_list');
Route::get('admin/company/info', 		'admin/company/company_info');
Route::get('admin/company/edit', 		'admin/company/company_edit_form');
Route::get('admin/company/delete', 		'admin/company/company_delete');
Route::get('admin/company/add', 		'admin/company/company_add_form');


