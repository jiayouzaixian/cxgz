<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class Product extends Base
{
    public $customTable        = 'cx_product';
    public $customUploadPath   = 'cxgz/product';
    public $customTitle        = '产品';
    public $customPathList     = 'product/list';
    public $customTemplateList = '/product/list';
    public $customTemplateAdd  = '/product/add_form';
    public $customTemplateEdit = '/product/edit_form';

    /**
     * 列表
     */
    public function lists()
    {    
      $lists = Db::table($this->customTable)->select();

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
     * 删除
     */
    public function company_qualification_delete()
    {    
      $goods_id   = input("goods_id");
      GoodsModel::destroy($goods_id);
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


