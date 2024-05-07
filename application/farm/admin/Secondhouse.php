<?php

namespace app\farm\admin;

use app\farm\model\SecondHouse as SecondHouseModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Secondhouse extends Common
{
    // 删除信息
    public function deleteHouse()
    {
        $data = $this->request->param();
        
        $result = SecondHouseModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的二手房列表
     */
    public function houseSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = SecondHouseModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 二手房发布
     */
    public function addHouse()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = SecondHouseModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = SecondHouseModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看二手房信息
     */
    public function houseDetail()
    {
        $data = $this->request->param();
        $result = SecondHouseModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 二手房列表
     */
    public function houseList()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']]
        ];
        
        $data_list = SecondHouseModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topHouse()
    {
        $data = $this->request->param();
        
        $result = SecondHouseModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
