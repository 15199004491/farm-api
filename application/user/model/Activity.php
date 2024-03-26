<?php


namespace app\user\model;

use think\Model;
use app\user\model\Activity as ActivityModel;

/**
 * 活动模型
 * @package app\admin\model
 */
class Activity extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'mobile_activity';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
