<?php


namespace app\user\model;

use think\Model;
use think\facade\Cache;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class User extends Model
{
    // 用于生成token的自定义盐
    const TOKEN_SALT = 'user_salt';
    // 设置当前模型对应的完整数据表名称
    protected $name = 'mobile_person';
     /**
     * 生成用户认证的token
     */
    private static function makeToken($userId)
    {
        // 生成一个不会重复的随机字符串
        $guid = self::get_guid_v4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = self::TOKEN_SALT;
        return $userId.','.md5("{$timeStamp}_{$userId}_{$guid}_{$salt}");
    }
    public static function updateToken($userInfo)
    {
        
        $token = self::makeToken($userInfo['username']);
        // 记录缓存, 30天
        Cache::set($token, [
            'user' => $userInfo,
            'is_login' => true,
        ], 86400 * 30);
        
        return $token;
    }
    /**
     * 记录登录信息
     * @param array $userInfo
     * @return string
     */
    public static function login(array $userInfo)
    {
        // 生成token
        $token = self::makeToken((int)$userInfo['admin_user_id']);
        // 记录缓存, 7天
        Cache::set($token, [
            'user' => [
                'admin_user_id' => (int)$userInfo['admin_user_id'],
                'user_name' => $userInfo['user_name'],
            ],
            'is_login' => true,
        ], 86400 * 7);
        return $token;
    }
    public static function deleteToekn(){
        Cache::delete(self::getToken());
    }
    // 新增用户并登录
    public static function addNewPerson($user){
        self::insert($user);
        $token = self::updateToken($user);
        return $token;
    }
    /**
     * 获取用户信息
     * @param string $token
     * @return User|array|false|null
     * @throws BaseException
     */
    public static function getUserByToken(string $token)
    {
        // 检查登录态是否存在
        if (!Cache::has($token)) {
            return false;
        }
        // 用户的ID
        $userId = (int)Cache::get($token)['user']['user_id'];
        // 用户基本信息
        $userInfo = self::detail($userId);
        if (empty($userInfo) || $userInfo['is_delete']) {
            throwError('很抱歉，用户信息不存在或已删除', config('status.not_logged'));
        }
        return $userInfo;
    }
    public static function getCurrentLoginUserId($token){
        return self::getUserByToken($token)['id'];
    }
}
