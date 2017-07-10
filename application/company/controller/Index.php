<?php
namespace app\company\controller;

use app\company\controller\Base; 

use \think\Db;

class Index extends Base
{
    public function index()
    {
        return view('index');
    }
}
