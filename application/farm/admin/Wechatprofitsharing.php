<?php

/**
 * Created by Sperk.
 * 微信支付下单带分账参数
 */
 
namespace app\farm\admin;
use app\common\controller\Common;

class Wechatprofitsharing extends Common{
    private $config = [
        'appid' => 'wx5375bc6d5a7a6227',
        'mch_id' => '1630175786',
        'api_key' => 'wcy15199004491mn1008612580666666',
        'serial_no' => '486D3A8AB689731A0107ABF798309913E381138A',
        'private_key_path' => __DIR__.'/cert/apiclient_key.pem',
        'cert_path' => __DIR__.'/cert/apiclient_cert.pem',
        'ca_path' => __DIR__.'/cert/cacert.pem'
    ];

    public function createOrder()
    {
        $params = [
            'appid' => $this->config['appid'],
            'mchid' => $this->config['mch_id'],
            'description' => input('msg'),
            'out_trade_no' => 'T'.date('YmdHis').mt_rand(1000,9999),
            'notify_url' => 'https://yourdomain.com/notify',
            'amount' => [
                'total' => intval(input('money')*100),
                'currency' => 'CNY'
            ],
            'settle_info' => [
                'profit_sharing' => true
            ],
            'payer' => [
                'openid' => input('open_id'),
            ]
        ];

        // 请求微信支付API
        $result = $this->requestWechatPay($params);
        
        // 生成前端支付参数
        $payParams = $this->generatePayParams($result['prepay_id']);
        
        // return json([
        //     'code' => 0,
        //     'data' => $payParams,
        //     'raw_response' => $result
        // ]);
        return $this->json_result($payParams, 200, '操作成功');
    }

    private function requestWechatPay($params) {
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $body = json_encode($params, JSON_UNESCAPED_UNICODE);
        $signature = $this->sign("POST\n/v3/pay/transactions/jsapi\n{$timestamp}\n{$nonceStr}\n{$body}\n");

        $headers = [
            'Accept: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'Content-Type: application/json',
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.
            'mchid="'.$this->config['mch_id'].'",'.
            'serial_no="'.$this->config['serial_no'].'",'.
            'nonce_str="'.$nonceStr.'",'.
            'timestamp="'.$timestamp.'",'.
            'signature="'.$signature.'"'
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => $this->config['ca_path']
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    private function generatePayParams($prepayId) {
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $package = "prepay_id=".$prepayId;
        $message = $this->config['appid']."\n".$timestamp."\n".$nonceStr."\n".$package."\n";
        $paySign = $this->sign($message);

        return [
            'appId' => $this->config['appid'],
            'timeStamp' => (string)$timestamp,
            'nonceStr' => $nonceStr,
            'package' => $package,
            'signType' => 'RSA',
            'paySign' => $paySign
        ];
    }

    private function sign($data) {
        $privateKey = openssl_pkey_get_private("file://".$this->config['private_key_path']);
        if(!$privateKey) die('私钥加载失败');
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    private function createNonceStr($length = 32) {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }
}
