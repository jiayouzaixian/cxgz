<?php
namespace app\admin\controller;

use think\Controller;

use app\admin\controller\Base; 
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
      $param = array(
          'user_name' => input("post.user_name"),
          'password'  => input("post.password")
      );
      
      // $adminUserModel = Model('AdminUser')->getUser($param['user_name']);
      $adminUserModel = Db::table('cx_admin_user')->where('user_name',$param['user_name'])->find();

      if(!$adminUserModel){
            $res = array('result'=>false,'code'=>1,'message'=>'没有此用户');
      }else if(md5($param['password']) != $adminUserModel['password']){
            $res = array('result'=>false,'code'=>1,'message'=>'密码错误');
      }else{
            $adminUserModel =  json_encode($adminUserModel);
            session('admin_user', $adminUserModel);  // 设置session
            $res = array('result'=>true,'code'=>0,'message'=>'登录成功');
      }
      return $res;
    }
     

    /**
    *	用户登出
    */
    public function user_logout(){
    	session('admin_user', null);
    	$this->success('退出登录成功！', '@admin/user/login');     
    }
}


