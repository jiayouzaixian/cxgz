<?php
namespace app\admin\controller;

use app\admin\controller\Base; 

use \think\Db;

class Category extends Base
{
    
    /**
     * 分类列表
     *
     */
    public function category_list()
    {    
      $categorys = Db::query('select * from cx_category ORDER BY weight ASC;');
      $this->assign('categorys', $categorys);
      return view('list');
    }

    /**
     * 分类添加
     */
    public function add_form()
    {   
       $this->assign('categorys', array());     
      return view('category/add_form');
    }


    /**
     * 分类编辑
     */
    public function edit_form()
    {   
      $category_id   = input("id");
      $category = Db::table('cx_category')->where('id', $category_id)->find();
      $this->assign('category',      $category);       
      $this->assign('categorys',     array()); 
      return view('category/edit_form');
    }

    /**
     * 分类删除
     */
    public function category_delete()
    {    
      $company_id   = input("id");
      Db::table('cx_category')->where('id', $company_id)->delete();
      $this->success('删除分类成功', '@admin/category/list');      
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
    * 分类添加提交处理
    */
    public function add_form_submit($post){
        $data = array(
           'name'         => $post['category_name'],
           'parent'       => $post['category_parent'],
           'weight'       => $post['category_weight'],
           'type'         => $this->category_get_type_id($post['category_type']),
        );

        if($post['category_parent'] == 0){
          $data['level'] = 1;
        }else{
          $data['level'] = 2;
        }

        $result = Db::table('cx_category')->insert($data);

        if(!$result){
            $this->error('添加分类失败');
        }

        $this->success('添加分类成功', '@admin/category/add');      
    }

    /**
     * 分类编辑提交处理
     */
    public function edit_form_submit($post)
    {   
        $data = array(
           'name'         => $post['category_name'],
           'parent'       => $post['category_parent'],
           'weight'       => $post['category_weight'],
           'type'         => $this->category_get_type_id($post['category_type']),
        );
        if($post['category_parent'] == 0){
          $data['level'] = 1;
        }else{
          $data['level'] = 2;
        }

        $category_id = $post['category_id'];

        $res = Db::table('cx_category')->where('id', $category_id)->update($data);

        if(!$res){
            $this->error('编辑分类失败');
        }

        $this->success('编辑分类成功', '@admin/category/list');
    }

    /**
    * 获取分类ID
    */
    public function category_get_type_id($typeName){
      switch ($typeName) {
        case 'company':
          $typeId = 1;
          break;
        case 'pinpai':
          $typeId = 2;
          break;
        case 'product':
          $typeId = 3;
          break;
        case 'jingqu':
          $typeId = 4;
          break;
        case 'shangbiao':
          $typeId = 5;
          break;        
      }

      return $typeId;
    }
}


