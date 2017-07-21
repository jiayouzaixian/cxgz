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

   /**
    * 用户修改密码
    */
    public function user_password_form(){
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $username        = $companyUser->username;
      $company_user_id = $companyUser->id;

      $this->assign('username',         $username);
      $this->assign('company_user_id',  $company_user_id);

      return view('user_password');
    }

       /**
    * 用户修改密码提交处理
    */
    public function user_password_form_submit(){
      $uid                 = $_POST['company_user_id'];
      $old_password        = md5($_POST['old_password']);
      $new_password        = $_POST['new_password'];
      $new_password_repeat = $_POST['new_password_repeat'];

      $sql = "select * from cx_company_user WHERE id = {$uid} AND password='{$old_password}'";
      $user = Db::query($sql);
      if(!$user){
        $this->error("旧密码填写错误!");
      }

      if($new_password != $new_password_repeat){
        $this->error("两次密码输入不一致!");
      }

      $data = array('password'=>md5($new_password));
      $res = Db::table('cx_company_user')->where('id', $uid)->update($data);

      if(!$res){
          $this->error("修改密码失败");
      }

      $this->success("修改密码成功!", '@user/password'); 
    }
}


