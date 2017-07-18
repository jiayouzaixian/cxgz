<?php
use think\Route;

//录入-首页
Route::get('company', 			'company/index/index');

//用户
Route::get('company/user/login', 			'company/Users/user_login_form');
Route::post('company/user/login_submit', 	'company/Users/user_login_form_submit');
Route::get('company/user/logout', 			'company/Users/user_logout');

//公司
Route::get('company/company/list', 			'company/company/company_list');
Route::get('company/company/edit', 			'company/company/company_edit_form');
Route::post('company/company/form_submit', 	'company/company/form_submit');