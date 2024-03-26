<?php

namespace app\user\admin;
use app\common\controller\Common;
use app\user\model\User as UserModel;

/**
 * 
 * @package
 */
class Myself extends Common
{
    // 我的页面
    public function index()
    {
        $token = $this->getToken();
        $userId = explode(',',$token)[0];
        $user = UserModel::where('username',  $userId)->find();
        return $this->json_result( $user, 200, '登录成功' );
    }
}
