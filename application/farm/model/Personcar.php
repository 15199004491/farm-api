<?php


namespace app\farm\model;

use think\Model;

/**
 * 活动模型
 * @package app\farm\model
 */
class Personcar extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'farm_person_car';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
