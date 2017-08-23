<?php
namespace app\pay\controller;

use think\Controller;
use Wechat\WechatMenu;
use Wechat\WechatOauth;
use Wechat\WechatPay;
use Wechat\WechatUser;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function scanQr()
    {
        vendor("phpqrcode.phpqrcode");

        $wxPay = new WechatPay();
        $result = $wxPay->getQrcPayUrl('测试', '20154785171254123332', 1, 'http://www.baidu.com');
        echo \QRcode::png($result);exit;
    }
}
