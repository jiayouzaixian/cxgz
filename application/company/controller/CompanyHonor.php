<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyHonor extends Base
{
    public $customTable        = 'cx_company_honor';
    public $customUploadPath   = 'cxgz/company/honor';
    public $customTitle        = '企业荣誉';
    public $customPathList     = 'honor/list';
    public $customTemplateList = '/company/honor/list';
    public $customTemplateAdd  = '/company/honor/add_form';
    public $customTemplateEdit = '/company/honor/edit_form';

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
        if(strpos($value['honor_pic'], 'uploads')){
          $honor_pic = 'upload/cxgz'.$value['honor_pic'];
        }else{
          $honor_pic = $value['honor_pic'];
        }
        $lists[$key]['picture_url'] = $this->imagePathHandle($honor_pic);
        $lists[$key]['thumb_url'] = $this->imageThumbUrl($honor_pic);
        $lists[$key]['thumb_url'] = str_replace('cxgz/thumb/ds', 'cxgz/uploads', $lists[$key]['thumb_url']);
      }
      $this->assign('lists', $lists);
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
      $this->assign('enterprise_id',      $enterprise_id); 
      return view($this->customTemplateAdd);
    }

    /**
    * 添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'honor_name'           => $post['honor_name'],
           'deleted'              => 0,
           'status'               => 1,
           'enterprise_id'        => $post['enterprise_id'],
        );
        $uploadFiles['name']        = $_FILES['honor_picture']['name'];
        $uploadFiles['type']        = $_FILES['honor_picture']['type'];
        $uploadFiles['tmp_name']    = $_FILES['honor_picture']['tmp_name'];
        $uploadFiles['size']        = $_FILES['honor_picture']['size'];

        $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
        $data['honor_pic'] = $sourcePath;

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
      $honor_id = input('honor_id');

      $item = Db::table($this->customTable)->where('id', $honor_id)->find();

      $item['picture_url'] = $this->imagePathHandle($item['honor_pic']);
      $item['thumb_url'] = $this->imageThumbUrl($item['honor_pic']);

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('item',      $item);       
      return view($this->customTemplateEdit);
    }

    /**
     * 编辑提交处理
     */
    public function edit_form_submit($post)
    {   
        $data = array(
           'honor_name'           => $post['honor_name'],
        );
        $honor_id = $post['honor_id'];

        if(!empty($_FILES['honor_picture']['size'])){
          $honorOld = Db::table($this->customTable)->where('id', $honor_id)->find();
          $this->delete_image_remote($honorOld['honor_pic']);

          $uploadFiles['name']        = $_FILES['honor_picture']['name'];
          $uploadFiles['type']        = $_FILES['honor_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['honor_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['honor_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['honor_pic'] = $sourcePath;
        }

        $res = Db::table($this->customTable)->where('id', $honor_id)->update($data);

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
      $honor_id   = input("honor_id");

      $honorOld = Db::table($this->customTable)->where('id', $honor_id)->find();
      if(strpos($honorOld['honor_pic'], 'uploads')){
        $honor_pic = 'upload/cxgz'.$honorOld['honor_pic'];
      }else{
        $honor_pic = $honorOld['honor_pic'];
      }

      $this->delete_image_remote($honor_pic);

      Db::table($this->customTable)->where('id', $honor_id)->delete();
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


