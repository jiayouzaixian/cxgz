<?php
namespace app\index\controller;

class Product
{
    public function index()
    {
        return view('index');
    }

    public function info()
    {
        return view('info');
    }

    public function ask()
    {
        return view('ask');
    }
}
