<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class KnowledgeSoftware extends Base
{
    public $customTable        = 'cx_company_knowledge_software';
    public $customUploadPath   = 'cxgz/company/software';
    public $customTitle        = '软件著作权';
    public $customPathList     = 'software/list';
    public $customTemplateList = '/knowledge/software/list';
    public $customTemplateAdd  = '/knowledge/software/add_form';
    public $customTemplateEdit = '/knowledge/software/edit_form';

    /**
     * 列表
     */
    public function lists()
    {    
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $enterprise_id = $companyUser->enterprise_id;
      $lists = Db::table($this->customTable)->where('enterprise_id',$enterprise_id)->select();

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
           'name'                 => $post['name'],
           'number'               => $post['number'],
           'type'                 => $post['type'],
           'register_date'        => $post['register_date'],
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
      $software_id = input('software_id');

      $item = Db::table($this->customTable)->where('id', $software_id)->find();

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
           'name'                 => $post['name'],
           'number'               => $post['number'],
           'type'                 => $post['type'],
           'register_date'        => $post['register_date'],
        );
        $software_id = $post['software_id'];

        $res = Db::table($this->customTable)->where('id', $software_id)->update($data);

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
      $software_id   = input("software_id");

      $softwareOld = Db::table($this->customTable)->where('id', $software_id)->find();


      Db::table($this->customTable)->where('id', $software_id)->delete();
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


