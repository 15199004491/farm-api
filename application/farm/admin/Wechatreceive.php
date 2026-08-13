<?php

/**
 * Created by Sperk.
 * 微信支付下单带分账参数
 */
 
namespace app\farm\admin;
use app\common\controller\Common;

class Wechatreceive extends Common{
    private $config = [
        'mch_id' => '1630175786',
        'appid' => 'wx5375bc6d5a7a6227',
        'api_v3_key' => 'wcy15199004491mn1008612580666666',
        'serial_no' => '486D3A8AB689731A0107ABF798309913E381138A',
        'platform_serial' => '1DD92AAA1CFCC3A63605C84EC6E7740C63D2D267',
        'private_key_path' => __DIR__.'/cert/apiclient_key.pem',
        'platform_cert_path' => __DIR__.'/cert/apiclient_cert.pem',
        'ca_cert_path' => __DIR__.'/cert/cacert.pem' // 新增CA证书路径
    ];

    public function addReceiver()
    {
        // 1. 准备请求数据（包含必填appid）
        $data = [
            'appid' => $this->config['appid'],
            'type' => 'MERCHANT_ID',
            'account' => input('account'),
            'name' => input('name'),
            'relation_type' => 'SERVICE_PROVIDER'
        ];

        // 2. 加载密钥（修复PEM格式问题）
        $privateKey = $this->loadPrivateKey();
        if(!$privateKey) die("密钥加载失败: ".openssl_error_string());

        // 3. 加密敏感字段（使用正确加载的平台公钥）
        $platformPublicKey = $this->loadPlatformPublicKey();
        if(!$platformPublicKey) die("平台证书加载失败");
        
        openssl_public_encrypt($data['account'], $encrypted, $platformPublicKey, OPENSSL_PKCS1_OAEP_PADDING);
        $data['account'] = base64_encode($encrypted);

        // 4. 生成签名和请求头（包含Wechatpay-Serial）
        $timestamp = time();
        $nonce = uniqid();
        $body = json_encode($data);
        $signature = $this->generateSignature('POST', '/v3/profitsharing/receivers', $timestamp, $nonce, $body, $privateKey);

        $headers = [
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.$signature,
            'Accept: application/json',
            'Content-Type: application/json',
            'Wechatpay-Serial: '.$this->config['platform_serial']
        ];

        // 5. 使用cURL发送请求（解决SSL证书问题）
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mch.weixin.qq.com/v3/profitsharing/receivers');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, $this->config['ca_cert_path']); // 设置CA证书
        
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            die('Curl error: '.curl_error($ch));
        }
        curl_close($ch);
        return json(json_decode($response, true));
    }

    private function loadPrivateKey()
    {
        $keyContent = file_get_contents($this->config['private_key_path']);
        if(strpos($keyContent, '-----BEGIN PRIVATE KEY-----') === false) {
            $keyContent = "-----BEGIN PRIVATE KEY-----\n".
                         chunk_split(base64_encode($keyContent), 64, "\n").
                         "-----END PRIVATE KEY-----\n";
        }
        return openssl_pkey_get_private($keyContent);
    }

    private function loadPlatformPublicKey()
    {
        $cert = file_get_contents($this->config['platform_cert_path']);
        if(strpos($cert, '-----BEGIN CERTIFICATE-----') === false) {
            $cert = "-----BEGIN CERTIFICATE-----\n".
                   chunk_split(base64_encode($cert), 64, "\n").
                   "-----END CERTIFICATE-----\n";
        }
        return openssl_pkey_get_public($cert);
    }

    private function generateSignature($method, $url, $timestamp, $nonce, $body, $privateKey)
    {
        $message = $method."\n".$url."\n".$timestamp."\n".$nonce."\n".$body."\n";
        openssl_sign($message, $signature, $privateKey, 'sha256WithRSAEncryption');
        return base64_encode($signature);
    }
}
