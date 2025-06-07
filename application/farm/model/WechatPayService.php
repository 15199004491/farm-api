<?php
namespace app\service;

use function openssl_x509_parse;
use function openssl_pkey_get_private;
use function openssl_pkey_get_public;
use function file_get_contents;
use think\facade\Config;
use WeChatPay\Builder;
use WeChatPay\Util\MediaUtil;
use WeChatPay\Formatter;
use Exception;

class WechatPayService
{
    private $instance;
    const KEY_TYPE_PRIVATE = 1;
    const KEY_TYPE_PUBLIC = 2;

    public function __construct()
    {
        $config = Config::get('wechat');
        // 加载商户私钥
        $merchantPrivateKey = self::from(self::loadPrivateKey($config['key_path']));

        // 构建微信支付实例
        $this->instance = self::factory([
            'mchid'      => $config['mch_id'],
            'serial'     => $this->getCertificateSerial($config['cert_path']), // 获取证书序列号
            'privateKey' => $merchantPrivateKey,
            'certs'      => $this->loadPlatformCerts($config['platform_cert_dir']),
            'secret'     => $config['v3_key'],
        ]);
    }
    // 工厂方法创建实例
    public static function factory(array $config) {
        return new self($config);
    }
    // 加载私钥文件
    public static function loadPrivateKey(string $filepath) {
        $content = file_get_contents($filepath);
        if ($content === false) {
            throw new \RuntimeException("Failed to read private key file");
        }
        return openssl_pkey_get_private($content);
    }
   
    // 从文件或字符串加载密钥
    public static function from($key, $type = self::KEY_TYPE_PRIVATE) {
        if (strpos($key, 'file://') === 0) {
            $key = file_get_contents(substr($key, 7));
        }

        $keyResource = $type === self::KEY_TYPE_PRIVATE 
            ? openssl_pkey_get_private($key)
            : openssl_pkey_get_public($key);

        if ($keyResource === false) {
            throw new \RuntimeException(openssl_error_string());
        }

        return new self($keyResource, $type);
    }

    /**
     * 获取商户证书序列号
     * @param string $certPath 证书路径
     * @return string
     */
    private function getCertificateSerial($certPath)
    {
        return PemUtil::parseCertificateSerialNo($certPath);
    }

    /**
     * 加载平台证书
     * @param string $dir 平台证书目录
     * @return array
     */
    private function loadPlatformCerts($dir)
    {
        $certs = [];
        $files = glob($dir . '*.pem');
        foreach ($files as $file) {
            $certs[] = PemUtil::loadCertificate($file);
        }
        return $certs;
    }

    /**
     * 执行分账
     * @param string $outOrderNo 商户分账单号
     * @param string $transactionId 微信支付订单号
     * @param array $receivers 分账接收方列表
     * @return array
     * @throws Exception
     */
    public function profitSharing($outOrderNo, $transactionId, array $receivers)
    {
        $config = Config::get('wechat');
        $json = [
            'appid' => $config['appid'],
            'out_order_no' => $outOrderNo,
            'transaction_id' => $transactionId,
            'receivers' => $receivers,
        ];

        try {
            $resp = $this->instance->v3->profitSharing->orders->post(['json' => $json]);
            return json_decode($resp->getBody(), true);
        } catch (\Exception $e) {
            throw new Exception('微信分账失败: '. $e->getMessage());
        }
    }
}
