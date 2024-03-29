<?php


namespace app\farm\model;

use think\Model;

/**
 * 人物模型
 * @package
 */
class Person extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'farm_user';
    // protected $name = 'admin_action';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
