<?php

namespace app\user\admin;

use app\user\model\Person as PersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Person extends Common
{
    /**
     * 注册/编辑会员
     */
    public function editPerson()
    {
        $data = $this->request->param();
        if($data['id']) {
            $result = PersonModel::where('id', $data['id'])->update();
        } else {
            $result = PersonModel::insertAll($data);
        }
        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 查询会员信息
     */
    public function personDetail()
    {
        $data = $this->request->param();
        $result = PersonModel::where('personId', $data['personId'])->find();
        return $this->json_result($result, 200, '操作成功');
    }
     /**
     * 会员分页列表
     */
    public function personList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];
        $map = [
            ['status', '=', 1],
            ['name|description', 'like', "%$keyword%"]
        ];
        $data_list = PersonModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_result($data_list, 200, '操作成功');
    }
}
