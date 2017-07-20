<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyGallery extends Base
{
    public $customTable        = 'cx_company_gallery';
    public $customUploadPath   = 'cxgz/company/gallery';
    public $customTitle        = '企业相册';
    public $customPathList     = 'gallery/list';
    public $customTemplateList = '/company/gallery/list';
    public $customTemplateAdd  = '/company/gallery/add_form';
    public $customTemplateEdit = '/company/gallery/edit_form';

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
        $lists[$key]['picture_url'] = $this->imagePathHandle($value['source_path']);
        $lists[$key]['thumb_url'] = $this->imageThumbUrl($value['source_path']);
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
           'name'                 => $post['gallery_name'],
           'weight'               => $post['weight'],
           'enterprise_id'        => $post['enterprise_id'],
        );
        $uploadFiles['name']        = $_FILES['gallery_picture']['name'];
        $uploadFiles['type']        = $_FILES['gallery_picture']['type'];
        $uploadFiles['tmp_name']    = $_FILES['gallery_picture']['tmp_name'];
        $uploadFiles['size']        = $_FILES['gallery_picture']['size'];

        $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
        $data['source_path'] = $sourcePath;

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
      $gallery_id = input('gallery_id');

      $item = Db::table($this->customTable)->where('id', $gallery_id)->find();

      $item['picture_url'] = $this->imagePathHandle($item['source_path']);
      $item['thumb_url'] = $this->imageThumbUrl($item['source_path']);

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
           'name'         => $post['gallery_name'],
           'weight'               => $post['weight'],
        );
        $gallery_id = $post['gallery_id'];

        if(!empty($_FILES['gallery_picture']['size'])){
          $galleryOld = Db::table($this->customTable)->where('id', $gallery_id)->find();
          $this->delete_image_remote($galleryOld['source_path']);

          $uploadFiles['name']        = $_FILES['gallery_picture']['name'];
          $uploadFiles['type']        = $_FILES['gallery_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['gallery_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['gallery_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['source_path'] = $sourcePath;
        }

        $res = Db::table($this->customTable)->where('id', $gallery_id)->update($data);

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
      $gallery_id   = input("gallery_id");

      $galleryOld = Db::table($this->customTable)->where('id', $gallery_id)->find();

      $this->delete_image_remote($galleryOld['source_path']);

      Db::table($this->customTable)->where('id', $gallery_id)->delete();
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


