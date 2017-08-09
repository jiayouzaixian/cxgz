<?php
namespace app\index\controller;

use \think\Db;
use app\index\controller\Base; 

class Trademark extends Base
{
    public function index()
    {
    	// $companys = Db::query('select * from cx_company  ORDER BY id DESC;');
        $name = input('keyword');
        $trademark_cat = input('trademark_cat') ? input('trademark_cat') : 1;
        $type = input('type') ? input('type') : null;
        $trademarksNew = array();

        if(!empty($name)){
        	if($type){
        		$trademarks = Db::table('cx_trademark')->where('apply_name', 'like', "%$name%")->where('trademark_cat', $trademark_cat)->where('type', $type)->paginate(20);
        	}else{
        		$trademarks = Db::table('cx_trademark')->where('apply_name', 'like', "%$name%")->where('trademark_cat', $trademark_cat)->paginate(20);
        	}
            
            $keyword = $name;
        }else{
        	if($type){
        		$trademarks = Db::table('cx_trademark')->where('trademark_cat', $trademark_cat)->where('type', $type)->paginate(20);
        	}else{
        		$trademarks = Db::table('cx_trademark')->where('trademark_cat', $trademark_cat)->paginate(20);
        	}
            
            $keyword = '';
        }

        foreach($trademarks as $key => $value){
        	if($value['trademark_pic']){
        		$value['trademark_pic'] = $this->imagePathHandle('upload/cxgz'.$value['trademark_pic']);       		
        	}else{
        		$value['trademark_pic'] = $this->imagePathHandle('/upload/cxgz/company/cxgz_default.jpg');        		
        	}
        	$trademarksNew[$key] = $value;
      	}
      	$page = $trademarks->render();

        $this->assign('keyword', $keyword);
      	$this->assign('trademarks', $trademarksNew);
      	$this->assign('trademark_cat', $trademark_cat);
      	$this->assign('page', $page);

        return view('index');
    }

    public function info(){
        $id = input('id');
        //基础信息
        $sql = "select * from cx_trademark WHERE id = {$id} ";
        $info = Db::query($sql);
        if($info[0]['trademark_pic']){
            $info[0]['trademark_pic'] = $this->imagePathHandle('upload/cxgz'.$info[0]['trademark_pic']); 
            $info[0]['trademark_pic_thumb'] = $this->imageThumbUrl('upload/cxgz'.$info[0]['trademark_pic'],'',0,8);
            $info[0]['trademark_pic_thumb'] = str_replace('cxgz/thumb/ds', 'cxgz/uploads', $info[0]['trademark_pic_thumb']);          
        }else{
            $info[0]['trademark_pic'] = $this->imagePathHandle('/upload/cxgz/company/cxgz_default.jpg');                
        }     
        // $info[0]['company_description'] = html_entity_decode($info[0]['company_description']);
        $this->assign('trademark', $info[0]);     

        //品牌建设
        // $sql = "SELECT c.*, t.pic_path FROM cx_company_tag as c LEFT JOIN cx_tag as t ON c.tag_id = t.id WHERE c.enterprise_id = {$id}";
        // $tags = Db::query($sql);
        // if($tags){
        //     foreach($tags as $key => $value){
        //         $tags[$key]['pic_path'] = $this->imagePathHandle('upload/cxgz'.$value['pic_path']);
        //         $tags[$key]['pic_path_thumb'] = $this->imageThumbUrl('upload/cxgz'.$value['pic_path'],'',0,8);
        //         $tags[$key]['pic_path_thumb'] = str_replace('cxgz/thumb/ds', 'cxgz/uploads', $tags[$key]['pic_path_thumb']); 
        //     }
        //     $this->assign('tags', $tags);     
        // }

        // //企业资质
        // $sql = "SELECT * FROM cx_company_qualification WHERE enterprise_id = {$id}";
        // $qualifications = Db::query($sql);
        // if($qualifications){
        //     foreach($qualifications as $key => $value){
        //         $source_pic = $this->imagePathHandle('upload/cxgz'.$value['quali_pic']);
        //         // $result = file_get_contents($source_pic);
        //         // if(strpos($result, 'Fatal error') === false){
        //             $qualifications[$key]['quali_pic'] = $source_pic;
        //             $qualifications[$key]['quali_pic_thumb'] = $this->imageThumbUrl('upload/cxgz'.$value['quali_pic']);
        //             $qualifications[$key]['quali_pic_thumb'] = str_replace('cxgz/thumb/ds', 'cxgz/uploads', $qualifications[$key]['quali_pic_thumb']);                    
        //         // }else{
        //         //     unset($qualifications[$key]);
        //         // }
        //     }
        //     $this->assign('qualifications', $qualifications);     
        // }

        // //企业荣耀
        // $sql = "SELECT * FROM cx_company_honor WHERE enterprise_id = {$id}";
        // $honors = Db::query($sql);
        // if($honors){
        //     foreach($honors as $key => $value){
        //         $source_pic = $this->imagePathHandle('upload/cxgz'.$value['honor_pic']);

        //         $honors[$key]['honor_pic'] = $source_pic;
        //         $honors[$key]['honor_pic_thumb'] = $this->imageThumbUrl('upload/cxgz'.$value['honor_pic']);
        //         $honors[$key]['honor_pic_thumb'] = str_replace('cxgz/thumb/ds', 'cxgz/uploads', $honors[$key]['honor_pic_thumb']);                    
        //     }
        //     $this->assign('honors', $honors);     
        // }

        // //企业商标
        // $sql = "SELECT * FROM cx_company_brand WHERE enterprise_id = {$id}";
        // $brands = Db::query($sql);
        // if($brands){
        //     foreach($brands as $key => $value){
        //         $source_pic = $this->imagePathHandle($value['picture_path']);

        //         $brands[$key]['brand_pic'] = $source_pic;
        //         $brands[$key]['brand_pic_thumb'] = $this->imageThumbUrl($value['brand_pic']);   
        //         if($value['type'] == 1){
        //             $type = '贵州省驰名商标';
        //         }   
        //         if($value['type'] == 2){
        //             $type = '驰名商标';
        //         } 
        //         if($value['type'] == 3){
        //             $type = '普通商标';
        //         }    
        //         $brands[$key]['type'] = $type;           
        //     }
        //     $this->assign('brands', $brands);     
        // }

        // //企业专利
        // $sql = "SELECT * FROM cx_company_patent WHERE enterprise_id = {$id}";
        // $patents = Db::query($sql);
        // if($patents){
        //     $this->assign('patents', $patents);     
        // }
        
        return view('info');
    }
}
