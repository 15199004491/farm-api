<?php


namespace app\farm\model;
use think\Model as ThinkModel;

class ProfitSharing extends ThinkModel
{
    public static function loadPrivateKey($path, $passphrase = null) {
        $key = file_get_contents($path);
        return openssl_pkey_get_private($key, $passphrase);
    }
    public function post($url, $data, $headers = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * 请求分账
     */
    public function ProfitSharingService($config,$transactionId, $receivers) {

        $privateKey = self::loadPrivateKey(
            $config['private_key_path'],
            $config['api_v3_key']
        );
        
        
        $timestamp = time();
        $nonce = uniqid();
        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/orders';
        $body = json_encode([
            'transaction_id' => $transactionId,
            'out_order_no' => 'PS'.date('YmdHis').rand(100,999),
            'receivers' => $receivers
        ]);
        

        $sign = $this->generateSign($privateKey, $timestamp, $nonce, $body);
        
        
        $response = request()->post($url, $body, [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: ThinkPHP',
            'Authorization: WECHATPAY2-SHA256-RSA2048 '.$sign,
            'Wechatpay-Serial: '.$config['serial_no']
        ]);

        return json_decode($response, true);
    }
    /**
     * 查询分账结果
     * @param string $outOrderNo 商户分账单号
     * @return array
     */
    public function querySharing($config,$outOrderNo)
    {
        if(empty($outOrderNo)){
            return json(['code'=>400, 'msg'=>'缺少分账单号']);
        }
       

        $url = "https://api.mch.weixin.qq.com/v3/profitsharing/orders/{$outOrderNo}?mchid={$config['mch_id']}";
        
        $headers = [
            'Authorization: '.$this->buildAuthHeader('GET', $url,$config),
            'Accept: application/json',
            'Content-Type: application/json'
        ];
        

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLCERT, $config['cert_path']);
        curl_setopt($ch, CURLOPT_SSLKEY, $config['key_path']);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            return json(['code'=>500, 'msg'=>'请求失败: '.curl_error($ch)]);
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if(!isset($result['state'])){
            return json(['code'=>500, 'msg'=>'返回数据异常']);
        }

        // 更新数据库状态
        // Db::name('profit_sharing')
        //     ->where('out_order_no', $outOrderNo)
        //     ->update(['status' => $this->mapStatus($result['state'])]);

        return json(['code'=>200, 'data'=>$result]);
    }
    private function buildAuthHeader($method, $url,$config)
    {
        $timestamp = time();
        $nonceStr = uniqid();
        $message = "{$method}\n{$url}\n{$timestamp}\n{$nonceStr}\n\n";
        
        
        openssl_sign($message, $sign, file_get_contents($config['key_path']), 'sha256');
        $signature = base64_encode($sign);
        $serialNo = $this->getCertSerialNo($config);

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $config['mch_id'],
            $nonceStr,
            $timestamp,
            $serialNo,
            $signature
        );
    }
    private function getCertSerialNo($config)
    {
        $cert = file_get_contents($config['cert_path']);
        $certInfo = openssl_x509_parse($cert);
        return bin2hex($certInfo['serialNumber']);
    }
    private function generateSign($privateKey, $timestamp, $nonce, $body) {
        $message = "$timestamp\n$nonce\n$body\n";
        openssl_sign($message, $signature, $privateKey, 'sha256WithRSAEncryption');
        return base64_encode($signature);
    }
    // 生成微信V3接口认证
    private function generateAuth($url, $body,$config)
    {
        $nonce = uniqid();
        $timestamp = time();
        $message = "POST\n{$url}\n{$timestamp}\n{$nonce}\n{$body}\n";
        $signature = base64_encode(hash_hmac('sha256', $message, $config['api_key'], true));
        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $config['mch_id'],
            $nonce,
            $timestamp,
            $this->getSerialNo($config),
            $signature
        );
    }
    // 获取证书序列号
    private function getSerialNo($config)
    {
        $cert = file_get_contents($config['cert_path']);
        openssl_x509_read($cert);
        $info = openssl_x509_parse($cert);
        return $info['serialNumberHex'] ?? '';
    }
    // 带证书的POST请求
    private function postRequest($url, $data,$config)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: '.$this->generateAuth($url, $data, $config)
        ]);
        curl_setopt($ch, CURLOPT_SSLCERT, $config['cert_path']);
        curl_setopt($ch, CURLOPT_SSLKEY, $config['key_path']);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * 添加分账接收方
     */
    public function addReception($type,$account,$name,$config)
    {
        // 构造请求数据
        $data = [
            'appid' => $config['appid'],
            'type' => $type,
            'account' => $account,
            'name' => $this->encryptName($name,$config), // 姓名加密
            'relation_type' => 'SERVICE_PROVIDER' // 固定值
        ];
        // 调用微信接口
        $url = 'https://api.mch.weixin.qq.com/v3/profitsharing/receivers/add';
        $result = $this->postRequest($url, json_encode($data, JSON_UNESCAPED_UNICODE),$config);
        var_dump($result);
        exit();
        
        // 处理结果
        return json_decode($result, true);
    }
    // 姓名加密（微信要求）
    private function encryptName($name,$config)
    {
        $publicKey = file_get_contents($config['cert_path']);
        openssl_public_encrypt($name, $encrypted, $publicKey);
        return base64_encode($encrypted);
    }
}
