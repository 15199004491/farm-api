<?php


namespace app\user\admin;
use app\user\model\Channel as ChannelModel;
use app\user\model\ItemMessage as ItemMessageModel;

use app\common\controller\Common;

/**
 * 消息控制器
 * @package
 */
class Channel extends Common
{
    /**
     * 消息列表
     */
    public function getLists()
    {
        $userId = $this->getUserId();
        $map = [
            ['from|to', '=', $userId]
        ];
        $result = ChannelModel::where($map)->select();
        return $this->json_return($result);
    }
    /**
     * 设置消息列表已读
     */
    public function setIsRead(){
        $data = $this->request->param();
        $result = ChannelModel::where('id',$data['id'])->update(['isRead' => 1]);
        return $this->json_return($result, 200, '操作成功');
    }
     /**
     * 创建对话
     */
    public function createChannel(){
        $data = $this->request->param();
        $token = self::getToken();
        $username = explode(',',$token)[0];
        ChannelModel::setList($username,$data['to'],'');
    }
}
