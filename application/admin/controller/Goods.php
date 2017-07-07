<?php
namespace app\admin\controller;

use app\admin\controller\Base; 
use app\admin\model\GoodsModel; 
use app\admin\model\GoodsGalleryModel; 

use \think\Db;

class Goods extends Base
{
    
    /**
     * 商品列表
     *
     */
    public function goods_list()
    {    
  
      $param = array(
          'page'      =>      input('page','1'),
          'page_size' =>      input('page_size','10'),
      );

      $goods = Model('GoodsModel')->getGoodses($param['page'],$param['page_size']);

      $BrandModel = Model('Brand');  
      $CategoryModel = Model('Category');

      foreach($goods as $key=>$val){
        $goods[$key]['brand_name'] = $BrandModel->getField('brand_name',$val['brand_id']);
        $goods[$key]['cat_name']   = $CategoryModel->getField('cat_name',$val['cat_id']);
      }

      $this->assign('result', $goods);
 
      return view('list');
    }

    /**
     *待售商品列表
     *
     */
    public function stay()
    {    
         return 'stay';

    	 return view('stay_list');
    }
    


    /**
     *商品详情
     *
     */
    public function info()
    { 

      $act   = input('act');
      $goods = '';
      if($act == 'edit'){
          $goods_id = input("goods_id");
          $goods = Model('GoodsModel')->getGoods($goods_id);
      }
      $brandList = Model('Brand')->getBrandses();
      $catList = Model('Category')->getCatList();

      $this->assign('result',$goods);     
      $this->assign('act',$act);
      $this->assign('brandList',$brandList);      
      $this->assign('catList',$catList);
      return view('info');

    }

    /**
     *商品添加
     *
     */
    public function add_form()
    {   
      $categorys  = Model('Category')->getCatList();
      $brands     = Model('Brand')->getBrandses();
      
      $this->assign('brands',     $brands);      
      $this->assign('categorys',  $categorys);

      return view('goods/add_form');
    }


    /**
     * 商品编辑
     *
     */
    public function edit_form()
    {   
      $goods_id   = input("goods_id");
      $goods      = Model('GoodsModel')->getGoods($goods_id);

      $categorys  = Model('Category')->getCatList();
      $brands     = Model('Brand')->getBrandses();
      
      $this->assign('goods',      $goods);     
      $this->assign('brands',     $brands);      
      $this->assign('categorys',  $categorys);

      return view('goods/edit_form');
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
     *待售商品列表
     *
     */
    public function goods_delete()
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
        case 'add':
          $this->add_form_submit($post);
        break;
        case 'edit':
          $this->edit_form_submit($post);
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
        
        $goods_id                  =  input('goods_id');

        $res = Model('GoodsModel')->addGoods($param,$goods_id);

        if(!$res){
            $this->error('添加商品失败');
        }

        $this->success('添加商品成功', '@goods/add');      
    }

    /**
     * 商品编辑提交处理
     */
    public function edit_form_submit($post)
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
}


