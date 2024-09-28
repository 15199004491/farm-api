<?php

namespace app\farm\admin;

use app\farm\model\CarPerson as CarPersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Carperson extends Common
{
    // 删除信息
    public function deleteCarPerson()
    {
        $data = $this->request->param();
        
        $result = CarPersonModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的车找人列表
     */
    public function carPersonSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = CarPersonModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    /**
     * 车找人信息发布
     */
    public function addCarPerson()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = CarPersonModel::where('Id', $data['Id'])->update($data);
        } else {
            $param = CarPersonModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    /**
     * 查看车找人信息
     */
    public function carPersonDetail()
    {
        $data = $this->request->param();
        $result = CarPersonModel::where('Id', $data['Id'])->find();
        $count = $result['count'] + 1;
        $result['count'] = $count;
        CarPersonModel::where('Id', $data['Id'])->update(['count' => $count]);
        return $this->json_return($result);
    }
     /**
     * 车找人列表
     */
    public function carPersonList()
    {
        $data = $this->request->param();
        $keyword = $data['keyword'];

        // 默认信息查询条件
        $map_data = [
            ['area', '=', $data['area']],
            ['title|gather|position|explain', 'like', "%$keyword%"],
        ];
        
        $data_list = CarPersonModel::where($map_data)->order('update_time desc')->limit($data['start']-1, $data['end'])->select();

        return $this->json_result($data_list, 200, '操作成功');
    } 
    // 置顶信息
    public function topCarPerson()
    {
        $data = $this->request->param();
        
        $result = CarPersonModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }
}
