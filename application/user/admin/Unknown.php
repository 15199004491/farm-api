<?php


namespace app\user\admin;
use app\user\model\Unknown as UnknownModel;

use app\common\controller\Common;

/**
 * 消息控制器
 * @package app\user\admin
 */
class Unknown extends Common
{
    /**
     * 取出纸条
     */
    public function inPaper()
    {
        $data = $this->request->param();
        $result = UnknownModel::insertGetId($data);
        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 取出纸条
     */
    public function getPaper()
    {
        $data = $this->request->param();
        $sex = $data['sex'] == 1 ? 0:1;
        $result = UnknownModel::where(['sex' => $sex])->limit(1)->orderRaw("rand() , id DESC")->select();
        return $this->json_result($result[0], 200, '操作成功');
    }
}
