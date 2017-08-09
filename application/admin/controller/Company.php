<?php
namespace app\admin\controller;

use app\admin\controller\Base; 

use \think\Db;

class Company extends Base
{
    
    /**
     * 商品列表
     *
     */
    public function company_list()
    {    

      // $companys = Db::table('cx_company')->select();
      $companys = Db::query('select c.*, u.username from cx_company as c LEFT JOIN cx_company_user as u ON c.id = u. enterprise_id ORDER BY c.id DESC;');
      $this->assign('companys', $companys);
      return view('list');
    }


    /**
     *商品详情
     *
     */
    public function company_info()
    { 

      $id   = input('id');
      $company = Db::query('select c.*, u.username from cx_company as c LEFT JOIN cx_company_user as u ON c.id = u.enterprise_id WHERE c.id = :id', ['id'=>$id]);
      $company[0]['logo_url'] = $this->imagePathHandle($company[0]['enterprise_logo']);
      $this->assign('company', $company[0]);     
      return view('info');
    }

    /**
     *商品添加
     *
     */
    public function add_form()
    {  
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
      $this->assign('categorys', $categoryNew);     
      return view('company/add_form');
    }


    /**
     * 商品编辑
     *
     */
    public function edit_form()
    {   
      $company_id   = input("id");
      $company = Db::table('cx_company')->where('id', $company_id)->find();
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
      $company['logo_url'] = $this->imagePathHandle($company['enterprise_logo']);
      $company['logo_url_thumb'] = $this->imageThumbUrl($company['enterprise_logo']);
      $this->assign('company',      $company);     
      $this->assign('categorys',    $categoryNew);    
      return view('company/edit_form');
    }


    /**
     *商品相册添加
     *
     */
    public function gallery_upload_form()
    {   
      $goods_id       = input("goods_id");
      $delete         = input("delete");
      if($delete){
        $gallery_id   = input("gallery_id");

        //删除图片 
        $source_path = Db::query('SELECT source_path FROM `goods_gallery` WHERE id=?',[$gallery_id]);
        if($source_path){
          $this->delete_image_remote($source_path[0]['source_path']);
          Db::execute('delete from goods_gallery where id = :id',['id'=>$gallery_id]);          
        }
      }
      $goods          = Model('GoodsModel')->getGoods($goods_id);
      $goodsGallery   = Model('GoodsGalleryModel')->getGoodsGallery($goods_id);
      if(count($goodsGallery)){
        foreach($goodsGallery as $key => $value){
          $gallery['thumb_url']  = $this->imageThumbUrl($value['source_path']);
          $gallery['source_url'] = $this->imagePathHandle($value['source_path']);
          $gallery['name']      = $value['image_name'];
          $gallery['weight']    = $value['weight'];
          $gallery['id']      = $value['id'];
          $gallery['delete_url']      = url('@goods/gallery_upload', 'goods_id='.$goods_id.'&delete=1&gallery_id='.$value['id']);
          $gallerys[] = $gallery;
        }        
        $this->assign('gallerys',    $gallerys);   
      }


      $this->assign('goods',       $goods);   
      
      return view('goods/gallery_upload_form');
    }

    /**
     *商品详情添加
     *
     */
    public function detail_upload_form()
    {   
      return view('goods/detail_upload_form');
    }

    /**
     * 公司删除
     */
    public function company_delete()
    {    
      $company_id   = input("id");

      $companyOld = Db::table('cx_company')->where('id', $company_id)->find();
      $this->delete_image_remote($companyOld['enterprise_logo']);

      Db::table('cx_company')->where('id', $company_id)->delete();
      $this->success('删除企业成功', '@admin/company/list');      
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
        case 'company_user_add':
          $this->company_user_add_form_submit($post);
        break;
        case 'company_user_edit':
          $this->company_user_edit_form_submit($post);
        break;
        case 'gallery_upload':
          $file = $_FILES;
          $this->gallery_upload_form_submit($post, $file);
        break;
        case 'detail_upload':
          $this->detail_upload_form_submit($post);
        break;
        case 'gallery_upload_update':
          $this->gallery_upload_update_form_submit($post);
        break;
      }
    }

    /**
    * 商品添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'enterprise_name'            => $post['company_name'],
           'USCC'                       => $post['company_uscc'],
           'legal_representative'       => $post['company_leader'],
           'registered_capital'         => $post['company_money'],
           // 'company_type'               => $post['company_type'],
           'enterprise_url'             => $post['company_url'],
           'registered_time'            => $post['company_created'],
           'registered_address'         => $post['company_address'],
           // 'enterprise_logo'            => $post['company_logo'],
           'business_scope'             => $post['company_business'],
           'phone'                      => $post['company_phone'],
           // 'company_description'        => $post['company_description'],
           'deleted'                    => 0,
           'enterprise_registeraion_number' => '',
           'category'                   => $post['company_category'],
        );
        if($_FILES['company_logo']['size'] > 0 ){
          $uploadFiles['name']        = $_FILES['company_logo']['name'];
          $uploadFiles['type']        = $_FILES['company_logo']['type'];
          $uploadFiles['tmp_name']    = $_FILES['company_logo']['tmp_name'];
          $uploadFiles['size']        = $_FILES['company_logo']['size'];
          $sourcePath = $this->upload_image_remote($uploadFiles, 'cxgz/company/logo');
          // print_r($sourcePath);die();
          // $image_name = explode('/', $sourcePath);
          // $image_name = end($image_name);
          $data['enterprise_logo'] = $sourcePath;          
        }else{
           $data['enterprise_logo'] = '';
        }



        $result = Db::table('cx_company')->insert($data);

        if(!$result){
            $this->error('添加商品失败');
        }

        $this->success('添加商品成功', '@admin/company/list');      
    }

    /**
     * 商品编辑提交处理
     */
    public function edit_form_submit($post)
    {   
        $data = array(
           'enterprise_name'            => $post['company_name'],
           'USCC'                       => $post['company_uscc'],
           'legal_representative'       => $post['company_leader'],
           'registered_capital'         => $post['company_money'],
           // 'company_type'               => $post['company_type'],
           'enterprise_url'             => $post['company_url'],
           'registered_time'            => $post['company_created'],
           'registered_address'         => $post['company_address'],
           // 'enterprise_logo'            => $post['company_logo'],
           'business_scope'             => $post['company_business'],
           'phone'                      => $post['company_phone'],
           'category'                   => $post['company_category'],
           // 'company_description'        => $post['company_description'],
           // 'deleted'                    => 0,
           // 'enterprise_registeraion_number' => '',
        );
        $company_id = $post['company_id'];

        if(!empty($_FILES['company_logo']['size'])){
          $companyOld = Db::table('cx_company')->where('id', $company_id)->find();
          $this->delete_image_remote($companyOld['enterprise_logo']);

          $uploadFiles['name']        = $_FILES['company_logo']['name'];
          $uploadFiles['type']        = $_FILES['company_logo']['type'];
          $uploadFiles['tmp_name']    = $_FILES['company_logo']['tmp_name'];
          $uploadFiles['size']        = $_FILES['company_logo']['size'];
          $sourcePath = $this->upload_image_remote($uploadFiles, 'cxgz/company/logo');
          $data['enterprise_logo'] = $sourcePath;
        }

        $res = Db::table('cx_company')->where('id', $company_id)->update($data);

        if(!$res){
            $this->error('编辑企业失败');
        }

        $this->success('编辑企业成功', '@admin/company/list');
    }


    /**
     * 商品相册提交处理
     */
    public function gallery_upload_form_submit($post=null, $files=null)
    {   
        if(empty($post)){
          $post = $_POST;
        }
        if(empty($file)){
          $files = $_FILES;
        }
        // print_r($post);
        // print_r($files);
        // die();

        $uploadFiles['name']        = $files['file']['name'];
        $uploadFiles['type']        = $files['file']['type'];
        $uploadFiles['tmp_name']    = $files['file']['tmp_name'];
        $uploadFiles['size']        = $files['file']['size'];
        $sourcePath = $this->upload_image_remote($uploadFiles, 'b/product/gallery');
        // print_r($sourcePath);die();

        $image_name = explode('/', $sourcePath);
        $image_name = end($image_name);
        $param = array(
           'goods_id'           => $post['goods_id'],
           'category_path'      => 'b/product/gallery',
           'image_name'         => $image_name,
           'source_path'        => $sourcePath,
           'weight'             => 1,
           'created'            => time(),
        );
        

        $res = Model('GoodsGalleryModel')->addGoodsGallery($param);

        if(!$res){
            $this->error('添加商品相册失败');
        }

        $this->success('添加商品相册成功', '@goods/gallery_upload?goods_id='.$post['goods_id']);   
    }


    /**
     * 商品详情提交处理
     */
    public function detail_upload_form_submit($post)
    {   
        $param = array(
           'goods_name'           => $post['goods_name'],
           'goods_sn'             => $post['goods_sn'],
           'goods_number'         => $post['goods_number'],
           'cat_id'               => $post['cat_id'],
           'brand_id'             => $post['brand_id'],
           'sale_price'           => $post['sale_price'],
           'market_price'         => $post['market_price'],
           'keywords'             => $post['keywords'],
           'goods_desc'           => $post['goods_desc'],
           'status'               => 1,
           'created'              => time(),
           'updated'              => time(),
        );
        
        $goods_id = $post['goods_id'];

        $res = Model('GoodsModel')->editGoods($param, $goods_id);

        if(!$res){
            $this->error('编辑商品失败');
        }

        $this->success('编辑商品成功', '@goods/list');
    }

    /**
    * 更新上传图片
    */
    public function gallery_upload_update_form_submit($post){
      $old_weights = $post['old_img_weight'];
      $goods_id = $post['goods_id'];
      foreach($old_weights as $gallery_id => $weight){
        Db::execute('update goods_gallery set weight = :weight where id = :id and goods_id = :goods_id',['id'=>$gallery_id, 'goods_id'=>$goods_id, 'weight'=>$weight]);
      }

      $this->success('更新商品相册成功', '@goods/gallery_upload?goods_id='.$goods_id);
    }

    /**
    * 企业账号列表
    */
    public function company_user_list(){
      $companyUsers = Db::query('select u.*, c.enterprise_name from cx_company_user as u LEFT JOIN cx_company as c ON u.enterprise_id = c.id ORDER BY id DESC;');
      $this->assign('company_users', $companyUsers);
      return view('company_user_list');
    }

    /**
    * 企业账号详情
    */
    public function company_user_info(){
      $id   = input('id');
      $companyUsers = Db::query('select * from cx_company_user WHERE id = :id', ['id'=>$id]);
      $this->assign('company_users', $companyUsers[0]);     
      return view('company_user_info');
    }

    /**
    * 企业账号删除
    */
    public function company_user_delete(){
      $id   = input("id");

      Db::table('cx_company_user')->where('id', $id)->delete();
      $this->success('删除企业会员成功！', '@admin/company/user/list');  
    }

    /**
    * 添加企业账号
    */
    public function company_user_add_form(){
      $companys = Db::query('select * from cx_company ;');
      $company_user_name = 'cxgz'.rand(1000,9999);
      $this->assign('companys', $companys);     
      $this->assign('company_user_name', $company_user_name);     
      return view('company/company_user_add_form');
    }

    /**
    * 添加企业账号提交处理
    */
    public function company_user_add_form_submit(){
        $data = array(
           'username'          => $_POST['company_user_name'].$_POST['company_id'],
           'phone_num'                => $_POST['company_phone'],
           'enterprise_id'            => $_POST['company_id'],
           'password'                 => md5(123456),
           'deleted'                  => 0,
           'status'                   => 0,
        );

        $result = Db::table('cx_company_user')->insert($data);

        if(!$result){
            $this->error('添加企业会员失败');
        }

        $this->success('添加企业会员成功', '@admin/company/user/list');   
    }   

    /**
    * 编辑企业账号
    */
    public function company_user_edit_form(){
      $company_user_id   = input("id");

      $company_user = Db::query('select u.*, c.enterprise_name from cx_company_user as u LEFT JOIN cx_company as c ON u.enterprise_id = c.id WHERE u.id = :id;', ['id'=>$company_user_id]);
      $this->assign('company_user', $company_user[0]);   
      $this->assign('company_user_id', $company_user_id);

      return view('company/company_user_edit_form');
    }

    /**
    * 编辑企业账号提交处理
    */
    public function company_user_edit_form_submit(){
      $password        = $_POST['password'];
      $password_repeat = $_POST['password_repeat'];
      if(!empty($password) && ($password == $password_repeat)){
        $data = array(
           'phone_num'                => $_POST['company_phone'],
           'password'                 => md5($password),
           'deleted'                  => 0,
           'status'                   => 0,
        );
      }else{
        $this->error('两次输入密码不一致！');
      }



        $company_user_id = $_POST['company_user_id'];

        $res = Db::table('cx_company_user')->where('id', $company_user_id)->update($data);

        if(!$res){
            $this->error('编辑企业会员失败！');
        }

        $this->success('编辑企业会员成功！', '@admin/company/user/list');
    }   

}


