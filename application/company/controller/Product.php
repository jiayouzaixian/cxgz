<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class Product extends Base
{
    public $customTable        = 'cx_product';
    public $customUploadPath   = 'cxgz/company/product';
    public $customTitle        = '产品';
    public $customPathList     = 'product/list';
    public $customTemplateList = '/product/list';
    public $customTemplateAdd  = '/product/add_form';
    public $customTemplateEdit = '/product/edit_form';
    public $customTemplateInfo = '/product/info';

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
        $lists[$key]['picture_source_url']  = $this->imagePathHandle($value['picture']);
        $lists[$key]['picture_thumb_url']    = $this->imageThumbUrl($value['picture']);

        $brand = Db::table('cx_brand')->where('id', $value['brand_id'])->find();
        $lists[$key]['brand'] = $brand['name'];

        $categorys = $this->product_category();
        $lists[$key]['category'] = $categorys[$value['category_id']];
        $region = $this->product_region();
        $lists[$key]['region'] = $region[$value['region_id']];
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
      $id = input('product_id');
      $sql = "select * from {$this->customTable} WHERE enterprise_id = {$enterprise_id} AND id={$id}";

      $product = Db::query($sql);
      $item = $product[0];

      $item['picture_source_url'] = $this->imagePathHandle($item['picture']);
      $item['picture_thumb_url'] = $this->imageThumbUrl($item['picture']);
      $item['description_picture_source_url'] = $this->imagePathHandle($item['description_picture']);
      $item['description_picture_thumb_url'] = $this->imageThumbUrl($item['description_picture']);

      $brand = Db::table('cx_brand')->where('id', $item['brand_id'])->find();
      $item['brand'] = $brand['name'];

      $categorys = $this->product_category();
      $item['category'] = $categorys[$item['category_id']];
      $region = $this->product_region();
      $item['region'] = $region[$item['region_id']];

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

      $brands     = Db::table('cx_brand')->where('enterprise_id', $enterprise_id)->select();
      $categorys  = $this->product_category();
      $regions    = $this->product_region();

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('enterprise_id',    $enterprise_id); 
      $this->assign('brands',           $brands); 
      $this->assign('categorys',        $categorys); 
      $this->assign('regions',          $regions); 

      return view($this->customTemplateAdd);
    }

    /**
    * 添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'name'                   => $post['name'],
           'category_id'            => $post['category'],
           'region_id'              => $post['region'],
           'brand_id'               => $post['brand'],
           'status'                 => 1,
           'enterprise_id'          => $post['enterprise_id'],
           'price'                  => $post['price'],
           'description'            => $post['description'],
           'created'                => time(),
        );
        if(!empty($_FILES['picture']['size'])){
          $uploadFiles['name']        = $_FILES['picture']['name'];
          $uploadFiles['type']        = $_FILES['picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['picture'] = $sourcePath;          
        }
        if(!empty($_FILES['description_picture']['size'])){
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
     *
     */
    public function edit_form()
    {   
      $product_id     = input('product_id');
      $loginInfo      = session('company_user');
      $loginInfo      = json_decode($loginInfo);
      $enterprise_id  = $loginInfo->enterprise_id;

      $item       = Db::table($this->customTable)->where('id', $product_id)->find();
      $item['picture_source_url'] = $this->imagePathHandle($item['picture']);
      $item['picture_thumb_url']  = $this->imageThumbUrl($item['picture']);
      $item['description_picture_source_url'] = $this->imagePathHandle($item['description_picture']);
      $item['description_picture_thumb_url']  = $this->imageThumbUrl($item['description_picture']);

      $brands     = Db::table('cx_brand')->where('enterprise_id', $enterprise_id)->select();
      $categorys  = $this->product_category();
      $regions    = $this->product_region();

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('enterprise_id',    $enterprise_id); 
      $this->assign('brands',           $brands); 
      $this->assign('categorys',        $categorys); 
      $this->assign('regions',          $regions); 
      $this->assign('item',             $item);

      return view('edit_form');
    }

    /**
     * 编辑提交处理
     */
    public function edit_form_submit($post)
    {
        $data = array(
           'name'                   => $post['name'],
           'category_id'            => $post['category'],
           'region_id'              => $post['region'],
           'brand_id'               => $post['brand'],
           'price'                  => $post['price'],
           'description'            => $post['description'],
        );
       $product_id = $post['product_id'];

        if(!empty($_FILES['picture']['size'])){
          $productOld = Db::table($this->customTable)->where('id', $product_id)->find();
          $this->delete_image_remote($productOld['picture']);

          $uploadFiles['name']        = $_FILES['picture']['name'];
          $uploadFiles['type']        = $_FILES['picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['picture'] = $sourcePath;          
        }
        if(!empty($_FILES['description_picture']['size'])){
          $productOld = Db::table($this->customTable)->where('id', $product_id)->find();
          $this->delete_image_remote($productOld['description_picture']);

          $uploadFiles['name']        = $_FILES['description_picture']['name'];
          $uploadFiles['type']        = $_FILES['description_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['description_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['description_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['description_picture'] = $sourcePath;
        }


        $res = Db::table($this->customTable)->where('id', $product_id)->update($data);

        if(!$res){
            $this->error("编辑{$this->customTitle}失败");
        }

        $this->success("编辑{$this->customTitle}成功", '@'.$this->customPathList); 
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


    /**
     * 删除
     */
    public function delete()
    {
      $product_id   = input("product_id");

      $productOld = Db::table($this->customTable)->where('id', $product_id)->find();

      $this->delete_image_remote($productOld['picture']);
      $this->delete_image_remote($productOld['description_picture']);

      Db::table($this->customTable)->where('id', $product_id)->delete();
      $this->success("删除{$this->customTitle}成功!", '@'.$this->customPathList);   
    }

    /**
    * 商标分类
    */
    public function product_category(){
      return array(
          1=>'石化',
          2=>'冶金', 
          3=>'机械', 
          4=>'皮革', 
          5=>'橡胶', 
          6=>'水泥', 
          7=>'焦炭', 
          8=>'陶瓷', 
          9=>'医药', 
          10=>'石材', 
          11=>'纺织', 
          12=>'钢铁', 
          13=>'船舶', 
        );
    }

    public function product_region(){
      return array(
          1 => '贵阳',
          2 => '遵义',
          3 => '安顺',
          4 => '黔南',
          5 => '黔东南',
          6 => '毕节',
          7 => '铜仁',
          8 => '六盘水',
          9 => '黔西南',
        );
    }
}


