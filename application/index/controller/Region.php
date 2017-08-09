<?php
namespace app\index\controller;

class Region
{
    public function index()
    {
        return view('index');
    }

    public function lists()
    {
        return view('list');
    }

    public function info()
    {
        return view('info');
    }
}
