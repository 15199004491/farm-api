<?php

namespace app\farm\admin;

use app\farm\model\Purchase as PurchaseModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Purchase extends Common
{
    /**
     * 收购发布
     */
    public function addPurchase()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = PurchaseModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = PurchaseModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看收购信息
     */
    public function purchaseDetail()
    {
        $data = $this->request->param();
        $result = PurchaseModel::where('Id', $data['Id'])->find();
        return $this->json_return($result);
    }
     /**
     * 收购分页列表
     */
    public function purchaseList()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']]
        ];
        
        $data_list = PurchaseModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topPurchase()
    {
        $data = $this->request->param();
        
        $result = PurchaseModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
