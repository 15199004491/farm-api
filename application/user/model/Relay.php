<?php


namespace app\user\model;

use think\Model;

/**
 * 人物模型
 * @package app\admin\model
 */
class Relay extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'mobile_relay';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
