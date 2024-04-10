<?php

namespace app\user\admin;

use app\user\model\Activity as ActivityModel;
use app\user\model\Person as PersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Activity extends Common
{
    /**
     * 发布/编辑活动
     */
    public function editActivity()
    {
        $data = $this->request->param();
        if($data['newAdd'] == 1) {
            $token = $this->getToken();
            $data['publisher'] = explode(',',$token)[0];
        }
        // 加入活动数据
        $paramRe = $data['newAdd']? ActivityModel::insertGetId($data) : ActivityModel::where('id', $data['id'])->update($data);
       
        // 把发布者加入参加者
        $param = PersonModel::where('username', $data['publisher'])->find();
        
        $activity = json_decode($param['activity']);

        $activity? array_push($activity,$paramRe):$activity=[$paramRe];
        $activity = json_encode($activity);
        
        $result = PersonModel::where('username',  $data['publisher'])->update(['activity' => $activity]);

        return $result? $this->json_result($result, 200, '操作成功') : $this->json_result($result, 409, '请修改信息后再操作');
    }
    /**
     * 查看活动信息
     */
    public function activityDetail()
    {
        $data = $this->request->param();
        if($data['id']) {
            $result = ActivityModel::where('Id', $data['id'])->find();
            return $this->json_result($result, 200, '操作成功');
        } else {
            return $this->json_result('', 409, '参数错误');
        }
    }
     /**
     * 活动列表
     */
    public function activityList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];
        $map = [
            ['status', '=', 1],
            ['name|description', 'like', "%$keyword%"]
        ];
        $data_list = ActivityModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_result($data_list, 200, '操作成功');
    }
    // 更新图片
    public function updateImage()
    {
        if(!empty($_FILES['image'])){
            //获取扩展名
            $exename = $this->getExeName($_FILES['image']['name']);

            $imageSavePath ='activity/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
    // 参加活动
    public function joinActivity()
    {
        $data = $this->request->param();
        
        $param = PersonModel::where('username', $data['username'])->find();
        
        $activity = json_decode($param['activity']);

        $activity? array_push($activity,$data['activityId']):$activity=[$data['activityId']];
        $activity = json_encode($activity);
        
        $result = PersonModel::where('username',  $data['username'])->update(['activity' => $activity]);
        
        return $this->json_return($result);
    }
    /**
     * 已参加的活动列表
     */
    public function attendedList()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $activity = PersonModel::where('username', $username)->value('activity');
        $condition = implode(",", json_decode($activity));
        $result = ActivityModel::where('id','in',$condition)->select();

        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 发布的活动列表
     */
    public function publishList()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];

        $result = ActivityModel::where('publisher',$username)->select();
        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 取消参加活动
     */
    public function cancleJoinActivity()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $star = PersonModel::where('username', $username)->value('activity');
        $newStar = array_diff(json_decode($star), [$data['username']]);
        $param = json_encode(array_values($newStar));
        $result = PersonModel::where('username',$username)->update(['activity' => $param]);

        return $this->json_return($result);
    }
    /**
     * 取消活动
     */
    public function cancleActivity()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $star = ActivityModel::where('username', $username)->value('star');
        $newStar = array_diff(json_decode($star), [$data['username']]);//$data['username']为取消的人
        $param = json_encode(array_values($newStar));
        $result = ActivityModel::where('username',$username)->update(['star' => $param]);

        return $this->json_return($result);
    }
}
