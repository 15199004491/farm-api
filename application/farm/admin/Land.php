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
    /**
     * 招工发布
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
     * 查看招工信息
     */
    public function landDetail()
    {
        $data = $this->request->param();
        $result = LandModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 招工分页列表
     */
    public function landList()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']]
        ];
        
        $data_list = LandModel::where($map_data)->order('update_time desc')->limit($data['start'], $data['end'])->select();

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
