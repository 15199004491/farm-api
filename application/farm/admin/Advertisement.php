<?php

namespace app\farm\admin;

use app\farm\model\Advertisement as AdvertisementModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Advertisement extends Common
{
    public function publishAdvertise()
    {
        $data = $this->request->param();
        $data['update_time'] = time();
        if(isset($data['Id'])) {
            $param = AdvertisementModel::where('Id', $data['Id'])->update($data);
        } else {
            $data['create_time'] = time();
            $param = AdvertisementModel::insertGetId($data);
        }
       
        return $this->json_return($param);
    }
    public function advertisingSelf()
    {
        $data = $this->request->param();
        $publisher = $data['publisher'];

        // 默认信息查询条件
        $map_data = [
            ['publisher', '=', $publisher],
        ];
        
        $data_list = AdvertisementModel::where($map_data)->select();

        return $this->json_result($data_list, 200, '操作成功');
    }
    public function advertisingDetail()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $map_data = [
            ['Id', '=', $data['Id']],
        ];
        
        $result = AdvertisementModel::where($map_data)->find();

        return $this->json_result($result, 200, '操作成功');
    }
    // 获取当前页面的轮播图
    public function advertisingPage()
    {
        $data = $this->request->param();

        // 默认信息查询条件
        $area = $data['area'];
        $map_data = [
            ['page', '=', $data['page']],
            ['target_area', 'like', "%$area%"],
            ['end', '>', time()]
        ];
        
        $result = AdvertisementModel::where($map_data)->select();

        return $this->json_result($result, 200, '操作成功');
    }
}
