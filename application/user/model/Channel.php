<?php


namespace app\user\model;

use think\Model;

/**
 * 消息列表模型
 * @package app\admin\model
 */
class Channel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'mobile_channel';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    // 创建一个对话
    public static function setList($from,$to,$message)
    {
        // 是否存在该条消息
        $map = [
            ['fromId', '=', $from],
            ['toId', '=', $to]
        ];
       
        $item = ['fromId' => $from,'toId' => $to,'message' => $message, 'isRead' => 0, 'insertTime' => request()->time()];
        $param = self::where($map)->find();
        if(!$param) self::insert($item);
    }
    // 更新列表的消息
    public static function updateList($from,$to,$message)
    {
        $map = [
            ['from', '=', $from],
            ['to', '=', $to]
        ];
        $item = ['from' => $from,'to' => $to,'message' => $message, 'isRead' => 0, 'time' => request()->time()];
        return self::where($map)->update($item);
    }
}
