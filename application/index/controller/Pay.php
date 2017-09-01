<?php
namespace app\index\controller;

use alipay\alipay;
use Amap\WebService;
use think\Controller;
use Wechat\WechatMenu;
use Wechat\WechatOauth;
use Wechat\WechatPay;
use Wechat\WechatUser;

class Pay extends Controller
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 微信扫码支付
     */
    public function scanQr()
    {
        vendor("phpqrcode.phpqrcode");
        $wxPay = new WechatPay();
        $result = $wxPay->getQrcPayUrl('测试', '2015478517781254333412', 1, 'http://www.baidu.com');
        echo \QRcode::png($result);exit;
   }

    /**
     * 微信订单查询
     */
    public function queryOrder()
    {
        $wxPay = new WechatPay();
        $result = $wxPay->queryOrder('20154785171254122');
        var_dump($result);
    }

    /**
     * 微信订单退款
     */
    public function refund()
    {
        $wxPay = new WechatPay();
        $result = $wxPay->refund('20154785171254122', '4004042001201708146185283281' , '5151951981891981', '1', '1');
        var_dump($result);
    }

    /**
     * 微信企业付款
     */
    public function transfers()
    {
        $wxPay = new WechatPay();
        $result = $wxPay->transfers('oZcUCwQe_LF73EbQ9eTkOs55wYJY', 1, '2015478517125412222','dd');
        var_dump($result);
    }



    /**
     * 支付宝转账
     */
    public function Alitransfer()
    {
        $aliPay = new alipay();
        $result = $aliPay->transfer('148489151561536441522','15088132389',0.1);
        var_dump($result);
    }


    /**
     * 支付宝转账查询
     */
    public function AliQueryTransfer()
    {
        $aliPay = new alipay();
        $result = $aliPay->queryTransfer('14848915156153333615','');
        var_dump($result);
    }
    /**
     * 支付宝即时到账
     */
    public function pay()
    {
        $aliPay = new alipay();
        $result = $aliPay->payOld('1484891515615333361544',0.1);
        echo $result;
    }
    /**
     * 支付宝条码支付
     */
    public function scene()
    {
        $bar_code = $this->request->param('bar_code');
        $aliPay = new alipay();
        $result = $aliPay->scene('14848915156165653333544', $bar_code,0.1);
        echo json_encode($result);
    }


}
