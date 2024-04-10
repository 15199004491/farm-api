<?php

namespace app\farm\admin;

use app\farm\model\Machine as MachineModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Machine extends Common
{
    // 删除信息
    public function deleteMachine()
    {
        $data = $this->request->param();
        
        $result = MachineModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的农机列表
     */
    public function machineSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = MachineModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 农机发布
     */
    public function addMachine()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = MachineModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = MachineModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看农机信息
     */
    public function machineDetail()
    {
        $data = $this->request->param();
        $result = MachineModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 农机列表
     */
    public function machineList()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']]
        ];
        
        $data_list = MachineModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topMachine()
    {
        $data = $this->request->param();
        
        $result = MachineModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
