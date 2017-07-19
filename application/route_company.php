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
Route::get('qualification/list', 			'company/CompanyQualification/company_qualification_list');
Route::get('qualification/info', 			'company/CompanyQualification/company_qualification_info');
Route::get('qualification/add', 			'company/CompanyQualification/company_qualification_add_form');
Route::get('qualification/edit', 			'company/CompanyQualification/company_qualification_edit_form');
Route::get('qualification/delete', 			'company/CompanyQualification/company_qualification_delete');
