<?php
use think\Route;

//录入-首页
Route::get('company', 						'company/index/index');

//用户
Route::get('user/login', 					'company/Users/user_login_form');
Route::post('user/login_submit', 			'company/Users/user_login_form_submit');
Route::get('user/logout', 					'company/Users/user_logout');

//公司	
Route::get('basic/edit', 					'company/company/company_edit_form');
Route::post('basic/form_submit', 			'company/company/form_submit');

//企业资质
Route::get('qualification/list', 			'company/CompanyQualification/lists');
Route::get('qualification/info', 			'company/CompanyQualification/info');
Route::get('qualification/add', 			'company/CompanyQualification/add_form');
Route::get('qualification/edit', 			'company/CompanyQualification/edit_form');
Route::get('qualification/delete', 			'company/CompanyQualification/delete');
Route::post('qualification/form_submit', 	'company/CompanyQualification/form_submit');

//企业荣誉
Route::get('honor/list', 					'company/CompanyHonor/lists');
Route::get('honor/info', 					'company/CompanyHonor/info');
Route::get('honor/add', 					'company/CompanyHonor/add_form');
Route::get('honor/edit', 					'company/CompanyHonor/edit_form');
Route::get('honor/delete', 					'company/CompanyHonor/delete');
Route::post('honor/form_submit', 			'company/CompanyHonor/form_submit');

//企业标签
Route::get('tag/edit', 						'company/CompanyTag/edit_form');
Route::post('tag/edit_form_submit', 		'company/CompanyTag/edit_form_submit');

//企业荣誉
Route::get('gallery/list', 					'company/CompanyGallery/lists');
Route::get('gallery/info', 					'company/CompanyGallery/info');
Route::get('gallery/add', 					'company/CompanyGallery/add_form');
Route::get('gallery/edit', 					'company/CompanyGallery/edit_form');
Route::get('gallery/delete', 				'company/CompanyGallery/delete');
Route::post('gallery/form_submit', 			'company/CompanyGallery/form_submit');

//产品
Route::get('product/list', 					'company/Product/lists');
Route::get('product/info', 					'company/Product/info');
Route::get('product/add', 					'company/Product/add_form');
Route::get('product/edit', 					'company/Product/edit_form');
Route::get('product/delete', 				'company/Product/delete');
Route::post('product/form_submit', 			'company/Product/form_submit');

//商标
Route::get('brand/list', 					'company/CompanyBrand/lists');
Route::get('brand/info', 					'company/CompanyBrand/info');
Route::get('brand/add', 					'company/CompanyBrand/add_form');
Route::get('brand/edit', 					'company/CompanyBrand/edit_form');
Route::get('brand/delete', 					'company/CompanyBrand/delete');
Route::post('brand/form_submit', 			'company/CompanyBrand/form_submit');
