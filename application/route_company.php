<?php
use think\Route;

//录入-首页
Route::get('company', 			'company/index/index');

//用户
Route::get('user/login', 			'company/Users/user_login_form');
Route::post('user/login_submit', 	'company/Users/user_login_form_submit');
Route::get('user/logout', 			'company/Users/user_logout');

//公司
Route::get('company/edit', 			'company/company/company_edit_form');
Route::post('company/form_submit', 	'company/company/form_submit');

//企业资质
Route::get('qualification/list', 			'company/CompanyQualification/lists');
Route::get('qualification/info', 			'company/CompanyQualification/info');
Route::get('qualification/add', 			'company/CompanyQualification/add_form');
Route::get('qualification/edit', 			'company/CompanyQualification/edit_form');
Route::get('qualification/delete', 			'company/CompanyQualification/delete');
Route::post('qualification/form_submit', 	'company/CompanyQualification/form_submit');

//产品
Route::get('product/list', 			'company/Product/lists');
Route::get('product/info', 			'company/Product/info');
Route::get('product/add', 			'company/Product/add_form');
Route::get('product/edit', 			'company/Product/edit_form');
Route::get('product/delete', 		'company/Product/delete');
Route::post('product/form_submit', 	'company/Product/form_submit');

