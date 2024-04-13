<?php
namespace app\farm\model; // 命名空间，根据自己的项目路径来生成
use think\Model; // 引入tp框架的Model类
use think\Db; // 引入 tp 框架的Db类
use think\facade\Cache; // 引入 tp 框架的缓存类

class Wxuser extends Model {
	private $appId;
	private $appSecret;
	public $error;
	public $token;

	protected $resultSetType = "collection"; // 设置返回类型
	protected $autoWriteTimestamp = true; // 自动记录时间戳

	/**
	* Wxuser constructor
	* @param $appId
	* @param $appSecret
	*/
	public function __construct() {
		$this->appId = 'wx5375bc6d5a7a6227';
		$this->appSecret = 'f946359b33b372d190c2d9be6e2cb213';
	}
	/**
	* 用户登陆
	*/
	public function login($post) {
		// 微信登陆 获取session_key
		$session = $this->wxlogin($post["code"]);
		return $session;
	}

	/**
	* 微信登陆
	* @param $code
	* @return array|mixed
	* @throws \think\exception\DbException
	*/
	private function wxlogin($code) {
		// 获取当前小程序信息
		if (empty($this->appId) || empty($this->appSecret)) {
			$this->json_result([], 409, '请到 [后台-小程序设置] 填写appid 和 appsecret');
		}
		// 微信登录 (获取session_key)
        if (!$session = $this->sessionKey($code)) {
			$this->json_result([], 409, $this->error);
        }
        return $session;
	}

	 /**
     * 获取session_key
     * @param $code
     * @return array|mixed
     */
    public function sessionKey($code) {
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
         */
		$url = 'https://api.weixin.qq.com/sns/jscode2session';
        $result = json_decode(curl_post($url, [
            'appid' => $this->appId,
            'secret' => $this->appSecret,
            'grant_type' => 'authorization_code',
            'js_code' => $code
        ]),true);
        if (isset($result['errcode'])) {
            $this->error = $result['errmsg'];
            return false;
        }
        return $result;
	}
	 /**
     * 获取微信用户手机号
     */
    public static function getWxMobile($token,$code) {
		$url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber';
        $result = json_decode(curl_post($url, [
            'access_token' => $token,
            'code' => $code
		]),true);
        if (isset($result['errcode'])) {
			throwError($result['errmsg']);
			return false;
        }
        return $result;
    }
	
	/**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    private function token($openid) {
        return md5($openid . 'token_salt');
    }

	 /**
     * 获取token
     * @return mixed
     */
    public function getToken() {
        return $this->token;
    }

	/**
     * 自动注册用户
     * @param $open_id
     * @param $userInfo
     * @return mixed
     * @throws \think\exception\DbException
     */
    private function register($open_id, $nickName,$avatarUrl,$gender,$area,$mobile) {
		$userInfo['open_id'] = $open_id;
		$userInfo['nick_name'] = preg_replace('/[\xf0-\xf7].{3}/', '', $nickName);
        $userInfo['avatar_url'] = $avatarUrl;
		$userInfo['gender'] = $gender+1;
		$userInfo['area'] = $area;
		$userInfo['login_mobile'] = $mobile;
		$data=Db::name('farm_user')->where('open_id',$open_id)->find();
        if(!$data){
        	$userInfo['create_time']=time();     
			$userInfo['update_time']=time();   
            $user_id = Db::name('farm_user')->insertGetId($userInfo);
        	if (!$user_id) {
	        	return json_encode(['code'=>0,'msg' => '用户注册失败']);
	        }
	        return $user_id;
        }else{
        	$userInfo['update_time']=time();
        	Db::name('farm_user')->where('Id',$data['Id'])->update($userInfo);
        	return $data['Id'];
        }
    }
}
?>
