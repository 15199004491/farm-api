<?php

namespace app\farm\admin;

use app\common\controller\Common;
use think\facade\Request;
use app\farm\model\ProfitSharing as ProfitSharingModel;


class Profitsharing extends Common{
    /**
     * 单次分账
     */
    public function singleSharing()
    {
        $config = [
            'appid' => 'wx5375bc6d5a7a6227',
            // 'mch_id' => '1630175786', 
            // 'sub_mch_id' => '1718391689',
            'mch_id' => '1630175786', 
            'sub_mch_id' => '1718391689',
            'nonce_str' => $this->createNonceStr(),
            'transaction_id' => '4200002676202506070856748775',
            'out_order_no' => 'PS'.date('YmdHis').rand(100,999),
            'api_v3_key' => 'QWERT54321qwertyQWERT54321qwerty',
            'serial_no' => '1DD92AAA1CFCC3A63605C84EC6E7740C63D2D267',
            'cert_path'  => __DIR__.'/apiclient_cert.pem',
            'private_key_path'  => __DIR__.'/apiclient_key.pem',
            'receivers' => json_encode(json_decode(Request::param('receivers'))),
        ];
        // $config,$transactionId, $receivers
        $transactionId = $config['transaction_id'];
        $receivers = Request::param('receivers');
        $ProfitSharingModel = new ProfitSharingModel;
        $result = $ProfitSharingModel->ProfitSharingService($config,$transactionId,$receivers);
        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 查询分账
     */
    public function querySharing() {
        $config = [
            'mch_id' => '1630175786', 
            'cert_path' => __DIR__.'\apiclient_cert.pem',
            'key_path' => __DIR__.'\apiclient_key.pem',
        ];
        $outOrderNo = Request::param('out_order_no');
        $ProfitSharingModel = new ProfitSharingModel;
        $result = $ProfitSharingModel->querySharing($config,$outOrderNo);
        return $this->json_result($result, 200, '操作成功');
    }
     /**
     * 添加分账接收方
     * @param string $type 接收方类型 MERCHANT_ID|PERSONAL_OPENID
     * @param string $account 接收方账号
     * @param string $name 接收方名称（需加密）
     */
    public function addReceiver($type, $account, $name)
    {
        $config = [
            'appid' => 'wx5375bc6d5a7a6227',
            'cert_path' => __DIR__.'\apiclient_cert.pem',
            'key_path' => __DIR__.'\apiclient_key.pem',
            'api_key' => 'QWERT54321qwertyQWERT54321qwerty',
            'mch_id' => '1630175786'
        ];
        $ProfitSharingModel = new ProfitSharingModel;
        $result = $ProfitSharingModel->addReception($type,$account,$name,$config);
        return $this->json_result($result, 200, '操作成功');
    }
    // 姓名加密（微信要求）
    private function encryptName($name)
    {
        $publicKey = file_get_contents(__DIR__.'\apiclient_cert.pem');
        openssl_public_encrypt($name, $encrypted, $publicKey);
        return base64_encode($encrypted);
    }
    /**
     * 生成随机字符串
     */
    private function createNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
