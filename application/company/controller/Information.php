<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class Information extends Base
{
    public $customTable        = 'cx_company_information';
    public $customUploadPath   = 'cxgz/company/information';
    public $customTitle        = '信息';
    public $customPathList     = 'information/list';
    public $customTemplateList = '/information/list';
    public $customTemplateAdd  = '/information/add_form';
    public $customTemplateEdit = '/information/edit_form';
    public $customTemplateInfo = '/information/info';
    /**
     * 列表
     */
    public function lists()
    {    
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $enterprise_id = $companyUser->enterprise_id;
      $lists = Db::table($this->customTable)->where('enterprise_id',$enterprise_id)->select();
      foreach($lists as $key => $value){
        $lists[$key]['created'] = date('Y-m-d H:i:s', $value['created']);
      }
      $this->assign('lists', $lists);
      $this->assign('customTitle', $this->customTitle);
      return view($this->customTemplateList);
    }

    /**
     * 详情
     *
     */
    public function info()
    { 
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $enterprise_id = $companyUser->enterprise_id;
      $id = input('information_id');
      $sql = "select * from cx_company_information WHERE enterprise_id = {$enterprise_id} AND id={$id}";

      $information = Db::query($sql);
      $this->assign('item', $information[0]);     
      return view($this->customTemplateInfo);
    }

    /**
     * 添加
     */
    public function add_form()
    {   
      $loginInfo      = session('company_user');
      $loginInfo      = json_decode($loginInfo);
      $enterprise_id  = $loginInfo->enterprise_id;

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('enterprise_id',    $enterprise_id); 

      return view($this->customTemplateAdd);
    }

    /**
    * 添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'title'                => $post['title'],
           'body'                 => $post['body'],
           'enterprise_id'        => $post['enterprise_id'],
           'created'              => time(),
        );

        $result = Db::table($this->customTable)->insert($data);

        if(!$result){
            $this->error("添加{$this->customTitle}失败");
        }

        $this->success("添加{$this->customTitle}成功", '@'.$this->customPathList);       
    }

    /**
     * 编辑
     */
    public function edit_form()
    {   
      $information_id = input('information_id');

      $item = Db::table($this->customTable)->where('id', $information_id)->find();

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('item',             $item); 

      return view($this->customTemplateEdit);
    }

    /**
     * 编辑提交处理
     */
    public function edit_form_submit($post)
    {   
        $data = array(
           'title'          => $post['title'],
           'body'           => $post['body'],
        );
        $information_id = $post['information_id'];

        $res = Db::table($this->customTable)->where('id', $information_id)->update($data);

        if(!$res){
            $this->error("编辑{$this->customTitle}失败");
        }

        $this->success("编辑{$this->customTitle}成功", '@'.$this->customPathList); 
    }

    /**
     * 删除
     */
    public function delete()
    {    
      $information_id   = input("information_id");

      $informationOld = Db::table($this->customTable)->where('id', $information_id)->find();


      Db::table($this->customTable)->where('id', $information_id)->delete();
      $this->success("删除{$this->customTitle}成功!", '@'.$this->customPathList);      
    }

    /**
    * 表单提交处理
    */
    public function form_submit(){
      $op = input('post.op');
      $post = $_POST;

      switch($op){
        case 'add':
          $this->add_form_submit($post);
        break;
        case 'edit':
          $this->edit_form_submit($post);
        break;
      }
    }
}


