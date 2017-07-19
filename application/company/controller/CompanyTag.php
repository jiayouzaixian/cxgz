<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class CompanyTag extends Base
{
    public $customTable        = 'cx_company_tag';
    public $customUploadPath   = 'cxgz/company/tag';
    public $customTitle        = '企业标签';
    public $customPathList     = 'tag/edit';
    public $customTemplateList = '/company/tag/list';
    public $customTemplateAdd  = '/company/tag/add_form';
    public $customTemplateEdit = '/company/tag/edit_form';


    /**
     * 编辑
     */
    public function edit_form()
    {   
      $companyUser = session('company_user');
      $companyUser = json_decode($companyUser);
      $enterprise_id = $companyUser->enterprise_id;

      $companyTags = Db::table($this->customTable)->where('enterprise_id', $enterprise_id)->select();
      $tags = Db::table('cx_tag')->select();

      $this->assign('companyTags',    $companyTags); 
      $this->assign('tags',           $tags);     
      $this->assign('enterprise_id',  $enterprise_id);       
      return view($this->customTemplateEdit);
    }

    /**
     * 编辑提交处理
     */
    public function edit_form_submit()
    {   
        if(!isset($_POST['tag_id'])){
          $this->error("编辑{$this->customTitle}完成!");
        }
        $companyUser = session('company_user');
        $companyUser = json_decode($companyUser);
        $enterprise_id = $companyUser->enterprise_id;

        Db::execute('delete from cx_company_tag where enterprise_id = :enterprise_id', ['enterprise_id'=>$enterprise_id]);

        $tagIds = $_POST['tag_id'];
        foreach ($tagIds as $tagId) {
          Db::execute('insert into cx_company_tag (deleted, enterprise_id, tag_id, description) values (0, :enterprise_id, :tag_id, "")', ['enterprise_id'=>$enterprise_id, 'tag_id'=>$tagId]);
        }

        $this->success("编辑{$this->customTitle}成功", '@'.$this->customPathList); 
    }
}


