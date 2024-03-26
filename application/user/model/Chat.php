<?php


namespace app\user\model;

use think\Model;

/**
 * 对话模型
 * @package app\admin\model
 */
class Chat extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'mobile_chat';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 设置一条消息 文本--1 图片--2
    public static function setMsg($from, $to, $type = 'text', $message, $channelId)
    {
        $message = ['from' => $from,'to' => $to,'message' => $message, 'type' => $type,'isRead' => false,'time' => time(),'channelId' => $channelId];
        return $message;
    }

}
