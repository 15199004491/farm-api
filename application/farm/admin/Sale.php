<?php

namespace app\farm\admin;

use app\farm\model\Sale as SaleModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Sale extends Common
{
    /**
     * 出售发布
     */
    public function addSale()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = SaleModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = SaleModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看出售信息
     */
    public function saleDetail()
    {
        $data = $this->request->param();
        $result = SaleModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 出售分页列表
     */
    public function saleList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['title|explain', 'like', "%$keyword%"],
            ['area', '=', $data['area']]
        ];
        
        $data_list = SaleModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topSale()
    {
        $data = $this->request->param();
        
        $result = SaleModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
