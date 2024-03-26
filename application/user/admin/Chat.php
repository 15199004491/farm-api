<?php


namespace app\user\admin;
use app\user\model\Channel as ChannelModel;
use app\user\model\Chat as ChatModel;

use app\common\controller\Common;

/**
 * 消息控制器
 * @package
 */
class Chat extends Common
{
    /**
     * 获取历史消息
     */
    public function getHistory()
    {
        $data = $this->request->param();
        $username = $data['user'];
        $map = [
            ['from|to', '=', $username]
        ];
        $data_list = ChatModel::where($map)->order('time asc')->limit($data['start'], $data['end'])->select();
        return $this->json_return($data_list);
    }
   
    /**
     * 发送对话
     */
    public function send()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $from = explode(',',$token)[0];
        // 更新消息列表
        $channelId = ChannelModel::updateList($from, $data['to'], $data['message'], true);
        // 新增对话消息
        $message = ChatModel::setMsg($from, $data['to'],$data['type'],$data['message'],$channelId);
        $result = ChatModel::insert($message);
        return $this->json_return($result,$data['message']);
    }
    /**
     * 上传图片
     */
    public function updateImage(){
        if(!empty($_FILES['image'])){
            //获取扩展名
            $exename = $this->getExeName($_FILES['image']['name']);

            $imageSavePath ='im/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
}
