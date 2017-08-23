<?php
/**
 * Created by PhpStorm.
 * User: GUO
 * Date: 2017/8/22
 * Time: 14:19
 */
namespace Wechat;
class Wechat {
    /**
     * @var string|WechatReceive
     * 这里作为微信公众号的消息入口（包括绑定入口）。用户在公众号发送的消息，微信服务器会推送到这里（index）.这个类可迁到开发的请求控制器中
     */
    private $WechatReceive = '';//消息实例
    private $WechatOauth = '';//授权实例
    private $FromUserName = '';//消息发送者
    private $ToUserName = '';  //消息接受者
    private $MsgType = '';  //接收消息的类型
    private $CreateTime = '';  //接收消息的时间
    private $Keyword = '';  //接收消息的值

    public function __construct()
    {
//        parent::__construct();
        $this->WechatReceive = new WechatReceive();
        $this->WechatOauth = new WechatOauth();
    }
    public function index()
    {
        if (isset($_GET["echostr"])) exit($_GET["echostr"]);

        $postObj = $this->WechatReceive->getRev();//获取微信服务器发来的内容

        $this->FromUserName = $this->WechatReceive->getRevFrom();//消息发送者
        $this->ToUserName = $this->WechatReceive->getRevTo();//消息接受者
        $this->CreateTime = $this->WechatReceive->getRevCtime();//接收消息的时间
        $this->MsgType = $this->WechatReceive->getRevType();//接收消息的类型

        $this->Keyword = trim($this->WechatReceive->getRevContent());//接收消息的值

        /*
                * 1、click：点击推事件
                * 用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event的结构给开发者（参考消息接口指南）
                * 并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；
                */
        if ($this->MsgType == 'event' && $postObj->Event == 'CLICK') {
            $this->Keyword = trim($this->WechatReceive->getRevEvent());//接收消息的值
        }


        //回复文本消息
        $map['keyword'] = $this->Keyword;
//        $wx_text = Db::name('wx_text')->where($map)->find();
         $wx_text='你好';
        if ($wx_text) {
            $this->WechatReceive->sendText($wx_text['text'], $this->FromUserName, $this->ToUserName);
        }
        //回复图文消息
//        $wx_img = Db::name('wx_img')->where($map)->find();
        $wx_img = array('title'=>'你好','desc'=>'123445','pic'=>'url');
        if ($wx_img) {
            $this->WechatReceive->sendImg($this->FromUserName, $this->ToUserName, $wx_img['title'], $wx_img['desc'], $wx_img['pic'], $wx_img['url']);
        }

        //匹配其他输入
        if ($this->Keyword == 'what') {

        }
        //发送客服消息（文本）
        if ($this->Keyword == 'ggg') {
            $data = array(
                'touser' => $this->FromUserName,
                'msgtype' => 'text',
                'text' => array('content' => 'hhh')
            );
            $result = $this->WechatReceive->sendCustomMessage($data);
        }
        //发送模板消息
        if ($this->Keyword == '15') {
            $this->WechatReceive->senTempBonus('', 'http://www.baidu.com', array('value' => '你的组织成功消费一笔订单', 'color' => '#173177'), '1', '2017-5-5', '到账商城余额', '2017-5-5',array('value' => '你的组织成功消费一笔订单', 'color' => '#173177'));
        }
        if ($this->Keyword == '16') {
            $this->WechatReceive->senTempOrderPay('', 'http://www.baidu.com', array('value' => '恭喜你支付成功！', 'color' => '#173177'), '1', '2015555', array('value' => '备注', 'color' => '#173177'));
        }
        if ($this->Keyword == '17') {
            $this->WechatReceive->senTempOrderStatus('', 'http://www.baidu.com', array('value' => '订单已发货！', 'color' => '#173177'), '55656165', '发货', array('value' => '备注', 'color' => '#173177'));
        }
        if ($this->Keyword == '18') {
            $this->WechatReceive->senTempRefund('', 'http://www.baidu.com', array('value' => '退款成功！', 'color' => '#173177'), '达大厦', '5.00', array('value' => '备注', 'color' => '#173177'));
        }

        //发送模板消息
        if ($this->Keyword == '20') {
            $res1  = $this->WechatReceive->setTMIndustry('10', '11');
            file_put_contents('g22.txt', json_encode($res1));

            $res = $this->WechatReceive->getTemplateList();
            file_put_contents('g11.txt', json_encode($res));
        }
    }
}