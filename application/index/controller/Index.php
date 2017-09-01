<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index()
    {
//        $a = file_get_contents(APP_PATH.DS.'common/'.'file/key.txt');
//        var_dump($a);exit;
        return $this->fetch();
    }
}
