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
    // 删除信息
    public function deleteEmploy()
    {
        $data = $this->request->param();
        
        $result = EmployModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的招工列表
     */
    public function employSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = EmployModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 招工发布
     */
    public function addEmploy()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = EmployModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = EmployModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看招工信息
     */
    public function employDetail()
    {
        $data = $this->request->param();
        $result = EmployModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
      /**
     * 招工列表
     */
    public function employList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['title|explain', 'like', "%$keyword%"],
            ['area', '=', $data['area']]
        ];
        
        $data_list = EmployModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topEmploy()
    {
        $data = $this->request->param();
        
        $result = EmployModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
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
