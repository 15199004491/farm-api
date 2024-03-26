<?php

namespace app\user\admin;

use app\common\controller\NoLogin;
use app\user\model\User as UserModel;
use think\facade\Hook;
use think\Db;

/**
 * 用户公开控制器，不经过权限认证
 * @package app\user\admin
 */
class Publics extends NoLogin
{
    /**
     * 用户登录
     * @author
     * @return mixed
     */
    public function signin()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param(); // 获取post数据
            $UserModel = new UserModel;
            $user = UserModel::where('username', $data['username'])->find();
            if($user) {
                if (md5($data['password']) != $user['password']) {
                    return $this->json_result('', 409, '密码错误!');
                } else {// 更新登录信息
                    if ($user->save()) {// 自动登录
                        $UserModel->autoLogin($UserModel::get($user['id']), false);
                        // 校验token
                        $UserModel->checkToken();
                        return $this->json_result('', 200, '登录成功!');
                    } else {// 更新登录信息失败
                        return $this->json_result('', 409, '登录信息更新失败，请重新登录！');
                    }
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
     * 用户注册
     * @author
     * @return mixed
     */
    public function registration()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param(); // 获取post数据
            $UserModel = new UserModel;
            $result = $UserModel->checkUser($data['username']);
            if($result) {
                return $this->json_result('', 409, '当前用户已存在');
            } else {
                // if (config('captcha_signin')) { // 验证码
                //     $captcha = $this->request->post('captcha', '');
                //     if($captcha) {
                //         if(captcha_check($captcha, '')) {
                            $uid = UserModel::insert($data);
                            if ($uid) {
                                action_log('user_signin', 'admin_user', $uid, $uid);// 记录行为
                                return $this->json_result('', 200, '注册成功');
                            } else {
                                return $this->json_result('', 409, '系统错误,请稍后重试');
                            }
                //         }
                //     }
                // }
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
     * 跳转到第一个有权限访问的url
     * @author
     * @return mixed|string
     */
    private function jumpUrl()
    {
        if (session('user_auth.role') == 1) {
            $this->success('登录成功', url('admin/index/index'));
        }

        $default_module = RoleModel::where('id', session('user_auth.role'))->value('default_module');
        $menu = MenuModel::get($default_module);
        if (!$menu) {
            $this->error('当前角色未指定默认跳转模块！');
        }

        if ($menu['url_type'] == 'link') {
            $this->success('登录成功', $menu['url_value']);
        }

        $menu_url = explode('/', $menu['url_value']);
        role_auth();

        $menus = MenuModel::getSidebarMenu($default_module, $menu['module'], $menu_url[1]);
        $url   = '';
        foreach ($menus as $key => $menu) {
            if (!empty($menu['url_value'])) {
                $url = $menu['url_value'];
                break;
            }
            if (!empty($menu['child'])) {
                $url = $menu['child'][0]['url_value'];
                break;
            }
        }

        if ($url == '') {
            $this->error('权限不足');
        } else {
            $this->success('登录成功', $url);
        }
    }

    /**
     * 退出登录
     * @author
     */
    public function signout()
    {
        $hook_result = Hook::listen('signout_sso');
        if (!empty($hook_result) && true !== $hook_result[0]) {
            if (isset($hook_result[0]['url'])) {
                $this->redirect($hook_result[0]['url']);
            }
            if (isset($hook_result[0]['error'])) {
                $this->error($hook_result[0]['error']);
            }
        }

        session(null);
        cookie('uid', null);
        cookie('signin_token', null);

        return $this->json_result('', 200, '退出成功');
    }
}
