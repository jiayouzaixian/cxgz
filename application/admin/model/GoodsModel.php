<?php
namespace app\admin\model;

use think\Model;

class GoodsModel extends Model
{
    protected $pk = 'goods_id';
    protected $table = 'goods';
    protected $resultSetType = 'collection';
    
    /**
     *查询列表数据
     *
     */
    public function getGoodses($page,$page_size)
    {   
        $goodses = $this->page($page,$page_size)->select()->toArray();
        return $goodses;
    }

    /**
     *查询单个数据
     *
     */
    public function getGoods($goods_id)
    {   
    	$goods = db('goods')->where('goods_id',$goods_id)->find();
    	return $goods;
    }

    /**
     *存储商品
     *
     */
    public function addGoods($data)
    {   
      $res = $this->data($data)->save();
      return $res;
    }    

    /**
     *修改商品
     *
     */
    public function editGoods($data,$goods_id)
    {   
         $res = $this->save($data,['goods_id' => $goods_id]);
         return $res;
    }    
}