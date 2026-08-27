<?php
namespace app\farm\admin;

use think\facade\Cache;
use app\common\controller\Common;
use app\farm\model\Wxuser as WxuserModel;
use app\farm\model\Suggest as SuggestModel;
use app\farm\model\Person as PersonModel;

/**
 * 微信用户相关接口
 * @package app\farm\admin
 */
class Wxuser extends Common
{
    /* ========================================
     * 工具方法
     * ======================================== */

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

        if ($user) {
            $userId = $user['Id'];
            PersonModel::where('Id', $userId)->update(['update_time' => time()]);
        } else {
            $data['create_time'] = time();
            $updateData['update_time'] = time();
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

        if (!is_array($result) || !isset($result['errcode'])) {
            return json(['code' => 500, 'msg' => '检测接口调用失败', 'data' => null]);
        }

        if ($result['errcode'] !== 0) {
            $errMsg = isset($result['errmsg']) ? $result['errmsg'] : '检测失败';
            return json(['code' => 500, 'msg' => $errMsg, 'data' => $result]);
        }

        $label = isset($result['result']['label']) ? $result['result']['label'] : 0;
        $level = isset($result['result']['level']) ? $result['result']['level'] : 'pass';
        $prob  = isset($result['result']['prob']) ? $result['result']['prob'] : 0;

        if ($label > 0 && $level === 'block') {
            return json(['code' => 400, 'msg' => '内容存在违规风险', 'data' => $result]);
        }

        if ($label > 0 || $level === 'review') {
            return json(['code' => 400, 'msg' => '内容需要人工审核', 'data' => $result]);
        }

        return json(['code' => 200, 'msg' => '检测通过', 'data' => $result]);
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