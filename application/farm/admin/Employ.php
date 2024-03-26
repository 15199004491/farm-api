<?php

namespace app\farm\admin;

use app\farm\model\Employ as EmployModel;
use app\farm\model\Person as PersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Employ extends Common
{
    /**
     * 招工发布
     */
    public function addEmploy()
    {
        $data = $this->request->param();
        if($data['new'] == 1) {
            $token = $this->getToken();
            $data['publisher'] = explode(',',$token)[0];
        }
        // 加入活动数据
        $param = $data['new']? EmployModel::insertGetId($data) : EmployModel::where('id', $data['id'])->update($data);
       
        return $this->json_return($param);
    }
    /**
     * 查看招工信息
     */
    public function employDetail()
    {
        $data = $this->request->param();
        $result = '';
        if($data['id']) {
            $result = EmployModel::where('Id', $data['id'])->find();
        }
        return $this->json_return($result);
    }
     /**
     * 招工分页列表
     */
    public function employList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];
        $map = [
            ['name|description', 'like', "%$keyword%"]
        ];
        $data_list = EmployModel::where($map)->order('id desc')->limit($data['start'], $data['end'])->select();
        // 分页数据
        return $this->json_return($data_list);
    }
    // 参加活动
    public function joinEmploy()
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
    public function attendedEmploy()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $activity = PersonModel::where('username', $username)->value('activity');
        $condition = implode(",", json_decode($activity));
        $result = EmployModel::where('id','in',$condition)->select();

        return $this->json_return($result);
    }
    /**
     * 发布的活动列表
     */
    public function publishList()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];

        $result = EmployModel::where('publisher',$username)->select();
        return $this->json_return($result);
    }
    // here--
    /**
     * 取消参加活动
     */
    public function cancleJoinEmploy()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        // 先把用户表里的活动删除
        // 再把活动表里的用户删除
        
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
        
        $star = EmployModel::where('username', $username)->value('star');
        $newStar = array_diff(json_decode($star), [$data['username']]);//$data['username']为取消的人
        $param = json_encode(array_values($newStar));
        $result = EmployModel::where('username',$username)->update(['star' => $param]);

        return $this->json_return($result);
    }
}
