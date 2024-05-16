<?php

namespace app\farm\admin;

use app\farm\model\Rent as RentModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Rent extends Common
{
    // 删除信息
    public function deleteRent()
    {
        $data = $this->request->param();
        
        $result = RentModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的二手房列表
     */
    public function rentSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = RentModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 二手房发布
     */
    public function addRent()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = RentModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = RentModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看二手房信息
     */
    public function rentDetail()
    {
        $data = $this->request->param();
        $result = RentModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 二手房列表
     */
    public function rentList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']],
            ['name|explain', 'like', "%$keyword%"]
        ];
        
        $data_list = RentModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topRent()
    {
        $data = $this->request->param();
        
        $result = RentModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
