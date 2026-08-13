<?php

/**
 * Created by Sperk.
 * 微信下单分账
 */
 
namespace app\farm\admin;

use app\common\controller\Common;
use think\facade\Db;
use think\facade\Request;

class Wxprofitsharingpay extends Common{
    // 微信支付配置
    private $config = [
        'appid' => 'wx5375bc6d5a7a6227',
        'mch_id' => '1630175786',
        'key' => '1a2s3d4f5g1a2s3d4f5g1a2s3d4f5g1a',
        'cert_path' => __DIR__.'/apiclient_cert.pem',
        'key_path' => __DIR__.'/apiclient_key.pem',
        'notify_url' => 'http://www.ctz.cn/wxpay.php'
    ];

    // 统一下单接口
    public function createOrder()
    {
        $open_id = Request::param('open_id');
        
        $params = [
            'appid' => $this->config['appid'],
            'mch_id' => $this->config['mch_id'],
            'nonce_str' => $this->createNonceStr(),
            'body' => '测试商品',
            'out_trade_no' => 'ORDER'.date('YmdHis').mt_rand(1000,9999),
            'total_fee' => 100, // 单位：分
            'spbill_create_ip' => Request::ip(),
            'notify_url' => $this->config['notify_url'],
            'trade_type' => 'JSAPI',
            'openid' => $open_id,
            'profit_sharing' => 'Y' // 标记需要分账
        ];

        $params['sign'] = $this->makeSign($params);
        
        $result = $this->postXmlCurl($this->arrayToXml($params), 
                    'https://api.mch.weixin.qq.com/pay/unifiedorder');
      
        $result = $this->xmlToArray($result);

        if($result['return_code'] == 'SUCCESS'){
            // 记录订单到数据库
            // Db::name('orders')->insert([
            //     'order_no' => $params['out_trade_no'],
            //     'amount' => $params['total_fee'] / 100,
            //     'status' => 0,
            //     'create_time' => time()
            // ]);
            return json($result);
        }
        return json(['code'=>500, 'msg'=>'下单失败']);
    }

    // 支付回调处理
    public function notify()
    {
        $xml = file_get_contents('php://input');
        $data = $this->xmlToArray($xml);
        
        if($data['return_code'] == 'SUCCESS' && $this->verifySign($data)){
            // 更新订单状态
            // Db::name('orders')
            //     ->where('order_no', $data['out_trade_no'])
            //     ->update([
            //         'status' => 1,
            //         'transaction_id' => $data['transaction_id'],
            //         'pay_time' => time()
            //     ]);
            
            // 自动触发分账
            if(isset($data['profit_sharing']) && $data['profit_sharing'] == 'Y'){
                $this->profitSharing($data['transaction_id']);
            }
            
            echo '<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>';
            exit;
        }
    }

    // 执行分账
    private function profitSharing($transactionId)
    {
        $params = [
            'appid' => $this->config['appid'],
            'mch_id' => $this->config['mch_id'],
            'nonce_str' => $this->createNonceStr(),
            'transaction_id' => $transactionId,
            'out_order_no' => 'SHARE'.date('YmdHis'),
            'receivers' => json_encode([[
                'type' => 'PERSONAL_OPENID',
                'account' => '分账接收方openid',
                'amount' => 30, // 分账金额(分)
                'description' => '商品销售分账'
            ]]),
            'sign_type' => 'HMAC-SHA256'
        ];
        
        $params['sign'] = $this->makeSign($params);
        $result = $this->postXmlCurl(
            $this->arrayToXml($params),
            'https://api.mch.weixin.qq.com/secapi/pay/profitsharing'
        );
        return $this->xmlToArray($result);
    }

    // 以下为工具方法
    private function makeSign($params){
        ksort($params);
        $string = '';
        foreach($params as $k => $v){
            if($k != 'sign' && $v != ''){
                $string .= $k . '=' . $v . '&';
            }
        }
        $string = rtrim($string, '&');
        return strtoupper(hash_hmac('sha256', $string, $this->config['key']));
    }

    private function verifySign($data){
        $sign = $data['sign'];
        unset($data['sign']);
        return $sign == $this->makeSign($data);
    }

    private function postXmlCurl($xml, $url){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(stripos($url,'https://') !== false){
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSLCERT, $this->config['cert_path']);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->config['key_path']);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function arrayToXml($arr){
        
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if(is_array($val)){
                $xml .= "<$key>".$this->arrayToXml($val)."</$key>";
            }else{
                $xml .= "<$key><![CDATA[$val]]></$key>";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function xmlToArray($xml){
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    private function createNonceStr($length = 32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++){
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}
