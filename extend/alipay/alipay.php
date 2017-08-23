<?php
/**
 * Created by PhpStorm.
 * User: GUO
 * Date: 2017/8/14
 * Time: 16:29
 */
namespace alipay;
/**
 * 即时到账，退款，退款查询，转账到支付宝，转账查询
 * 一、先配置appid和prikey
 * Class alipay
 * @package alipay
 *
 */
class alipay{
    private $_appid = '';
    private $_prikey = '';

    /**
     * 即时到账
     * @param $out_trade_no
     * @param $total_amount
     * @param $subject
     * @return mixed
     */
    public function pay($out_trade_no, $total_amount, $subject)
    {
        $url='https://openapi.alipay.com/gateway.do';
        //业务参数
        $biz_content = array('out_trade_no'=>$out_trade_no,'total_amount'=>$total_amount,'subject'=>$subject, 'product_code'=>'FAST_INSTANT_TRADE_PAY');
        ksort($biz_content);
        $biz_content =json_encode($biz_content);//转换为json
        //公共参数
        $data = array(
            'app_id'=>$this->_appid,
            'biz_content'=>$biz_content,
            'charset'=>'utf-8',
            'method'=>'alipay.trade.page.pay',
            'sign_type'=>'RSA',
            'timestamp'=>date('Y-m-d H:i:s',time()),
            'version'=>'1.0',
        );
        //获取签名
        $data['sign'] = $this->sign($data);
        //发送请求
        $result =  $this->curl($url,$data);

//        $result =  $this->httpRequest($url, 'POST', $data);
        //结果
//        $result = json_decode($result,true);

        return $result;
    }
    /**
     *  * App支付请求参数
     * @param $out_trade_no 订单号
     * @param $total_amount 金额
     * @param $notify_url 回调地址
     * @return bool|string 返回给app客户端的数据
     *
     */
    function appPay($out_trade_no, $total_amount, $notify_url){
        $method = 'alipay.trade.app.pay';//接口名称
        $charset='utf-8';  //商户网站使用编码格式
        $version = '1.0';
        $sign_type = 'RSA'; //签名方式
        $timestamp = date('Y-m-d H:i:s',time());

        //业务参数
        $subject = '购买商品';
        $product_code = 'QUICK_MSECURITY_PAY';
        $biz_content = array('product_code'=>$product_code, 'total_amount'=>$total_amount,'subject'=>$subject,'out_trade_no'=>$out_trade_no);
        $biz_content = json_encode($biz_content);

        //公共参数
        $data = array(
            'app_id'=>$this->_appid,
            'biz_content'=>$biz_content,
            'charset'=>$charset,
            'method'=>$method,
            'notify_url'=>$notify_url,
            'sign_type'=>$sign_type,
            'timestamp'=>$timestamp,
            'version'=>$version
        );

        //获取签名
        $data['sign'] = $this->sign($data);

        $result = $this->formatBizQueryParaMap($data, true);//按照key=value&key=value方式拼接字符串

        return $result;
    }

    /**
     * 退款
     * @param $out_trade_no 交易时的订单号
     * @param $refund_amount 本次退款的金额
     * @param $out_request_no 本次退款的单号
     * @return mixed
     */
    function refund($out_trade_no, $refund_amount, $out_request_no){
        $url='https://openapi.alipay.com/gateway.do';
        //业务参数
        $biz_content = array('out_trade_no'=>$out_trade_no,'refund_amount'=>$refund_amount,'out_request_no'=>$out_request_no);
        ksort($biz_content);
        $biz_content =json_encode($biz_content);//转换为json
        //公共参数
        $data = array(
            'app_id'=>$this->_appid,
            'biz_content'=>$biz_content,
            'charset'=>'utf-8',
            'method'=>'alipay.trade.refund',
            'sign_type'=>'RSA',
            'timestamp'=>date('Y-m-d H:i:s',time()),
            'version'=>'1.0',
        );
        //获取签名
        $data['sign'] = $this->sign($data);
        //发送请求
        $result =  $this->curl($url,$data);
        //结果
//        $result = json_decode($result,true);

        return $result;
    }

    /**
     * 单笔转账到支付宝账户
     * @param $out_biz_no 订单号
     * @param $payee_account 收款支付宝账号
     * @param $amount 金额
     * @return mixed
     */
    public function transfer($out_biz_no, $payee_account, $amount)
    {
        $url='https://openapi.alipay.com/gateway.do';
        //业务参数
        $biz_content = array('out_biz_no'=>$out_biz_no,'payee_account'=>$payee_account,'amount'=>$amount, 'payee_type'=>'ALIPAY_LOGONID');
        ksort($biz_content);
        $biz_content =json_encode($biz_content);//转换为json
        //公共参数
        $data = array(
            'app_id'=>$this->_appid,
            'biz_content'=>$biz_content,
            'charset'=>'utf-8',
            'method'=>'alipay.fund.trans.toaccount.transfer',
            'sign_type'=>'RSA',
            'timestamp'=>date('Y-m-d H:i:s',time()),
            'version'=>'1.0',
        );
        //获取签名
        $data['sign'] = $this->sign($data);
        //发送请求
        $result =  $this->curl($url,$data);

//        $result =  $this->httpRequest($url, 'POST', $data);
        //结果
        $result = json_decode($result,true);

        return $result;
    }

    /**查询转账订单
     * @param $out_biz_no 商户转账唯一订单号：发起转账来源方定义的转账单据ID。
     * @param $order_id 支付宝转账单据号   和商户转账唯一订单号不能同时为空
     * @return mixed
     */
    public function queryTransfer($out_biz_no='', $order_id='')
    {
        $url='https://openapi.alipay.com/gateway.do';
        //业务参数
        if($out_biz_no){
            $biz_content = array('out_biz_no'=>$out_biz_no);
        }else{
            $biz_content = array('order_id'=>$order_id);
        }
        ksort($biz_content);
        $biz_content =json_encode($biz_content);//转换为json
        //公共参数
        $data = array(
            'app_id'=>$this->_appid,
            'biz_content'=>$biz_content,
            'charset'=>'utf-8',
            'method'=>'alipay.fund.trans.order.query',
            'sign_type'=>'RSA',
            'timestamp'=>date('Y-m-d H:i:s',time()),
            'version'=>'1.0',
        );
        //获取签名
        $data['sign'] = $this->sign($data);
        //发送请求
        $result =  $this->curl($url,$data);
        //结果
        $result = json_decode($result,true);

        return $result;
    }

    /**
     * 利用公共参数来生成签名sign
     * @param $data 公共参数
     * @return string
     */
    function sign($data){
        $sing_waitting = $this->formatBizQueryParaMap($data, false);//按照key=value&key=value方式拼接的未签名原始字符串
    $prikey = $this->_prikey;
        $sign = $this->rsaSign($sing_waitting,$prikey);//利用商户私钥对待签名字符串进行签名
        return $sign;
    }




    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }
    /**
     * RSA签名
     * @param $data 待签名数据
     * @param $private_key 商户私钥字符串
     * return 签名结果
     */
    function rsaSign($data, $private_key) {
        //以下为了初始化私钥，保证在您填写私钥时不管是带格式还是不带格式都可以通过验证。
        $private_key=str_replace("-----BEGIN RSA PRIVATE KEY-----","",$private_key);
        $private_key=str_replace("-----END RSA PRIVATE KEY-----","",$private_key);
        $private_key=str_replace("\n","",$private_key);

        $private_key="-----BEGIN RSA PRIVATE KEY-----".PHP_EOL .wordwrap($private_key, 64, "\n", true). PHP_EOL."-----END RSA PRIVATE KEY-----";
        $res=openssl_get_privatekey($private_key);

        if($res)
        {
            openssl_sign($data, $sign,$res);
        }
        else {
            echo "您的私钥格式不正确!"."<br/>"."The format of your private_key is incorrect!";
            exit();
        }
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     *  作用：生成签名
     * @param int $type 微信密钥 1表示服务号商户 2表示开放平台商户
     */
    function getSign($Obj,$type=1)
    {
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".config('WX_KEY0'.$type);
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     * CURL请求
     * @param $url 请求url地址
     * @param null $postFields post数据数组
     * @return mixed
     */
    function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = "";
        $encodeArray = Array();
        $postMultipart = false;


        if (is_array($postFields) && 0 < count($postFields)) {

            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {

                    $postBodyString .= "$k=" . urlencode($this->characet($v, 'utf-8')) . "&";
                    $encodeArray[$k] = $this->characet($v, 'utf-8');
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                    $encodeArray[$k] = new \CURLFile(substr($v, 1));
                }

            }
            unset ($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }

        if ($postMultipart) {

            $headers = array('content-type: multipart/form-data;charset=utf-8;boundary=' . $this->getMillisecond());
        } else {

            $headers = array('content-type: application/x-www-form-urlencoded;charset=utf-8');
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {

            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($reponse, $httpStatusCode);
            }
        }

        curl_close($ch);
        return $reponse;
    }
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = 'UTF-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //              $data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }


        return $data;
    }
    function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }

    /**
     * CURL请求
     * @param $url 请求url地址
     * @param $method 请求方法 get post
     * @param null $postfields post数据数组
     * @param array $headers 请求header信息
     * @param bool|false $debug  调试开启 默认false
     * @return mixed
     */
    function httpRequest($url, $method="GET", $postfields = null, $headers = array(), $debug = false) {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);//版本
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");//在HTTP请求中包含一个"User-Agent: "头的字符串。
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);//将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);//启用时会发送一个常规的POST请求，类型为：application/x-www-form-urlencoded，就像表单提交的一样。
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);//需要获取的URL地址，也可以在curl_init()函数中设置
        if($ssl){
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
        curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $response;
    }
}