<?php
namespace app\index\controller;

use Amap\WebService;
use think\Controller;

class Amap extends Controller
{
    /**
     * 地址逆编码：ip地址-->地理地址
     */
    public function location()
    {
        $location = new WebService();
        $result = $location->location('123','25');
        var_dump($result);
    }
}
