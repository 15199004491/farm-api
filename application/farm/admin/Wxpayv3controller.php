<?php

/**
 * Created by Sperk.
 * 微信支付下单/分账
 */
 
namespace app\farm\admin;
use app\common\controller\Common;
use think\facade\Request;

class Wxpayv3controller extends Common{
    // 配置参数（需替换实际值）
    private $config = [
        'appid' => 'wx5375bc6d5a7a6227',  // 小程序/公众号APPID
        'mch_id' => '1630175786',         // 商户号
        'api_v3_key' => 'QWERT54321qwertyQWERT54321qwerty', // APIv3密钥
        'serial_no' => '486D3A8AB689731A0107ABF798309913E381138A', // 证书序列号
        'private_key_path' => __DIR__.'/cert/apiclient_key.pem', // 私钥文件路径
        'cert_path' => __DIR__.'/cert/apiclient_cert.pem', // 证书文件路径
        'ca_path' => __DIR__.'/cert/cacert.pem', // 根证书路径
        'notify_url' => 'https://yourdomain.com/notify' // 支付回调地址
    ];

    // 初始化验证
    private function init()
    {
        // 验证证书文件
        foreach (['private_key_path', 'cert_path', 'ca_path'] as $key) {
            if (!file_exists($this->config[$key])) {
                throw new \Exception("证书文件不存在: ".$this->config[$key]);
            }
            if (!is_readable($this->config[$key])) {
                throw new \Exception("证书不可读: ".$this->config[$key]);
            }
        }

        // 验证私钥
        $privateKey = file_get_contents($this->config['private_key_path']);
        if (!openssl_pkey_get_private($privateKey)) {
            throw new \Exception("私钥无效: ".openssl_error_string());
        }
    }

    // 创建支付订单（含分账）
    public function createOrder()
    {
        $this->init();
        $orderNo = 'PS'.date('YmdHis').mt_rand(1000,9999);
        $amount = 10; // 单位：分

        // 1. 创建支付订单
        $prepayId = $this->createPayment($orderNo, $amount);

        // 3. 返回JSAPI支付参数
        return $this->json_result($this->buildJsParams($prepayId), 200, '操作成功');
    }

    // 创建支付订单
    private function createPayment($orderNo, $amount)
    {
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        $params = [
            'appid' => $this->config['appid'],
            'mchid' => $this->config['mch_id'],
            'description' => '分账订单',
            'out_trade_no' => 'ORDER'.date('YmdHis'),
            'notify_url' => 'https://yourdomain.com/notify',
            'amount' => [
                'total' => $amount,
                'currency' => 'CNY'
            ],
            'settle_info' => [
                'profit_sharing' => true
            ],
            'payer' => [
                'openid' => Request::param('open_id'),
            ]
        ];

        $result = $this->apiRequest($url, $params);
        if(empty($result['prepay_id'])){
            throw new \Exception('获取prepay_id失败: '.json_encode($result));
        }
        return $result['prepay_id'];
    }

    // 执行分账
    public function profitSharing()
    {
        $url = '/v3/profitsharing/orders';
        $data = [
            'appid' => $this->config['appid'],
            'transaction_id' => 'WX_TRANSACTION_ID',
            'out_order_no' => 'SHARE'.date('YmdHis'),
            'receivers' => [
                [
                    'type' => 'MERCHANT_ID',
                    'account' => 'RECEIVER_MCHID',
                    'amount' => 10,
                    'description' => '分账测试'
                ]
            ]
        ];

        $result = $this->apiRequest('POST', $url, $data);
        return json($result);
    }

    // 构建JSAPI参数
    private function buildJsParams($prepayId)
    {
        $params = [
            'appId' => $this->config['appid'],
            'timeStamp' => (string)time(),
            'nonceStr' => $this->generateNonceStr(),
            'package' => "prepay_id=$prepayId",
            'signType' => 'RSA'
        ];

        $privateKey = file_get_contents($this->config['private_key_path']);
        $signature = $this->generateSignature($params, $privateKey);
        $params['paySign'] = $signature;

        return $params;
    }

    // 生成签名
    private function generateSignature($params, $privateKey)
    {
        $signContent = sprintf("%s\n%s\n%s\n%s\n",
            $params['appId'],
            $params['timeStamp'],
            $params['nonceStr'],
            $params['package']
        );

        if (!openssl_sign($signContent, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('签名生成失败: '.openssl_error_string());
        }

        return base64_encode($signature);
    }

    // API请求
    private function apiRequest($url, $data, $method = 'POST')
    {
        $timestamp = time();
        $nonce = $this->generateNonceStr();
        $body = $method === 'POST' ? json_encode($data, JSON_UNESCAPED_UNICODE) : '';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: '.$this->buildAuthHeader($url, $body, $timestamp, $nonce),
                'User-Agent: ThinkPHP-WxPayV3'
            ],
            CURLOPT_POST => $method === 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => $this->config['ca_path'],
            CURLOPT_TIMEOUT => 15
        ]);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);

        if ($errno) {
            throw new \Exception("API请求失败[{$errno}]: {$error}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("API响应异常[HTTP {$httpCode}]: {$response}");
        }

        return json_decode($response, true) ?: [];
    }

    // 构建Authorization头
    private function buildAuthHeader($url, $body, $timestamp, $nonce)
    {
        $urlParts = parse_url($url);
        $path = $urlParts['path'] ?? '/';
        $query = $urlParts['query'] ?? '';
        $signUrl = $query ? "$path?$query" : $path;

        $signContent = sprintf("%s\n%s\n%d\n%s\n%s\n",
            'POST',
            $signUrl,
            $timestamp,
            $nonce,
            $body
        );

        $privateKey = file_get_contents($this->config['private_key_path']);
        if (!openssl_sign($signContent, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new \Exception('签名头生成失败: '.openssl_error_string());
        }

        $signature = base64_encode($signature);
        
        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->config['mch_id'],
            $nonce,
            $timestamp,
            $this->config['serial_no'],
            $signature
        );
    }
    // 生成随机字符串
    private function generateNonceStr($length = 32)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
}
