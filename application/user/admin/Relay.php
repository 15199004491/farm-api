<?php

namespace app\user\admin;

use app\user\model\Relay as RelayModel;
use app\user\model\Person as PersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Relay extends Common
{
    /**
     * 接龙列表
     */
    public function relayList()
    {
        $data = $this->request->param();
        if(isset($data->keyword)) {
            $keyword = $data['keyword'];
            $map = [
                ['name|content', 'like', "%$keyword%"],
                ['open', '=', 1],
            ];
        } else {
            $map = [
                ['open', '=', 1],
            ];
        }
        $data_list = RelayModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_result($data_list, 200, '操作成功');
    }
    /**
     * 接龙列表
     */
    public function getRelay()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $activeUer = explode(',',$token)[0];
        $result = PersonModel::where('Id', $activeUer)->find();
        $param = $data['type'] == 1 ? $result['activity'] : $data['type'] == 2 ? $result['pubActivity'] : [];//已参加activity和已发起的pubActivity
        $map = [
            ['id', 'in', $param]
        ];
        $data_list = RelayModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_result($data_list, 200, '操作成功');
    }
    // 新增/编辑活动
    public function edit()
    {
        $data = $this->request->param();
        if($data['newAdd'] == 1) {
            $token = $this->getToken();
            $data['publisher'] = explode(',',$token)[0];
        }
        $data['edit_time'] = request()->time();
        $param = $data['newAdd']? RelayModel::insertGetId($data) : RelayModel::where('id', $data['id'])->update($data);
        return $this->json_result($param, 200, '操作成功');
    }
    // 查询活动详情
    public function detail()
    {
        $data = $this->request->param();
        $result = RelayModel::where('Id', $data['id'])->find();
        return $this->json_return($result, 200, '操作成功');
    }
    // 参加接龙
    public function soonAttend(){
        $data = $this->request->param();

        $token = $this->getToken();
        $username = explode(',',$token)[0];
        // 参加活动的信息存储至人员表的活动字段里
        $person = PersonModel::where('username', $username)->find();
        $activity = json_decode($person['activity']);
        $param = array_push($activity, $data);
        PersonModel::where('username', $username)->update(['activity' => $param]);
        //参加活动的人员存储为字符串、
        $result = RelayModel::where('Id',  $data['id'])->find();
        $str = $result['person'].','.$username;
        
        $result = RelayModel::where('Id',  $data['id'])->update(['person' => $str]);
        return $this->json_return($result);
    }
    // 上传图片
    public function updateImage()
    {
        if(!empty($_FILES['image'])){
            //获取扩展名
            $exename = $this->getExeName($_FILES['image']['name']);

            $imageSavePath ='relay/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
}
