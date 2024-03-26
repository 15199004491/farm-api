<?php


namespace app\user\admin;
use app\user\model\Message as MessageModel;

use app\common\controller\Common;

/**
 * 消息控制器
 * @package app\user\admin
 */
class Message extends Common
{
    /**
     * 消息列表
     */
    public function index()
    {
        $data = $this->request->param();
        $username = $data['username'];
        $map = [$data['type'] == 0? ['from', '=', $username] : ['to', '=', $username]];
        $data_list = MessageModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_result($data_list, 200, '操作成功');
    }
    /**
     * 获取邮件内容
     */
    public function content()
    {
        $data = $this->request->param();
        $result = MessageModel::where('id', $data['id'])->find();
        // 分页数据
        return $this->json_result($result, 200, '操作成功');
    }

    /**
     * 发送消息
     */
    public function send()
    {
        $data = $this->request->post();
        $data['time'] = time();

        $Model = new MessageModel();
        $result = $Model->insert($data);
        return $this->json_result($result, 200, '发送成功');
    }
    /**
     * 删除消息
     */
    public function delete()
    {
        $data = $this->request->get();
        $result = MessageModel::where('id',$data['id'])->delete();
        return $this->json_result($result, 200, '删除成功');
    }
}
