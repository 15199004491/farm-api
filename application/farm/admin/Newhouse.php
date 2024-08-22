<?php

namespace app\farm\admin;

use app\farm\model\NewHouse as NewHouseModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class NewHouse extends Common
{
    // 删除信息
    public function deleteNewHouse()
    {
        $data = $this->request->param();
        
        $result = NewHouseModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的新房列表
     */
    public function newHouseSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = NewHouseModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 新房发布
     */
    public function addNewHouse()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = NewHouseModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = NewHouseModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    // 投放区域
    public function regionalDelivery()
    {
        $data = $this->request->param();
        $data['payment_time'] = time();
        $param = NewHouseModel::where('Id', $data['Id'])->update($data);
        return $this->json_return($param);
    }
    /**
     * 查看新房信息
     */
    public function newHouseDetail()
    {
        $data = $this->request->param();
        $result = NewHouseModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
    /**
     * 新房列表
     */
    public function newHouseList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $area = $data['area'];
        $map_data = [
            ['name|explain', 'like', "%$keyword%"],
            ['target_area', 'like', "%$area%"],
            ['end', '>', time()]
        ];
        
        $result = NewHouseModel::where($map_data)->select();

        return $this->json_result($result, 200, '操作成功');
    }
    // 置顶信息
    public function topHouse()
    {
        $data = $this->request->param();
        
        $result = NewHouseModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
