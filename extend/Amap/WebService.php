<?php
/**
 * Created by PhpStorm.
 * User: GUO
 * Date: 2017/8/23
 * Time: 9:46
 */
namespace Amap;
class WebService extends Common{

    public $Webkey ='';//web服务应用的key
    /**ip转地址
     * @param $lng 经度
     * @param $lat 纬度
     * @return mixed
     */
    public function __construct()
    {
        $this->Webkey = amap_webservice_key();
    }
    public function location($lng, $lat)
    {
        $lng = trim($lng);
        $lat = trim($lat);
        $url = 'http://restapi.amap.com/v3/geocode/regeo?key=' . $this->Webkey . $this->Webkey.'&';
        $url = $url.'location='.$lng . ','. $lat;
        $result = $this->httpRequest($url);
        return json_decode($result, true);
    }
}