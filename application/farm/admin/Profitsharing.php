<?php

/**
 * Created by Sperk.
 * 微信支付控制器
 */
 
namespace app\farm\admin;
use app\common\controller\Common;
use think\facade\Request;

class Profitsharing extends Common{
    // 微信支付配置
    private $config = [
        'appid' => 'wx5375bc6d5a7a6227',  // 替换为你的APPID
        'mch_id' => '1630175786',         // 替换为你的商户号
        'key' => 'QWERT54321qwertyQWERT54321qwerty',  // 替换为你的API密钥
        'serial_no' => '486D3A8AB689731A0107ABF798309913E381138A', // 证书序列号
        'cert_path' => __DIR__.'/cert/apiclient_cert.pem',  // 证书绝对路径
        'key_path' => __DIR__.'/cert/apiclient_key.pem',    // 密钥绝对路径
        'platform_cert' => __DIR__.'/cert/cacert.pem' // 平台证书
    ];

   /**
     * 添加分账接收方
     */
    public function addReceiver()
    {
        $params = [
            'appid'         => $this->config['appid'],
            'type'          => 'MERCHANT_ID', // 接收方类型
            'account'       => '1718391689', // 替换为实际接收方账号
            'name'          => $this->encryptAesGcm('五家渠市丝柏凌应用软件开发中心(个体工商户)'), // 改用AES-GCM加密
            'relation_type' => 'SERVICE_PROVIDER'
        ];

        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/receivers/add';
        $result = $this->requestWechat($url, $params);

        if ($result['code'] == 200) {
            return json(['status' => 1, 'data' => $result['data']]);
        } else {
            return json(['status' => 0, 'msg' => $result['message']]);
        }
    }

    /**
     * 微信V3接口请求
     */
    private function requestWechat($url, $data)
    {
        $timestamp = time();
        $nonce_str = $this->createNonceStr();
        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sign = $this->createSign('POST', $url, $timestamp, $nonce_str, $body);

        $headers = [
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: ThinkPHP/WechatV3',
            'Wechatpay-Serial: '.$this->config['serial_no'],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code'    => $httpCode,
            'data'    => json_decode($response, true),
            'message' => $httpCode == 200 ? 'success' : $response
        ];
    }

    /**
     * 使用AES-GCM加密（替代RSA加密）
     */
    private function encryptAesGcm($data)
    {
        $iv = random_bytes(12);
        $ciphertext = openssl_encrypt(
            $data,
            'aes-256-gcm',
            $this->config['key'],
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        return base64_encode($iv.$ciphertext.$tag);
    }

    /**
     * 生成V3签名
     */
    private function createSign($method, $url, $timestamp, $nonce, $body)
    {
        $message = $method."\n".
            parse_url($url, PHP_URL_PATH)."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        $privateKey = file_get_contents($this->config['key_path']);
        openssl_sign($message, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return sprintf(
            'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->config['mch_id'],
            $nonce,
            $timestamp,
            $this->config['serial_no'],
            base64_encode($signature)
        );
    }

    /**
     * 生成随机字符串
     */
    private function createNonceStr($length = 32)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    // 2. 执行分账
    public function doSharing()
    {
        $data = input('post.');
        if(empty($data['transaction_id']) || empty($data['receivers'])){
            return json(['code'=>400,'msg'=>'缺少必要参数']);
        }
        $receivers = json_decode($data['receivers'],true);
        $wxData = [
            'appid' => $this->config['appid'],
            'mch_id' => $this->config['mch_id'],
            'transaction_id' => $data['transaction_id'],
            'out_order_no' => $data['out_order_no'] ?? date('YmdHis').rand(1000,9999),
            'receivers' => $this->formatReceivers($receivers)
        ];

        $result = $this->wxRequest(
            'https://api.mch.weixin.qq.com/secapi/pay/profitsharing',
            $wxData,
            true
        );

        if($result['code'] != 200){
            return json(['code'=>500,'msg'=>$result['message']]);
        }

        // Db::name('profit_sharing')->insert([
        //     'transaction_id' => $data['transaction_id'],
        //     'out_order_no' => $wxData['out_order_no'],
        //     'receivers' => json_encode($data['receivers']),
        //     'status' => 'PROCESSING',
        //     'create_time' => time()
        // ]);

        return json(['code'=>200,'msg'=>'分账请求已提交']);
    }

    // 3. 查询分账结果
    public function querySharing()
    {
        $data = input('post.');
        if(empty($data['transaction_id']) || empty($data['out_order_no'])){
            return json(['code'=>400,'msg'=>'缺少必要参数']);
        }

        $wxData = [
            'transaction_id' => $data['transaction_id'],
            'out_order_no' => $data['out_order_no']
        ];

        $result = $this->wxRequest(
            'https://api.mch.weixin.qq.com/v3/profitsharing/orders/'.$data['out_order_no'].'/query',
            $wxData
        );

        if($result['code'] != 200){
            return json(['code'=>500,'msg'=>$result['message']]);
        }

        // Db::name('profit_sharing')
        //     ->where('out_order_no', $data['out_order_no'])
        //     ->update(['status' => $result['status']]);

        return json(['code'=>200,'data'=>$result]);
    }
    // API方式查询分账标志
    public function checkByApi() {
        $transaction_id = Request::param('transaction_id');
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/id/'.$transaction_id;
        $timestamp = time();
        $nonce_str = $this->createNonceStr();
        $sign = $this->makeSign($timestamp, $nonce_str, 'GET', $url);
        
        $header = [
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.
            'mchid="'.$this->config['mch_id'].'",'.
            'nonce_str="'.$nonce_str.'",'.
            'timestamp="'.$timestamp.'",'.
            'serial_no="'.$this->config['serial_no'].'",'.
            'signature="'.$sign.'"',
            'Accept: application/json'
        ];
       
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        var_dump($result);
        exit();
        
        return isset($result['profit_sharing']) && $result['profit_sharing'] == 'Y';
    }


    // 微信请求公共方法
    private function wxRequest($url, $data, $useCert = false)
    {
        $timestamp = time();
        $nonce = md5(uniqid());
        $body = json_encode($data);
        $sign = $this->makeSign($timestamp, $nonce, $body);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.
                'mchid="'.$this->config['mch_id'].'",'.
                'nonce_str="'.$nonce.'",'.
                'timestamp="'.$timestamp.'",'.
                'serial_no="'.file_get_contents($this->config['platform_cert']).'",'.
                'signature="'.$sign.'"'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if($useCert){
            curl_setopt($ch, CURLOPT_SSLCERT, $this->config['cert_path']);
            curl_setopt($ch, CURLOPT_SSLKEY, $this->config['key_path']);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    // 生成签名
    private function makeSign($timestamp, $nonce, $body)
    {
        $message = $timestamp."\n".
                  $nonce."\n".
                  $body."\n";
        
        openssl_sign($message, $sign, 
            file_get_contents($this->config['key_path']), 
            OPENSSL_ALGO_SHA256);
            
        return base64_encode($sign);
    }

    // 加密敏感数据
    private function encryptData($data)
    {
        $publicKey = file_get_contents($this->config['platform_cert']);
        openssl_public_encrypt($data, $encrypted, $publicKey);
        return base64_encode($encrypted);
    }

    // 格式化接收方数据
    private function formatReceivers($receivers)
    {
        return array_map(function($item){
            return [
                'type' => $item['type'],
                'account' => $item['account'],
                'amount' => (int)($item['amount'] * 100),
                'description' => $item['desc'] ?? '分账'
            ];
        }, $receivers);
    }
}
