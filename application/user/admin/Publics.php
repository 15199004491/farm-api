<?php

namespace app\user\admin;

use app\common\controller\NoLogin;
use app\user\model\User as UserModel;
use think\Db;

/**
 * 用户公开控制器，不经过权限认证
 * @package app\user\admin
 */
class Publics extends NoLogin
{
    
    /**
     * 用户登录
     */
    public function signin()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param(); // 获取post数据
            $user = UserModel::where('username', $data['username'])->find();
            if($user) {
                if (md5($data['password']) != $user['password']) {
                    return $this->json_result('', 409, '密码错误!');
                } else {// 更新token
                    $token = UserModel::updateToken($user);
                    $user['token'] = $token;
                    if($token) return $this->json_result($user, 200, '登录成功');
                }
            } else {
                return $this->json_result('', 409, '用户不存在或已禁用!');
            }
        }
    }
      /**
     * 首页模块列表
     * @author
     * @return mixed
     */
    public function menuList()
    {
        $map = [
            ['status', '=', 1]
        ];
        $data_list = Db::name('admin_module')->where($map)->select();
        return $this->json_result($data_list, 200, '操作成功');
    }
    /**
     * 重置密码
     * @author
     * @return mixed
     */
    public function reSetPwd()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if (config('captcha_signin')) { // 验证码
                $captcha = $this->request->post('captcha', '');
                if($captcha) {
                    if(captcha_check($captcha, '')) {
                        $map = [
                            ['username|mobile|email', '=', $data['username']]
                        ];
                        $user = UserModel::where($map)->find();
                        if($user) {
                            UserModel::where($map)->update(['password' => md5('123456')]);
                            return $this->json_result('123456', 200, '已为您重置新密码,请重新登录'); // 重置密码
                        } else {
                            return $this->json_result('', 409, '当前用户不存在!');
                        }
                    } else {
                        return $this->json_result('', 409, '验证码错误或失效');
                    }
                } else {
                    return $this->json_result('', 409, '请输入验证码');
                }
            }
        }
    }
     /**
     * 重置密码
     * @author
     * @return mixed
     */
    public function replacement()
    {
        $data = $this->request->param();
        $map = [
            ['username|mobile|email', '=', $data['username']]
        ];
        $user = UserModel::where($map)->find();
        if(!$user) return $this->json_result('', 409, '当前用户不存在!');
        if($data['identityCard'] == $user['identity_card']) {
            return $this->json_result('123456', 200, '重置成功!');
        } else {
            return $this->json_result('', 409, '身份证后6位数有误!');
        }
    }
    
    /**
     * 用户注册
     * @author
     * @return mixed
     */
    public function registration()
    {
        $param = $this->request->param();
        // 获取当前用户信息
        $user = UserModel::where('username', $param['username'])->find();

        if($user['mobile']) {
            return $this->json_result('', 409, '当前用户已存在');
        } else {
            $param['integral'] = 10;//首次登陆赠送10个积分
            $param['last_login_time']  = request()->time();
            $param['password'] = md5($param['password']);
            $token = UserModel::addNewPerson($param);//新增数据并登录
            // 给介绍者加积分并添加入子集
            if($param['introducer']) {
                $info = UserModel::where('username', $param['introducer'])->find();
                $info['integral'] += 6;
                $info['belong'] += ',' + $user['username'];
                $integral = UserModel::where('username', $param['introducer'])->update($info);// 给介绍者加积分
            }
            if($token || $param['introducer'] && $integral) {//新增数据成功
                return $this->json_result($token, 200, '注册成功,已赠送您10个积分');
            } else {
                return $this->json_result('', 409, '系统错误,请稍后重试');
            }
        }
    }
     /**
     * 判断用户是否存在
     * @author
     * @return mixed
     */
    public function checkUser() {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            $UserModel = new UserModel;
            $result = $UserModel->checkUser($data['username']);
            if($result) return $this->json_result(true, 409, '当前用户已存在');
        }
    }
    /**
     * 退出登录
     * @author
     */
    public function signout()
    {
        $result = UserModel::deleteToekn();
        if($result) $this->json_result('', 200, '退出成功');
    }
    /**
     * 收藏用户
     */
    public function star()
    {
        $param = $this->request->param();
        $map = [
            ['username|mobile|email', '=', $param['username']]
        ];
        $result = UserModel::where($map)->update(['password' => md5('123456')]);
        if($result) $this->json_result('', 200, '收藏成功');
    }
    // 获取最新的用户信息
    public function getUserInfo()
    {
        $data = $this->request->param();
        $user = UserModel::where('username', $data['username'])->find();
        return $this->json_result($user, 200, '上传成功');
    }
    // 微信登录
    public function wxLogin(){
        //声明CODE，获取小程序传过来的CODE
        if(!isset($_GET["code"])) return $this->json_result('', 409, '缺少code!');
        $code = $_GET["code"];
        //配置appid
        $appid = "wx5443fa9eaa5993ad";
        //配置appscret
        $secret = "9daa29a1baee09ba2b831d11d0970c8a";
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $info = file_get_contents($url);//get请求网址，获取数据
        $jsonObj = json_decode($info);//对json数据解码
        if(isset($jsonObj->errcode)) return $this->json_result('', 409, '解码失败!');
        return $this->json_result($jsonObj, 200, '解码成功!');
    }
}
