<?php
namespace app\wechat\controller;

use think\Controller;
use Wechat\WechatMenu;
use Wechat\WechatOauth;
use Wechat\WechatUser;

class Index extends Controller
{
    public function index(){
        return $this->fetch();
    }

    /**
     * 获取公众号access_token
     */
    public function getAccessToken()
    {
        $wechatOauth = new WechatOauth();
        $accessToken = $wechatOauth->get_access_token();
        echo $accessToken;
    }

    /**
     * 生成公众号菜单
     */
    public function createMenu()
    {
        $data = //测试数据
            '{
            "button":[{	
               "type":"click",
                "name":"今日歌曲2",
               "key":"V1001_TODAY_MUSIC"
             },
            {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
           
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
             }]
             }';
        $wechatMenu = new WechatMenu();
        $result = $wechatMenu->createMenu($data);
        echo $result;
    }

    /**
     * 批量获取关注粉丝列表
     */
    public function getUserLists()
    {
        $wechatUser = new  WechatUser();
        $result = $wechatUser->getUserList();
        dump($result);
    }

    public function location()
    {
        $wechatUser = new  WechatUser();
        $result = $wechatUser->location('113.323916', '23.089716');
        $result = json_decode($result,true);
        dump($result['regeocode']['addressComponent']['city']);
    }

}
