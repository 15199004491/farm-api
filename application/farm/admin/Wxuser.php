<?php
namespace app\farm\admin;

use think\Controller;
use think\facade\Cache;
use app\farm\model\Wxuser as WxuserModel;
use app\farm\model\Suggest as SuggestModel;
use app\farm\model\Person as PersonModel;

/**
 * 微信用户相关接口
 * @package app\farm\admin
 */
class Wxuser extends Controller
{
    private $appId;
    private $appSecret;

    public function __construct()
    {
        $this->appId     = config('wechat.wx_appid') ?: 'wx5375bc6d5a7a6227';
        $this->appSecret = config('wechat.wx_appsecret') ?: 'f946359b33b372d190c2d9be6e2cb213';
    }

    /* ========================================
     * 工具方法
     * ======================================== */

    private function http_request($url, $data = null, $useJson = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($useJson && is_array($data)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return json_encode(['errcode' => -1, 'errmsg' => 'HTTP请求失败', 'http_code' => $httpCode]);
        }

        return $output;
    }

    private function getAccessToken()
    {
        $cacheKey = 'wx_access_token';
        $token = Cache::get($cacheKey);
        if ($token) {
            return $token;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $res = json_decode($this->http_request($url), true);

        if (isset($res['access_token'])) {
            Cache::set($cacheKey, $res['access_token'], 7000);
            return $res['access_token'];
        }

        return '';
    }

    private function generateToken($openid)
    {
        return md5($openid . '_' . time() . '_' . mt_rand(1000, 9999));
    }

    private function getUserByToken($token)
    {
        if (empty($token)) {
            return null;
        }

        $userId = Cache::get('wx_token_' . $token);
        if ($userId) {
            return PersonModel::where('Id', $userId)->find();
        }

        $user = PersonModel::where('login_mobile', $token)->find();
        if ($user) {
            return $user;
        }

        return null;
    }

    /* ========================================
     * 登录 / 登出
     * ======================================== */

    /**
     * 微信登录
     * 参数：code(小程序wx.login获取), login_mobile(可选，绑定已有手机号)
     */
    public function login()
    {
        $data = request()->param();

        $code = isset($data['code']) ? $data['code'] : '';
        if (empty($code)) {
            return json(['code' => 400, 'msg' => '请传入code', 'data' => null]);
        }

        $model = new WxuserModel();
        $session = $model->login($data);

        if (!isset($session['openid'])) {
            $errmsg = isset($session['errmsg']) ? $session['errmsg'] : '微信登录失败';
            return json(['code' => 500, 'msg' => $errmsg, 'data' => null]);
        }

        $openid = $session['openid'];

        $data['open_id'] = $openid;
        $data['update_time'] = time();

        $user = PersonModel::where('open_id', $openid)->find();

        $mobile = isset($data['login_mobile']) ? $data['login_mobile'] : '';

        if ($user) {
            $userId = $user['Id'];
            if ($mobile !== '') {
                $data['login_mobile'] = $mobile;
                PersonModel::where('Id', $userId)->update($data);
            } else {
                PersonModel::where('Id', $userId)->update(['update_time' => time()]);
            }
        } else {
            $data['create_time'] = time();
            $data['login_mobile'] = $mobile;
            $userId = PersonModel::insertGetId($data);
        }

        $token = $this->generateToken($openid);
        Cache::set('wx_token_' . $token, $userId, 604800);

        $result = PersonModel::where('Id', $userId)->find();
        $result['session_token'] = $token;

        return json(['code' => 200, 'msg' => '登录成功', 'data' => $result]);
    }

    /**
     * 绑定手机号（用户登录后绑定/更换手机号）
     * 参数：token, login_mobile
     */
    public function bindMobile()
    {
        $data = request()->param();
        $token = isset($data['token']) ? $data['token'] : '';
        $mobile = isset($data['login_mobile']) ? $data['login_mobile'] : '';

        if (empty($token)) {
            return json(['code' => 401, 'msg' => '请先登录', 'data' => null]);
        }
        if (empty($mobile)) {
            return json(['code' => 400, 'msg' => '请传入手机号', 'data' => null]);
        }

        $user = $this->getUserByToken($token);
        if (!$user) {
            return json(['code' => 401, 'msg' => '登录已过期', 'data' => null]);
        }

        $exists = PersonModel::where('login_mobile', $mobile)
            ->where('Id', '<>', $user['Id'])
            ->find();
        if ($exists) {
            return json(['code' => 409, 'msg' => '该手机号已被其他账号绑定', 'data' => null]);
        }

        $result = PersonModel::where('Id', $user['Id'])->update([
            'login_mobile' => $mobile,
            'update_time'  => time(),
        ]);

        return json(['code' => 200, 'msg' => '绑定成功', 'data' => $result]);
    }

    /**
     * 退出登录
     * 参数：token
     */
    public function logout()
    {
        $data = request()->param();
        $token = isset($data['token']) ? $data['token'] : '';

        if (!empty($token)) {
            Cache::delete('wx_token_' . $token);
        }

        return json(['code' => 200, 'msg' => '退出成功', 'data' => null]);
    }

    /**
     * 刷新token（延长有效期）
     * 参数：token
     */
    public function refreshToken()
    {
        $data = request()->param();
        $token = isset($data['token']) ? $data['token'] : '';

        $userId = Cache::get('wx_token_' . $token);
        if ($userId) {
            Cache::set('wx_token_' . $token, $userId, 604800);
            return json(['code' => 200, 'msg' => '刷新成功', 'data' => ['token' => $token]]);
        }

        $user = $this->getUserByToken($token);
        if ($user) {
            $newToken = $this->generateToken($user['open_id']);
            Cache::set('wx_token_' . $newToken, $user['Id'], 604800);
            return json(['code' => 200, 'msg' => '刷新成功', 'data' => ['token' => $newToken]]);
        }

        return json(['code' => 401, 'msg' => '登录已过期', 'data' => null]);
    }

    /* ========================================
     * 用户信息
     * ======================================== */

    /**
     * 获取当前用户信息
     * 参数：token
     */
    public function getUserInfo()
    {
        $data = request()->param();
        $token = isset($data['token']) ? $data['token'] : '';

        $result = $this->getUserByToken($token);

        if (!$result) {
            return json(['code' => 401, 'msg' => '用户不存在或未登录', 'data' => null]);
        }

        $result['house_vip'] = $result['house_vip_end'] > time();
        $result['session_token'] = $token;

        return json(['code' => 200, 'msg' => '操作成功', 'data' => $result]);
    }

    /**
     * 更新用户信息
     * 参数：token + 要更新的字段
     */
    public function ringUp()
    {
        $data = request()->param();
        $token = isset($data['token']) ? $data['token'] : '';

        $user = $this->getUserByToken($token);
        if (!$user) {
            return json(['code' => 401, 'msg' => '用户不存在或未登录', 'data' => null]);
        }

        $data['update_time'] = time();
        unset($data['token']);

        $result = PersonModel::where('Id', $user['Id'])->update($data);

        return json(['code' => 200, 'msg' => '更新成功', 'data' => $result]);
    }

    /**
     * 房产会员
     */
    public function houseVip()
    {
        $data = request()->param();
        $result = PersonModel::where('login_mobile', $data['login_mobile'])->update($data);
        return json(['code' => 200, 'msg' => '操作成功', 'data' => $result]);
    }

    /* ========================================
     * 微信敏感内容检测
     * ======================================== */

    /**
     * 微信文字敏感内容检测
     * 参数：msg
     */
    public function msgSecCheck()
    {
        $data = request()->post();
        $msg = isset($data['msg']) ? $data['msg'] : '';

        if (empty($msg)) {
            return json(['code' => 400, 'msg' => '内容不能为空', 'data' => null]);
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return json(['code' => 500, 'msg' => '获取access_token失败', 'data' => null]);
        }

        $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token={$token}";
        $info = $this->http_request($url, ['content' => $msg]);
        $result = json_decode($info, true);

        return json(['code' => 200, 'msg' => '检测完成', 'data' => $result]);
    }

    /**
     * 微信图片敏感内容检测（同步上传）
     */
    public function imgSecCheck()
    {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return json(['code' => 400, 'msg' => '请上传图片', 'data' => null]);
        }

        $fileTmpPath = $_FILES['file']['tmp_name'];
        if (!file_exists($fileTmpPath)) {
            return json(['code' => 400, 'msg' => '文件不存在', 'data' => null]);
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return json(['code' => 500, 'msg' => '获取access_token失败', 'data' => null]);
        }

        $url = "https://api.weixin.qq.com/wxa/img_sec_check?access_token={$token}";

        $tmpFile = tempnam(sys_get_temp_dir(), 'wx_img_');
        $imgContent = file_get_contents($fileTmpPath);
        file_put_contents($tmpFile, $imgContent);

        $obj = new \CURLFile($tmpFile);
        $obj->setMimeType("image/jpeg");
        $result = $this->http_request($url, ['media' => $obj], false);

        @unlink($tmpFile);

        return json(['code' => 200, 'msg' => '检测完成', 'data' => json_decode($result, true)]);
    }

    /* ========================================
     * 手机号获取
     * ======================================== */

    /**
     * 获取手机号
     * 参数：code (wx.getPhoneNumber获取的code)
     */
    public function getuserphonenumber()
    {
        $data = request()->param();
        $code = isset($data['code']) ? $data['code'] : '';

        if (empty($code)) {
            return json(['code' => 400, 'msg' => '请传入code', 'data' => null]);
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return json(['code' => 500, 'msg' => '获取access_token失败', 'data' => null]);
        }

        $url = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token={$token}";
        $info = $this->http_request($url, ['code' => $code]);
        $tmpinfo = json_decode($info, true);

        if (isset($tmpinfo['errcode']) && $tmpinfo['errcode'] == 0) {
            $phoneNumber = $tmpinfo['phone_info']['phoneNumber'] ?? '';
            return json(['code' => 200, 'msg' => '获取成功', 'data' => ['phoneNumber' => $phoneNumber]]);
        }

        $errmsg = isset($tmpinfo['errmsg']) ? $tmpinfo['errmsg'] : '获取手机号失败';
        return json(['code' => 500, 'msg' => $errmsg, 'data' => null]);
    }

    /* ========================================
     * 图片上传 / 删除
     * ======================================== */

    private $allowExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $uploadDir = 'farm';

    public function updateImage()
    {
        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return json(['code' => 400, 'msg' => '请上传图片', 'data' => null]);
        }

        $fileName = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowExt)) {
            return json(['code' => 400, 'msg' => '不支持的图片格式', 'data' => null]);
        }

        $maxSize = 5 * 1024 * 1024;
        if ($_FILES['image']['size'] > $maxSize) {
            return json(['code' => 400, 'msg' => '图片不能超过5M', 'data' => null]);
        }

        $uploadDir = root_path() . 'public' . DIRECTORY_SEPARATOR . $this->uploadDir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $saveName = uniqid() . '.' . $ext;
        $savePath = $uploadDir . DIRECTORY_SEPARATOR . $saveName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $savePath)) {
            $imageUrl = $this->uploadDir . '/' . $saveName;
            return json(['code' => 200, 'msg' => '上传成功', 'data' => $imageUrl]);
        }

        return json(['code' => 500, 'msg' => '上传失败', 'data' => null]);
    }

    public function removeImage()
    {
        $data = request()->param();
        $path = isset($data['path']) ? $data['path'] : '';

        if (empty($path)) {
            return json(['code' => 400, 'msg' => '请传入文件路径', 'data' => null]);
        }

        $realPath = realpath(root_path() . 'public' . DIRECTORY_SEPARATOR . $path);
        $uploadDir = realpath(root_path() . 'public' . DIRECTORY_SEPARATOR . $this->uploadDir);

        if (!$realPath || strpos($realPath, $uploadDir) !== 0) {
            return json(['code' => 403, 'msg' => '无权删除该文件', 'data' => null]);
        }

        if (file_exists($realPath)) {
            @unlink($realPath);
            return json(['code' => 200, 'msg' => '删除成功', 'data' => null]);
        }

        return json(['code' => 404, 'msg' => '文件不存在', 'data' => null]);
    }

    /* ========================================
     * 投诉建议
     * ======================================== */

    public function addSuggest()
    {
        $data = request()->param();
        $data['update_time'] = time();
        $param = SuggestModel::insertGetId($data);

        return json(['code' => 200, 'msg' => '提交成功', 'data' => $param]);
    }

    /* ========================================
     * Access Token（供外部调用）
     * ======================================== */

    public function getToken()
    {
        $token = $this->getAccessToken();
        if ($token) {
            return json(['code' => 200, 'msg' => '获取成功', 'data' => $token]);
        }
        return json(['code' => 500, 'msg' => '获取失败', 'data' => null]);
    }
}