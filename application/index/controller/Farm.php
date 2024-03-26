<?php


namespace app\index\controller;

use app\common\controller\Common;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Farm extends Common
{
    /**
     * 初始化方法
     * @author
     */
    protected function initialize()
    {
        // 系统开关
        if (!config('web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
    }
    public function index()
    {
        var_dump(23453464);
        exit();
    }
}
