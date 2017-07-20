<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class KnowledgePatent extends Base
{
    public $customTable        = 'cx_company_knowledge_patent';
    public $customUploadPath   = 'cxgz/company/patent';
    public $customTitle        = '专利';
    public $customPathList     = 'patent/list';
    public $customTemplateList = '/knowledge/patent/list';
    public $customTemplateAdd  = '/knowledge/patent/add_form';
    public $customTemplateEdit = '/knowledge/patent/edit_form';

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
           'authorization_date'   => $post['authorization_date'],
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
      $patent_id = input('patent_id');

      $item = Db::table($this->customTable)->where('id', $patent_id)->find();

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
           'authorization_date'   => $post['authorization_date'],
        );
        $patent_id = $post['patent_id'];

        $res = Db::table($this->customTable)->where('id', $patent_id)->update($data);

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
      $patent_id   = input("patent_id");

      $patentOld = Db::table($this->customTable)->where('id', $patent_id)->find();


      Db::table($this->customTable)->where('id', $patent_id)->delete();
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


