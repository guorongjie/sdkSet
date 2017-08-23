<?php
/**
 * Created by PhpStorm.
 * User: GUO
 * Date: 5/4/2017
 * Time: 5:10 PM
 */
namespace Wechat;
class WechatOauth extends Common {

    const OAUTH_PREFIX = 'https://open.weixin.qq.com/connect/oauth2';
    const OAUTH_AUTHORIZE_URL = '/authorize?';
    const OAUTH_TOKEN_URL = '/sns/oauth2/access_token?';
    const OAUTH_REFRESH_URL = '/sns/oauth2/refresh_token?';
    const OAUTH_USERINFO_URL = '/sns/userinfo?';
    const OAUTH_AUTH_URL = '/sns/auth?';

    /**
     * 通过网页授权获取openid(包括某些数据)
     * @return array|bool
     * 返回：openid, nickname, sex, head_pic, subscribe, [unionid]
     */
    public function getOpenid()
    {
        if(isset($_SESSION['openid'])) return $_SESSION['openid'];
        if (!isset($_GET['code'])){
            //触发微信返回code码
            //$baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
            $baseUrl = $this->get_url();//获取当前链接地址
            $url = $this->getOauthRedirect($baseUrl,"STATE","snsapi_userinfo"); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            //上面获取到code后这里跳转回来
            $code = $_GET['code'];
            $data = $this->getOauthAccessToken($code);//获取网页授权access_token和用户openid
            $data2 = $this->getOauthUserInfo($data['access_token'],$data['openid']);//获取微信用户信息
//            var_dump($data2);exit;
            $data['nickname'] = empty($data2['nickname']) ? '微信用户' : trim($data2['nickname']);
            $data['sex'] = $data2['sex'];
            $data['head_pic'] = $data2['headimgurl'];
            $data['subscribe'] = $data2['subscribe'];
            $_SESSION['openid'] = $data['openid'];//session openid
            $data['oauth'] = 'weixin';
            if(isset($data2['unionid'])){
                $data['unionid'] = $data2['unionid'];
            }
            return $data;
        }
    }

    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
    /**
     * Oauth 授权跳转接口
     * @param string $callback 授权回跳地址
     * @param string $state 为重定向后会带上state参数（填写a-zA-Z0-9的参数值，最多128字节）
     * @param string $scope 授权类类型(可选值snsapi_base|snsapi_userinfo)
     * @return string
     */
    public function getOauthRedirect($callback, $state = '', $scope = 'snsapi_base') {
        $redirect_uri = urlencode($callback);
        return self::OAUTH_PREFIX . self::OAUTH_AUTHORIZE_URL . "appid=$this->appid&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
    }


    /**
     * 通过 code 获取 AccessToken 和 openid
     * @return bool|array
     */
    public function getOauthAccessToken($code) {
        if (empty($code)) {
            return false;
        }
        $result = $this->httpRequest(self::API_BASE_URL_PREFIX . self::OAUTH_TOKEN_URL . "appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code",'GET');
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return $json;
            }
            return $json;
        }
        return false;
    }
    /**
     * 获取授权后的用户资料
     * @param string $access_token
     * @param string $openid
     * @return bool|array {openid,nickname,sex,province,city,country,headimgurl,privilege,[unionid]}
     * 注意：unionid字段 只有在用户将公众号绑定到微信开放平台账号后，才会出现。建议调用前用isset()检测一下
     */
    public function getOauthUserInfo($access_token, $openid) {
        $result = $this->httpRequest(self::API_BASE_URL_PREFIX . self::OAUTH_USERINFO_URL . "access_token={$access_token}&openid={$openid}" ,'GET');
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                return $json;

            }
            return $json;
        }
        return false;
    }
}