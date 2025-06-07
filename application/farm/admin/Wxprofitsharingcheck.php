<?php
namespace app\farm\admin;
use think\facade\Request;
use app\common\controller\Common;

class Wxprofitsharingcheck extends Common{
    // 微信支付配置
    private $config = [
        'appid'      => 'wx5375bc6d5a7a6227',
        'mch_id'     => '1630175786',
        'key'        => '1a2s3d4f5g1a2s3d4f5g1a2s3d4f5g1a',//api支付的签名
        'cert_path'  => __DIR__.'/apiclient_cert.pem',
        'key_path'   => __DIR__.'/apiclient_key.pem'
    ];
    /**
     * 查询订单是否为分账订单
     * @param string $transaction_id 微信支付订单号
     * @return bool
     */
    public function isProfitSharingOrder() {
        // 构造查询参数
        $params = [
            'appid'          => $this->config['appid'],
            'mch_id'         => $this->config['mch_id'],
            'transaction_id' => Request::param('transaction_id'),
            'nonce_str'      => $this->createNonceStr()
        ];
        
        
        // 生成签名
        $params['sign'] = $this->makeSign($params);
        
        
        // 转换XML格式
        $xml = $this->arrayToXml($params);
        
        // 请求微信订单查询接口
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        
        $response = $this->postXmlCurl($xml, $url);
        
        $result = $this->xmlToArray($response);
        
        // 判断分账标记
        // return isset($result['profit_sharing']) && $result['profit_sharing'] == 'Y';
        return $this->json_result($result);
    }

    // 辅助方法：生成随机字符串
    private function createNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    // 辅助方法：生成签名
    private function makeSign($params) {
        ksort($params);
        $string = $this->toUrlParams($params);
        $string .= '&key=' . $this->config['key'];
        return strtoupper(md5($string));
    }

    // 辅助方法：数组转URL格式
    private function toUrlParams($params) {
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        return trim($buff, "&");
    }

    // 辅助方法：数组转XML
    private function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            $xml .= "<".$key.">".$val."</".$key.">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    // 辅助方法：XML转数组
    private function xmlToArray($xml) {
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    // 辅助方法：POST请求
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        
        if($useCert) {
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->config['cert_path']);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->config['key_path']);
        }
        
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}

// 使用示例：
// $checker = new WxOrderCheck();
// $isSharing = $checker->isProfitSharingOrder('微信支付订单号');
// var_dump($isSharing);
