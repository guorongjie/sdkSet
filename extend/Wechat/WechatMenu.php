<?php
/**
 * Created by PhpStorm.
 * User: GUO
 * Date: 2017/5/6
 * Time: 9:50
 */
namespace Wechat;

class WechatMenu extends Common {

    /** 创建自定义菜单 */
    const MENU_ADD_URL = '/menu/create?';
    /* 获取自定义菜单 */
    const MENU_GET_URL = '/menu/get?';
    /* 删除自定义菜单 */
    const MENU_DEL_URL = '/menu/delete?';

    /** 添加个性菜单 */
    const COND_MENU_ADD_URL = '/menu/addconditional?';
    /* 删除个性菜单 */
    const COND_MENU_DEL_URL = '/menu/delconditional?';
    /* 测试个性菜单 */
    const COND_MENU_TRY_URL = '/menu/trymatch?';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * 创建自定义菜单
     * @param json $data 菜单数组数据
     * @param bool $debug 是否输出错误信息
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013&token=&lang=zh_CN 文档
     * @return bool/string
     */
    public function createMenu($data, $debug = true) {
        if (!$this->access_token && !$this->get_access_token()) {
            return false;
        }
        $result = $this->httpRequest('https://api.weixin.qq.com/cgi-bin/menu/create?' . "access_token={$this->access_token}",'POST', $data);
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                if ($debug) {
                    return $json['errcode'] . ' ' . $json['errmsg'] ;
                }
                return false;
            }
            return true;
        }
        return false;
    }
}