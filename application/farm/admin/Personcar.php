<?php

namespace app\farm\admin;

use app\farm\model\Personcar as PersoncarModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Personcar extends Common
{
    // 删除信息
    public function deletePersonCar()
    {
        $data = $this->request->param();
        
        $result = PersoncarModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的人找车列表
     */
    public function personCarSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = PersoncarModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 人找车信息发布
     */
    public function addPersonCar()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = PersoncarModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = PersoncarModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看人找车信息
     */
    public function personCarDetail()
    {
        $data = $this->request->param();
        $result = PersoncarModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 人找车列表
     */
    public function personCarList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']],
            ['gather|position|explain', 'like', "%$keyword%"],
        ];
        
        $data_list = PersoncarModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topPersonCar()
    {
        $data = $this->request->param();
        
        $result = PersoncarModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
