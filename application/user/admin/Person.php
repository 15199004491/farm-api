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
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        // 查询是否存在当前信息
        $user = PersonModel::where('username', $username)->find();
        $user? PersonModel::where('username', $username)->update($data) : '';
        if($user) {
            $result = PersonModel::where('username', $username)->update($data);
            return $this->json_result($result, 200, '操作成功');
        } else {
            $result = PersonModel::insert($data);
            return $this->json_result($result, 200, '注册成功');
        }
    }
    // 修改密码
    public function editPassword()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        $user = PersonModel::where('username', $username)->find();
        if($data['identity_card'] == $user['identity_card']) {
            $result = PersonModel::where('identity_card', $data['identity_card'])->update(['password' => $data['newPwd']]);
            return $result? $this->json_result($result, 200, '操作成功') : $this->json_result($result, 409, '系统错误,请稍后重试');
        } else {
            return $this->json_result('', 409, '身份证后6位错误,请重新输入');
        }
    }
    /**
     * 查询会员信息
     */
    public function personDetail()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        $result = PersonModel::where('username', $username)->find();
        return $result? $this->json_result($result, 200, '操作成功') : $this->json_result($result, 409, '系统错误,请稍后重试');
    }
     /**
     * 会员分页列表
     */
    public function personList()
    {
        // here--
        // 区分性别，对应捞取数据
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
    // 更新图片
    public function updateImage()
    {
        if(!empty($_FILES['image'])){
            //获取扩展名
            $exename = $this->getExeName($_FILES['image']['name']);

            $imageSavePath ='person/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
    // 收藏心动人员
    public function saveStar()
    {
        $data = $this->request->param();
        
        $user = PersonModel::where('username', $data['yourId'])->find();
        
        $star = json_decode($user['star']);

        $star? array_push($star,$data['username']):$star=[$data['username']];
        $star = json_encode($star);
        
        $result = PersonModel::where('username',  $data['yourId'])->update(['star' => $star]);
        
        return $this->json_return($result);
    }
    /**
     * 心动列表
     */
    public function starList()
    {
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $star = PersonModel::where('username', $username)->value('star');
        $condition = implode(",", json_decode($star));
        $result = PersonModel::where('username','in',$condition)->select();

        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 取消心动
     */
    public function cancleStar()
    {
        $data = $this->request->param();
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        
        $star = PersonModel::where('username', $username)->value('star');
        $newStar = array_diff(json_decode($star), [$data['username']]);//$data['username']为取消的人
        $param = json_encode(array_values($newStar));
        $result = PersonModel::where('username',$username)->update(['star' => $param]);

        return $this->json_return($result);
    }
    /**
     * 获取人员头像
     */
    public function getUsersInfo()
    {
        $data = $this->request->param();
        $result = PersonModel::where('username','in',$data['userIds'])->select();
        return $this->json_return($result);
    }
    /**
     * 按登录时间取最新的6条数据
     */
    public function refreshData()
    {
        // 1.查询当前登录用户的性别
        $token = $this->getToken();
        $username = explode(',',$token)[0];
        $userInfo = PersonModel::where('mobile', $username)->find();
        $sex = $userInfo['sex'] == 0? 1 : 0;

        // 2.扣除对应的积分6分
        $over = $userInfo['integral'] - 6;
        PersonModel::where('mobile', $username)->update(['integral' => $over]);

        // 3.对应查询数据
        $map = [
            ['sex', '=', $sex]
        ];
        $data_list = PersonModel::where($map)->order('id last_login_time')->limit(0, 6)->select();

        // 4.返回数据
        return $this->json_result($data_list, 200, '操作成功');
    }
    // 获取自己介绍过来的人
    public function belong() {
        // here--
    }
}
