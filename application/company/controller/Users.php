<?php
namespace app\company\controller;

use think\Controller;

use app\company\controller\Base; 
use \think\Db;

class Users extends Controller
{
    
    /**
     *
     *会员列表
     *
     */
    public function user_list()
    {    
    	 return 'list';
    }

    /**
    *	用户登录
    */
    public function user_login_form(){
       return view('user_login');
    }

    /**
    *	用户登录提交处理
    */
    public function user_login_form_submit(){

      $username = input("post.user_name");
      $password = input("post.password");


      $adminUserModel = Db::table('cx_company_user')->where('username', $username)->find();

      if(!$adminUserModel){
            $res = array('result'=>false,'code'=>1,'message'=>'没有此用户');
      }else if(md5(trim($password)) != $adminUserModel['password']){
            $res = array('result'=>false,'code'=>1,'message'=>'密码错误');
      }else{
            $adminUserModel =  json_encode($adminUserModel);
            session('company_user', $adminUserModel);  // 设置session
            $res = array('result'=>true,'code'=>0,'message'=>'登录成功');
      }
      return $res;
    }
     

    /**
    *	用户登出
    */
    public function user_logout(){
    	session('company_user', null);
    	$this->success('退出登录成功！', '@user/login');     
    }
}


