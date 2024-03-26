<?php


namespace app\user\model;

use think\Model;
use think\helper\Hash;
use app\user\model\Role as RoleModel;
use app\user\model\User as UserModel;
use think\Db;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_user';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 对密码进行加密
    public function setPasswordAttr($value)
    {
        return Hash::make((string)$value);
    }

    // 获取注册ip
    public function setSignupIpAttr()
    {
        return get_client_ip(1);
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param bool $rememberme 记住登录
     * @author
     * @return bool|mixed
     */
    public function login($username = '', $password = '', $rememberme = false)
    {
        $map['status'] = 1;
        $map['username'] = $username;
        $user = $this::get($map);// 查找用户
        if (!$user) {
            return $this->json_result('', 409, '用户名错误!');
        } else {
            if (md5($password) != $user['password']) {
                return $this->json_result('', 409, '密码错误!');
            } else {// 更新登录信息
                $uid = $user['id'];
                $user['last_login_time'] = request()->time();
                $user['last_login_ip']   = request()->ip(1);
                if ($user->save()) {// 自动登录
                    $uid = $this->autoLogin($this::get($uid), $rememberme);
                    action_log('user_signin', 'admin_user', $uid, $uid);// 记录行为
                    return $this->json_result('', 200, '登录成功!');
                } else {// 更新登录信息失败
                    return $this->json_result('', 409, '登录信息更新失败，请重新登录！');
                }
            }
        }
    }

    /**
     * 自动登录
     * @param object $user 用户对象
     * @param bool $rememberme 是否记住登录，默认7天
     * @author
     * @return bool|int
     */
    public function autoLogin($user, $rememberme = false)
    {
        // 记录登录SESSION和COOKIES
        $auth = array(
            'uid'             => $user->id,
            'group'           => $user->group,
            'role'            => $user->role,
            'role_name'       => Db::name('mobile_role')->where('id', $user->role)->value('name'),
            'avatar'          => $user->avatar,
            'username'        => $user->username,
            'nickname'        => $user->nickname,
            'last_login_time' => $user->last_login_time,
            'last_login_ip'   => get_client_ip(1),
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
        

        // 保存用户节点权限
        if ($user->role != 1) {
            $menu_auth = Db::name('mobile_role')->where('id', session('user_auth.role'))->value('menu_auth');
            $menu_auth = json_decode($menu_auth, true);
            if (!$menu_auth) {
                session('user_auth', null);
                session('user_auth_sign', null);
                $this->error = '未分配任何节点权限！';
                return false;
            }
        }

        // 记住登录
        if ($rememberme) {
            $signin_token = $user->username.$user->id.$user->last_login_time;
            cookie('uid', $user->id, 24 * 3600 * 7);
            cookie('signin_token', data_auth_sign($signin_token), 24 * 3600 * 7);
        }

        return $user->id;
    }
}
