<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyBrand extends Base
{
    public $customTable        = 'cx_company_brand';
    public $customUploadPath   = 'cxgz/company/brand';
    public $customTitle        = '企业商标';
    public $customPathList     = 'brand/list';
    public $customTemplateList = '/company/brand/list';
    public $customTemplateAdd  = '/company/brand/add_form';
    public $customTemplateEdit = '/company/brand/edit_form';

    /**
     * 列表
     */
    public function lists()
    {    
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $enterprise_id = $companyUser->enterprise_id;
      $lists = Db::table($this->customTable)->where('enterprise_id',$enterprise_id)->select();
      $types      = $this->brand_types();
      $categorys  = $this->brand_categorys();

      foreach($lists as $key => $value){
        $lists[$key]['picture_url'] = $this->imagePathHandle($value['picture_path']);
        $lists[$key]['thumb_url'] = $this->imageThumbUrl($value['picture_path']);
        if(!empty($value['type'])){
          $lists[$key]['type'] = $types[$value['type']];
        }else{
          $lists[$key]['type'] = '';
        }

        if(!empty($value['category_id'])){
          $lists[$key]['category'] = $categorys[$value['category_id']];
        }else{
          $lists[$key]['category'] = '';
        }        
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

    }

    /**
     * 添加
     */
    public function add_form()
    {   
      $loginInfo      = session('company_user');
      $loginInfo      = json_decode($loginInfo);
      $enterprise_id  = $loginInfo->enterprise_id;
      $categorys      = $this->brand_categorys();
      $types          = $this->brand_types();

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('enterprise_id',    $enterprise_id); 
      $this->assign('categorys',        $categorys); 
      $this->assign('types',            $types); 

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
           'category_id'          => $post['category'],
           'enterprise_id'        => $post['enterprise_id'],
           'created'              => time(),
        );
        $uploadFiles['name']        = $_FILES['brand_picture']['name'];
        $uploadFiles['type']        = $_FILES['brand_picture']['type'];
        $uploadFiles['tmp_name']    = $_FILES['brand_picture']['tmp_name'];
        $uploadFiles['size']        = $_FILES['brand_picture']['size'];

        $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
        $data['picture_path'] = $sourcePath;

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

      $item['picture_url'] = $this->imagePathHandle($item['picture_path']);
      $item['thumb_url'] = $this->imageThumbUrl($item['picture_path']);

      $categorys      = $this->brand_categorys();
      $types          = $this->brand_types();

      $this->assign('customTitle',      $this->customTitle); 
      $this->assign('item',             $item); 
      $this->assign('categorys',        $categorys); 
      $this->assign('types',            $types); 

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
           'category_id'          => $post['category'],
        );
        $brand_id = $post['brand_id'];

        if(!empty($_FILES['brand_picture']['size'])){
          $brandOld = Db::table($this->customTable)->where('id', $brand_id)->find();
          $this->delete_image_remote($brandOld['picture_path']);

          $uploadFiles['name']        = $_FILES['brand_picture']['name'];
          $uploadFiles['type']        = $_FILES['brand_picture']['type'];
          $uploadFiles['tmp_name']    = $_FILES['brand_picture']['tmp_name'];
          $uploadFiles['size']        = $_FILES['brand_picture']['size'];

          $sourcePath = $this->upload_image_remote($uploadFiles, $this->customUploadPath);
          $data['picture_path'] = $sourcePath;
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

      $this->delete_image_remote($brandOld['picture_path']);

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

    /**
    * 商标分类
    */
    public function brand_categorys(){
      return array(
          1=>'化学原料',
          2=>'涂料油漆', 
          3=>'日化用品', 
          4=>'燃料油脂', 
          5=>'医  药', 
          6=>'金属材料', 
          7=>'机械设备', 
          8=>'手工器械', 
          9=>'科学仪器', 
          10=>'医疗器械', 
          11=>'灯具空调', 
          12=>'运输工具', 
          13=>'军火烟火', 
          14=>'珠宝钟表', 
          15=>'乐  器', 
          16=>'办公用品', 
          17=>'橡胶制品', 
          18=>'皮革皮具', 
          19=>'建筑材料', 
          20=>'家  具', 
          21=>'厨房洁具', 
          22=>'绳网袋篷', 
          23=>'纱线丝', 
          24=>'布料床单', 
          25=>'服装鞋帽', 
          26=>'纽扣拉链', 
          27=>'地毯席垫', 
          28=>'健身器材', 
          29=>'食  品', 
          30=>'方便食品', 
          31=>'饲料种籽', 
          32=>'啤酒饮料', 
          33=>'酒', 
          34=>'烟草烟具', 
          35=>'广告销售', 
          36=>'金融物管', 
          37=>'建筑修理', 
          38=>'通讯服务', 
          39=>'运输储藏', 
          40=>'材料加工', 
          41=>'教育娱乐', 
          42=>'网站服务', 
          43=>'餐饮住宿', 
          44=>'医疗园艺', 
          45=>'社会服务',
        );
    }

    public function brand_types(){
      return array(
          1 => '贵州省驰名商标',
          2 => '驰名商标',
          3 => '普通商标',
        );
    }

}


