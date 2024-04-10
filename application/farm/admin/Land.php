<?php

namespace app\farm\admin;

use app\farm\model\Land as LandModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Land extends Common
{
    // 删除信息
    public function deleteLand()
    {
        $data = $this->request->param();
        
        $result = LandModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的土地列表
     */
    public function landSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = LandModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 土地发布
     */
    public function addLand()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = LandModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = LandModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看土地信息
     */
    public function landDetail()
    {
        $data = $this->request->param();
        $result = LandModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 土地列表
     */
    public function landList()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']]
        ];
        
        $data_list = LandModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topLand()
    {
        $data = $this->request->param();
        
        $result = LandModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
