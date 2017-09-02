<?php
namespace app\index\controller;

use think\Controller;
class Redis extends Controller
{
    public function index(){
        return $this->fetch();
    }

    public function test()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis->set('test','hello redis');
        echo $redis->get('test');
   }
}
