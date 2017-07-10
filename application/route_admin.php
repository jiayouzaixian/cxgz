<?php
use think\Route;
//后台-首页
Route::get('admin', 			'admin/index/index');

//用户
Route::get('admin/user/login', 			'admin/Users/user_login_form');
Route::post('admin/user/login_submit', 	'admin/Users/user_login_form_submit');
Route::get('admin/user/logout', 			'admin/Users/user_logout');



