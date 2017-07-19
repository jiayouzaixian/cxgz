<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyQualification extends Base
{
    public $customTable        = 'cx_company_qualification';
    public $customUploadPath   = 'cxgz/company/qualification';
    public $customTitle        = '企业资质';
    public $customPathList     = 'qualification/list';
    public $customTemplateList = '/company/qualification/list';
    public $customTemplateAdd  = '/company/qualification/add_form';
    public $customTemplateEdit = '/company/qualification/edit_form';

    /**
     * 列表
     */
    public function lists()
    {    
      $lists = Db::table($this->customTable)->select();

      foreach($lists as $key => $value){
        if(strpos($value['quali_pic'], 'uploads')){
          $quali_pic = 'upload/cxgz'.$value['quali_pic'];
        }else{
          $quali_pic = $value['quali_pic'];
        }
        $lists[$key]['picture_url'] = $this->imagePathHandle($quali_pic);
        $lists[$key]['thumb_url'] = $this->imageThumbUrl($quali_pic);
      }
      $this->assign('lists', $lists);
      return view('/company/qualification/list');
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
           'quali_name'           => $post['qualification_name'],
           'deleted'              => 0,
           'status'               => 1,
           'enterprise_id'        => $post['enterprise_id'],
        );
        $uploadFiles['name']        = $_FILES['qualification_picture']['name'];
        $uploadFiles['type']        = $_FILES['qualification_picture']['type'];
        $uploadFiles['tmp_name']    = $_FILES['qualification_picture']['tmp_name'];
        $uploadFiles['size']        = $_FILES['qualification_picture']['size'];

        $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
        $data['quali_pic'] = $sourcePath;

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
      $qualification_id = input('qualification_id');

      $item = Db::table($this->customTable)->where('id', $qualification_id)->find();

      $item['picture_url'] = $this->imagePathHandle($item['quali_pic']);
      $item['thumb_url'] = $this->imageThumbUrl($item['quali_pic']);

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
           'quali_name'           => $post['qualification_name'],
        );
        $qualification_id = $post['qualification_id'];

        if(!empty($_FILES['qualification_picture']['size'])){
          $qualificationOld = Db::table($this->customTable)->where('id', $qualification_id)->find();
          $this->delete_image_remote($qualificationOld['quali_pic']);

          $uploadFiles['name']        = $_FILES['qualification_picture']['name'];
          $uploadFiles['type']        = $_FILES['qualification_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['qualification_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['qualification_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['quali_pic'] = $sourcePath;
        }

        $res = Db::table($this->customTable)->where('id', $qualification_id)->update($data);

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
      $qualification_id   = input("qualification_id");

      $qualificationOld = Db::table($this->customTable)->where('id', $qualification_id)->find();
      if(strpos($qualificationOld['quali_pic'], 'uploads')){
        $quali_pic = 'upload/cxgz'.$qualificationOld['quali_pic'];
      }else{
        $quali_pic = $qualificationOld['quali_pic'];
      }

      $this->delete_image_remote($quali_pic);

      Db::table($this->customTable)->where('id', $qualification_id)->delete();
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


