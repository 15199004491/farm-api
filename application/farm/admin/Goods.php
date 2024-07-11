<?php

namespace app\farm\admin;

use app\farm\model\Goods as GoodsModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Goods extends Common
{
    public function publishGoods()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = GoodsModel::where('Id', $data['Id'])->update($data);
        } else {
            $data['create_time'] = time();
            $param = GoodsModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    public function goodsSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = GoodsModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    }
    public function goodsDetail()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['Id', '=', $data['Id']],
        ];
        
        $result = GoodsModel::where($map_data)->find();

        return $this->json_result($result, 200, '操作成功');
    }
    // 获取当前页面的农资
    public function goodsPage()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $area = $data['area'];
        $map_data = [
            ['title|desc|explain', 'like', "%$keyword%"],
            ['target_area', 'like', "%$area%"],
            ['end', '>', time()]
        ];
        
        $result = GoodsModel::where($map_data)->select();

        return $this->json_result($result, 200, '操作成功');
    }
    public function topGoods()
    {
        $data = $this->request->param();
        
        $result = GoodsModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
    public function deleteGoods()
    {
        $data = $this->request->param();
        
        $result = GoodsModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
}
