<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyQualification extends Base
{
    $customTable        = 'cx_company_qualification';
    $customUploadPath   = 'cxgz/company/qualification';
    $customTitle        = '企业资质';
    $customPathList     = 'qualification/list';
    $customTemplateList = 'company/qualification/list';
    $customTemplateAdd  = 'company/qualification/add_form';
    $customTemplateEdit = 'company/qualification/edit_form';

    /**
     * 列表
     */
    public function list()
    {    
      $lists = Db::table($this->customTable)->select();

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

        $this->success("添加{$this->customTitle}成功", '@'.$this->$customPathList);       
    }

    /**
     * 编辑
     *
     */
    public function edit_form()
    {   
      $qualification_id = input['qualification_id'];

      $item = Db::table($this->customTable)->where('id', $qualification_id)->find();
      $item['pic_url'] = $this->imagePathHandle($item['quali_pic']);
      $this->assign('item',      $item);       
      return view('company/edit_form');
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

        if(is_array($_FILES) && count($_FILES)){
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
     * 企业资质删除
     */
    public function company_qualification_delete()
    {    
      $goods_id   = input("goods_id");
      GoodsModel::destroy($goods_id);
      $this->success('删除商品成功', '@goods/list');      
    }

    /**
    * 表单提交处理
    */
    public function form_submit(){
      $op = input('post.op');
      $post = $_POST;

      switch($op){
        case 'company_qualification_edit':
          $this->company_qualification_edit_form_submit($post);
        break;
      }
    }

}


