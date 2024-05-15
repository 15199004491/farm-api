<?php

namespace app\farm\admin;

use app\farm\model\Factory as FactoryModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Factory extends Common
{
    /**
     * 个人名下的加工厂列表
     */
    public function factorySelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = FactoryModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 添加加工厂信息
     */
    public function addFactory()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = FactoryModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = FactoryModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    // 切换加工厂的状态
    public function remit()
    {
        $data = $this->request->param();
        $param = FactoryModel::where('Id', $data['id'])->update($data);
        return $this->json_result($param, 200, '操作成功');
    }
    /**
     * 查看加工厂信息
     */
    public function factoryDetail()
    {
        $data = $this->request->param();
        $result = FactoryModel::where('Id', $data['Id'])->find();
        $count = $result['count'] + 1;
        $result['count'] = $count;
        FactoryModel::where('Id', $data['Id'])->update(['count' => $count]);
        return $this->json_return($result);
    }
     /**
     * 加工厂列表
     */
    public function factoryList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['name|explain', 'like', "%$keyword%"],
        ];
        
        $data_list = FactoryModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topFactory()
    {
        $data = $this->request->param();
        
        $result = FactoryModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
    /**
     * 删除加工厂信息
     */
    public function deleteFactory()
    {
        $data = $this->request->param();
        $result = FactoryModel::where('Id', $data['Id'])->delete();
        return $this->json_return($result);
    }
}
