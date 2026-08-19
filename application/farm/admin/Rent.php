<?php

namespace app\farm\admin;

use app\farm\model\Rent as RentModel;
use app\common\controller\Common;

/**
 * 出租房接口
 * @package app\farm\admin
 */
class Rent extends Common
{
    /**
     * 获取出租房列表（支持分页、搜索、地区筛选）
     * 参数：page, limit, keyword, region
     */
    public function rentList()
    {
        $data = $this->request->param();

        $page  = isset($data['page'])  ? intval($data['page'])  : 1;
        $limit = isset($data['limit']) ? intval($data['limit']) : 10;
        $keyword = isset($data['keyword']) ? trim($data['keyword']) : '';
        $region  = isset($data['region'])  ? trim($data['region'])  : '';

        $map = [];

        if ($keyword !== '') {
            $map[] = ['name|explain', 'like', "%{$keyword}%"];
        }
        if ($region !== '') {
            $map[] = ['area', 'like', "%{$region}%"];
        }

        $total = RentModel::where($map)->count();
        $list  = RentModel::where($map)
            ->order('update_time desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        $result = [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'list'  => $list,
        ];

        return $this->json_result($result, 200, '操作成功');
    }

    /**
     * 当前用户发布的出租房列表
     * 参数：open_id(用户ID)
     */
    public function rentSelf()
    {
        $data = $this->request->param();
        $open_id = isset($data['open_id']) ? $data['open_id'] : 0;

        $map = [
            ['open_id', '=', $open_id],
        ];

        $list = RentModel::where($map)
            ->order('update_time desc')
            ->select()
            ->toArray();

        return $this->json_result($list, 200, '操作成功');
    }

    /**
     * 出租房详情
     * 参数：Id
     */
    public function rentDetail()
    {
        $data = $this->request->param();
        $id   = isset($data['Id']) ? intval($data['Id']) : 0;

        if ($id <= 0) {
            return $this->json_result('', 400, '参数错误');
        }

        $result = RentModel::where('Id', $id)->find();

        if (!$result) {
            return $this->json_result('', 404, '房源不存在');
        }

        $count = $result['count'] + 1;
        RentModel::where('Id', $id)->update(['count' => $count]);
        $result['count'] = $count;

        return $this->json_result($result, 200, '操作成功');
    }

    /**
     * 新增 / 编辑 出租房
     * 参数：全部表单字段，带 Id 为编辑，不带 Id 为新增
     */
    public function addRent()
    {
        $data = $this->request->param();

        $id = isset($data['Id']) ? intval($data['Id']) : 0;

        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        $data['update_time'] = time();

        if ($id > 0) {
            $rent = RentModel::where('Id', $id)->find();
            if (!$rent) {
                return $this->json_result('', 404, '房源不存在');
            }
            if ($openId && $rent['open_id'] !== $openId) {
                return $this->json_result('', 403, '无权修改该房源');
            }
            unset($data['open_id']);

            $result = RentModel::where('Id', $id)->update($data);
            if ($result !== false) {
                return $this->json_result(['Id' => $id], 200, '修改成功');
            }
            return $this->json_result('', 500, '修改失败');
        } else {
            $data['create_time'] = time();
            $data['open_id']     = $openId;
            $data['count']       = 0;
            $data['top_start']   = 0;
            $data['top_end']     = 0;
            $newId = RentModel::insertGetId($data);
            if ($newId) {
                return $this->json_result(['Id' => $newId], 200, '发布成功');
            }
            return $this->json_result('', 500, '发布失败');
        }
    }

    /**
     * 删除出租房
     * 参数：Id
     */
    public function deleteRent()
    {
        $data   = $this->request->param();
        $id     = isset($data['Id']) ? intval($data['Id']) : 0;
        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        if ($id <= 0) {
            return $this->json_result('', 400, '参数错误');
        }

        $rent = RentModel::where('Id', $id)->find();
        if (!$rent) {
            return $this->json_result('', 404, '房源不存在');
        }

        if ($openId && $rent['open_id'] !== $openId) {
            return $this->json_result('', 403, '无权删除该房源');
        }

        $result = RentModel::where('Id', $id)->delete();

        if ($result !== false) {
            return $this->json_result('', 200, '删除成功');
        }
        return $this->json_result('', 500, '删除失败');
    }

    /**
     * 置顶出租房
     * 参数：Id, top_start, top_end
     */
    public function topRent()
    {
        $data   = $this->request->param();
        $id     = isset($data['Id']) ? intval($data['Id']) : 0;
        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        if ($id <= 0) {
            return $this->json_result('', 400, '参数错误');
        }

        $rent = RentModel::where('Id', $id)->find();
        if (!$rent) {
            return $this->json_result('', 404, '房源不存在');
        }

        if ($openId && $rent['open_id'] !== $openId) {
            return $this->json_result('', 403, '无权操作该房源');
        }

        $result = RentModel::where('Id', $id)->update([
            'top_start' => isset($data['top_start']) ? $data['top_start'] : 0,
            'top_end'   => isset($data['top_end'])   ? $data['top_end']   : 0,
        ]);

        if ($result !== false) {
            return $this->json_result('', 200, '置顶成功');
        }
        return $this->json_result('', 500, '置顶失败');
    }
}