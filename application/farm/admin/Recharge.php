<?php

/**
 * Created by Sperk.
 * 微信支付控制器
 */
 
namespace app\farm\admin;
use app\common\controller\Common;
use app\farm\model\Recharge as RechargeModel;
use think\facade\Cache;


class Recharge extends Common{

    private $app_id = 'wx5375bc6d5a7a6227';                                                      //Your appid
    private $mch_id = '1630175786';        //Your 商户号
    private $api_v3_key = 'QWERT54321qwertyQWERT54321qwerty';     // APIv3密钥     
    private $serial_no = '599DFD78309F7CC5FC1707CA90B1FD65C2E4B1FC';//商户证书序列号
    private $platform_serial = '1DD92AAA1CFCC3A63605C84EC6E7740C63D2D267'; //平台证书序列号
    // private $key_path = '../config/apiclient_key.pem';
    // private $platform_cert = '../config/apiclient_cert.pem';
                                          
    private $makesign = '1a2s3d4f5g1a2s3d4f5g1a2s3d4f5g1a';                                                    //Your API支付的签名(在商户平台API安全按钮中获取)
    private $parameters=NULL;
    private $notify='http://www.ctz.cn/wxpay.php';                             //配置回调地址(给pays中转文件上传到根目录下面)
    private $app_secret='f946359b33b372d190c2d9be6e2cb213';                                                    //Your appSecret 微信官方获取
    public $error = 0;
    public $orderid = null;
    public $openid = '';
	
    //进行微信支付
    public function wxPay(){

        $data = $this->request->param();

        $reannumb = $this->randomkeys(6);  //生成随机数 以后可以当做 订单号
        $pays = $data['money'];                        //获取需要支付的价格·
		
        #插入语句书写的地方
        $conf = $this->payconfig('Bm'.$reannumb,$pays * 100, $data['msg'],$data['open_id']);
        if (!$conf || $conf['return_code'] == 'FAIL') exit("<script>alert('对不起，微信支付接口调用错误!" . $conf['return_msg'] . "');history.go(-1);</script>");
		$this->orderid = $conf['prepay_id'];
        $data['prepay_id'] = $conf['prepay_id'];
        //微信相关配置如果不正的话，进入支付页面会出现错误信息

	   //生成页面调用参数
        $jsApiObj["appId"] = $conf['appid'];
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=" . $conf['prepay_id'];
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->MakeSign($jsApiObj);
        // 创建订单
        RechargeModel::insertGetId($data);
        return $this->json_result($jsApiObj, 200, '操作成功');
    }
    /**
     * Single profit sharing.
     * 请求单次分账.
     *
     * @param string $transactionId 微信支付订单号
     * @param string $outOrderNo    商户系统内部的分账单号
     * @param array  $receivers     分账接收方列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wxShare(
        string $transactionId,
        string $outOrderNo,
        array $receivers
    ) {
        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/orders';
        $data = [
            'appid' => $this->app_id,
            'mch_id' => $this->mch_id,
            'transaction_id' => $transactionId,
            'out_order_no' => 'SH' . date('YmdHis') . rand(1000,9999),
            'receivers' => json_encode($receivers),
            'total_amount' => 0.1,
            'nonce_str' => $this->createNonceStr()
        ];
        $data['sign'] = $this->makeSign($data);

        $result = $this->postXmlCurl($this->arrayToXml($data), $url);
        return $this->xmlToArray($result);;
    }
    function xmlToArray($xmlString) {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) {
            // throw new Exception('XML解析失败: ' . libxml_get_last_error()->message);
            throw $this->error('XML解析失败: ' . libxml_get_last_error()->message);
        }
    
        $result = [];
        foreach ($xml->children() as $node) {
            $nodeData = [];
            
            // 处理节点属性
            if ($attributes = $node->attributes()) {
                foreach ($attributes as $attrName => $attrValue) {
                    $nodeData['@attributes'][$attrName] = (string)$attrValue;
                }
            }
    
            // 处理子节点
            if ($node->count() > 0) {
                $nodeData = array_merge($nodeData, $this->xmlToArray($node->asXML()));
            } else {
                $nodeData['value'] = (string)$node;
            }
    
            // 处理同名节点
            if (isset($result[$node->getName()])) {
                if (!isset($result[$node->getName()][0])) {
                    $result[$node->getName()] = [$result[$node->getName()]];
                }
                $result[$node->getName()][] = $nodeData;
            } else {
                $result[$node->getName()] = $nodeData;
            }
        }
        
        return $result;
    }
    function postXmlCurl($xml, $url, $useCert = false, $sslCertPath = '', $sslKeyPath = '', $timeout = 30) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml',
            'Accept: application/xml'
        ]);
    
        // 双向证书配置
        if ($useCert) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $sslKeyPath);
        }
    
        // 跳过证书验证（仅测试环境使用）
        if (strpos($url, 'sandbox') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }
    
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // throw new Exception('CURL error: ' . curl_error($ch));
            throw $this->error('CURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
    function arrayToXml($data, &$xml = null, $parentNodeName = null) {
        if ($xml === null) {
            $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root/>');
        }
    
        foreach ($data as $key => $value) {
            // 处理数字索引节点（自动转为<item>）
            if (is_numeric($key)) {
                $key = $parentNodeName ?: 'item';
            }
    
            // 处理带属性的节点
            if (is_array($value) && isset($value['@attributes'])) {
                $node = $xml->addChild($key);
                foreach ($value['@attributes'] as $attr => $val) {
                    $node->addAttribute($attr, $val);
                }
                unset($value['@attributes']);
               $this->arrayToXml($value, $node, $key);
            } 
            // 处理CDATA内容
            elseif (is_array($value) && isset($value['@cdata'])) {
                $node = $xml->addChild($key);
                $dom = dom_import_simplexml($node);
                $dom->appendChild($dom->ownerDocument->createCDATASection($value['@cdata']));
            }
            // 处理嵌套数组
            elseif (is_array($value)) {
                $node = $xml->addChild($key);
                arrayToXml($value, $node, $key);
            }
            // 普通值
            else {
                $xml->addChild($key, htmlspecialchars($value));
            }
        }
        
        return $xml->asXML();
    }

    //订单管理
    #微信JS支付参数获取#
    protected function payconfig($no, $fee, $body, $open_id)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $data['appid'] = $this->app_id;
        $data['mch_id'] = $this->mch_id;                       //商户号
        $data['profit_sharing'] = 'Y';
        $data['device_info'] = 'WEB';
        $data['body'] = $body;
        $data['out_trade_no'] = $no;                           //订单号
        $data['total_fee'] = $fee;                             //金额
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];   //ip地址
        $data['notify_url'] = $this->notify;
        $data['trade_type'] = 'JSAPI';
        if(!$open_id) return;
        $data['openid'] = $open_id;                 //获取保存用户的openid
        $data['nonce_str'] = $this->createNoncestr();
        $data['sign'] = $this->MakeSign($data);
		
        $xml = $this->ToXml($data);
        $curl = curl_init(); // 启动一个CURL会话
		
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		
        //设置header
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
		
        //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        $arr = $this->FromXml($tmpInfo);
        return $arr;
    }

    /**
     *    作用：产生随机字符串，不长于32位
     */
    public function createNoncestr($length = 32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public function FromXml($xml)
    {
        //将XML转为array
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public function ToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    protected function MakeSign($arr)
    {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        $string = $this->ToUrlParams($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->makesign;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    protected function ToUrlParams($arr)
    {
        $buff = "";
        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

}