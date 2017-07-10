<?php
namespace app\company\controller;

use think\Controller;

class Base extends Controller
{ 
    
    /**
     *构造函数
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->assignAdminUser();
    }   

    /**
     * 获取管理员数据
     */
    protected function getAdminUser()
    {
         $adminUser = session('admin_user');
    
         if($adminUser){
            $adminUser = json_decode($adminUser,true);
         }

         return $adminUser;
    }


    /**
     * 分配管理员至模板，未登录则跳转至登录
     */
    public function assignAdminUser($name = 'admin_user')
    {
         $adminUser = session('admin_user');

         if(!$adminUser){
           $this->redirect('@user/login'); 
         }else{
            $adminUser = json_decode($adminUser,true);
         }
          // print_r($adminUser);die();
         $this->assign($name, $adminUser);
    }

    /**
     * 图片上传远程的处理函数
     *
     * @access      public
     * @param       array       upload     包含上传的图片文件信息的数组
     * @param       array       dir        必须在服务器上提前创建目录
     * @return      mix         如果成功则返回文件名，否则返回false
     */
    function upload_image_remote($upload, $dir = '')
    {
  
        $img_name = $this->unique_name($dir);
        $img_name = $dir . $img_name . $this->get_filetype($upload['name']);
 
 
        /* 远程上传图片 */
        $curl = curl_init();     
        $file = new \CurlFile($upload['tmp_name']); 

        $data = array('picture'=>$file, 'dir'=>$dir, 'name'=>$upload['name']);  

        curl_setopt($curl, CURLOPT_URL, "http://img2.t.jiayou9.com/upload_image.php?debug=1");   
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($curl, CURLOPT_POST, true);    
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);   
        $result = curl_exec($curl);    
        curl_close($curl);
        $upload_result = json_decode($result);

        if($upload_result->code == 0){
            return $upload_result->data->file_path;
        }else{
            return false;
        }
    }

    /**
     *  生成指定目录不重名的文件名
     *
     * @access  public
     * @param   string      $dir        要检查是否有同名文件的目录
     *
     * @return  string      文件名
     */
    function unique_name($dir)
    {
        $filename = '';
        while (empty($filename))
        {
            $filename = $this->random_filename();
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png'))
            {
                $filename = '';
            }
        }

        return $filename;
    }

    /**
     *  返回文件后缀名，如‘.php’
     *
     * @access  public
     * @param
     *
     * @return  string      文件后缀名
     */
    function get_filetype($path)
    {
        $pos = strrpos($path, '.');

        if ($pos !== false)
        {
            return substr($path, $pos);
        }
        else
        {
            return '';
        }
    }

    /**
     * 生成随机的数字串
     *
     * @author: weber liu
     * @return string
     */
    function random_filename()
    {
        $str = '';
        for($i = 0; $i < 9; $i++)
        {
            $str .= mt_rand(0, 9);
        }

        return time() . $str;
    }

    /**
    * 获取图片缩略图
    */
    public function imageThumbUrl($image_path, $part='', $debug=0, $type = 1){
        $base_url = "http://img2.t.jiayou9.com/";

        switch ($type) {
          case 1:
            $width = ',p_20';
            break;
          case 2:
            $width = ',w_150';
            break;    
          default:
            $width = ',p_20';
            break;
        }

        $imageThumbUrl = str_replace('upload', 'thumb/d', $image_path);

        if(strstr($imageThumbUrl,'jpeg'))
        {
          $path = substr($imageThumbUrl, 0, -5);
          $extension = substr($imageThumbUrl, -5);
        }else{
          $path = substr($imageThumbUrl, 0, -4);
          $extension = substr($imageThumbUrl, -4);
        }

        $imageThumbUrl = $base_url.$path.$width.$extension;

        return $imageThumbUrl;
    }

    function imagePathHandle($image_path, $part='', $debug=0){
      if($debug){
        $host = 'http://img2.t.jiayou9.com/';
      }else{
        $host = 'http://img2.t.jiayou9.com/';
      }
     
      return $host.$image_path;
    }

    /**
     * 删除远程图片
     *
     * @access      public
     * @param       array       source_path     图片路径
     */
    function delete_image_remote($source_path)
    {
        $curl = curl_init();   
        $data = array('source_path'=>$source_path,);  

        curl_setopt($curl, CURLOPT_URL, "http://img2.t.jiayou9.com/delete_image.php?debug=1");   
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    
        curl_setopt($curl, CURLOPT_POST, 1);    
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);   

        $result = curl_exec($curl);    
        curl_close($curl);
    }

}