<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class Brand extends Base
{
    public $customTable        = 'cx_brand';
    public $customUploadPath   = 'cxgz/company/brand1';
    public $customTitle        = '品牌';
    public $customPathList     = 'brand/list';
    public $customTemplateList = '/brand/list';
    public $customTemplateAdd  = '/brand/add_form';
    public $customTemplateEdit = '/brand/edit_form';
    public $customTemplateInfo = '/brand/info';

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
        $lists[$key]['created']           = date('Y-m-d H:i:s', $value['created']);
        $lists[$key]['logo_picture_url']  = $this->imagePathHandle($value['logo']);
        $lists[$key]['logo_thumb_url']    = $this->imageThumbUrl($value['logo']);
        $category = Db::table('cx_category')->where('id', $value['category_id'])->find();
        $lists[$key]['category'] = $category['name'];
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
      $id = input('brand_id');
      $sql = "select * from {$this->customTable} WHERE enterprise_id = {$enterprise_id} AND id={$id}";

      $brand = Db::query($sql);
      $item = $brand[0];

      $item['logo_picture_url'] = $this->imagePathHandle($item['logo']);
      $item['logo_thumb_url'] = $this->imageThumbUrl($item['logo']);
      $item['description_picture_picture_url'] = $this->imagePathHandle($item['description_picture']);
      $item['description_picture_thumb_url'] = $this->imageThumbUrl($item['description_picture']);

      $category = Db::table('cx_category')->where('id', $item['category_id'])->find();
      $item['category'] = $category['name'];

      $this->assign('item', $item);     
      $this->assign('customTitle', $this->customTitle);

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

      $categorys = Db::query('select * from cx_category WHERE level = 1 ORDER BY weight ASC;');
      if($categorys){
        foreach($categorys as $category){
          $sql = "select * from cx_category WHERE parent = {$category['id']} AND level = 2 ORDER BY weight ASC;";
          $categoryChilds = Db::query($sql);
          $categoryNew[] = $category;
          if($categoryChilds){
            foreach($categoryChilds as $categoryChild){
             $categoryNew[] = $categoryChild;
            }
          }
        }
      }

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('categorys',         $categoryNew); 
      $this->assign('enterprise_id',    $enterprise_id); 

      return view($this->customTemplateAdd);
    }

    /**
    * 添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'name'                => $post['name'],
           'brief'                => $post['brief'],
           'category_id'          => $post['category'],
           'status'               => 1,
           'website'              => $post['website'],
           'description'          => $post['description'],
           'tag'                  => $post['tag'],
           'enterprise_id'        => $post['enterprise_id'],
           'created'              => time(),
        );
        if(!empty($_FILES['logo']['size'])){
          $uploadFiles['name']        = $_FILES['logo']['name'];
          $uploadFiles['type']        = $_FILES['logo']['type'];
          $uploadFiles['tmp_name']    = $_FILES['logo']['tmp_name'];
          $uploadFiles['size']        = $_FILES['logo']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['logo'] = $sourcePath;
        }

        if(!empty($_FILES['logo']['size'])){
          $uploadFiles['name']        = $_FILES['description_picture']['name'];
          $uploadFiles['type']        = $_FILES['description_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['description_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['description_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['description_picture'] = $sourcePath;
        }
        
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
      $brand_id = input('brand_id');

      $item = Db::table($this->customTable)->where('id', $brand_id)->find();

      $categorys = Db::query('select * from cx_category WHERE level = 1 ORDER BY weight ASC;');
      if($categorys){
        foreach($categorys as $category){
          $sql = "select * from cx_category WHERE parent = {$category['id']} AND level = 2 ORDER BY weight ASC;";
          $categoryChilds = Db::query($sql);
          $categoryNew[] = $category;
          if($categoryChilds){
            foreach($categoryChilds as $categoryChild){
             $categoryNew[] = $categoryChild;
            }
          }
        }
      }

      $item['logo_picture_url'] = $this->imagePathHandle($item['logo']);
      $item['logo_thumb_url'] = $this->imageThumbUrl($item['logo']);
      $item['description_picture_picture_url'] = $this->imagePathHandle($item['description_picture']);
      $item['description_picture_thumb_url'] = $this->imageThumbUrl($item['description_picture']);

      $this->assign('categorys',         $categoryNew); 
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
           'name'                => $post['name'],
           'brief'                => $post['brief'],
           'category_id'          => $post['category'],
           'website'              => $post['website'],
           'description'          => $post['description'],
           'tag'                  => $post['tag'],
        );
        $brand_id = $post['brand_id'];

        if(!empty($_FILES['logo']['size'])){
          $brandOld = Db::table($this->customTable)->where('id', $brand_id)->find();
          $this->delete_image_remote($brandOld['logo']);

          $uploadFiles['name']        = $_FILES['logo']['name'];
          $uploadFiles['type']        = $_FILES['logo']['type'];
          $uploadFiles['tmp_name']    = $_FILES['logo']['tmp_name'];
          $uploadFiles['size']        = $_FILES['logo']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['logo'] = $sourcePath;
        }

        if(!empty($_FILES['description_picture']['size'])){
          $brandOld = Db::table($this->customTable)->where('id', $brand_id)->find();
          $this->delete_image_remote($brandOld['description_picture']);

          $uploadFiles['name']        = $_FILES['description_picture']['name'];
          $uploadFiles['type']        = $_FILES['description_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['description_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['description_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['description_picture'] = $sourcePath;
        }

        $res = Db::table($this->customTable)->where('id', $brand_id)->update($data);

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
      $brand_id   = input("brand_id");

      $brandOld = Db::table($this->customTable)->where('id', $brand_id)->find();

      $this->delete_image_remote($brandOld['logo']);
      $this->delete_image_remote($brandOld['description_picture']);

      Db::table($this->customTable)->where('id', $brand_id)->delete();
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


