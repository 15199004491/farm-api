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
    // 切换加工厂至已打款状态
    public function remit()
    {
        $data = $this->request->param();
        $param = FactoryModel::where('Id', $data['id'])->update(['identification' => $data['state']]);
        return $this->json_return($param);
    }
    /**
     * 查看加工厂信息
     */
    public function factoryDetail()
    {
        $data = $this->request->param();
        $result = FactoryModel::where('publisher', $data['publisher'])->find();
        return $this->json_return($result);
    }
     /**
     * 加工厂分页列表
     */
    public function factoryList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['info|explain', 'like', "%$keyword%"],
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
}
