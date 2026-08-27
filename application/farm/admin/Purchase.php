<?php

namespace app\farm\admin;

use app\farm\model\Purchase as PurchaseModel;
use app\farm\model\Person as PersonModel;
use app\common\controller\Common;

/**
 * 
 * @package
 */
class Purchase extends Common
{
    // 删除信息
    public function deletePurchase()
    {
        $data = $this->request->param();
        
        $result = PurchaseModel::where('Id', $data['Id'])->delete();
        
        return $this->json_return($result);
    }
    /**
     * 当前人发布的个人收购列表
     */
    public function purchaseSelf()
    {
        $data = $this->request->param();
        $open_id = $data['open_id'];

        $map_data = [
            ['open_id', '=', $open_id],
        ];
        
        $data_list = PurchaseModel::where($map_data)->select()->toArray();
        $data_list = $this->formatCategoriesList($data_list);

        return $this->json_result($data_list, 200, '操作成功');
    }
    /**
     * 收购发布
     */
    public function addPurchase()
    {
        $data = $this->request->param();
        $data['update_time'] = time();

        if (isset($data['categories']) && is_array($data['categories'])) {
            $data['categories'] = json_encode($data['categories'], JSON_UNESCAPED_UNICODE);
        }

        if(isset($data['id'])) {
            $param = PurchaseModel::where('Id', $data['id'])->update($data);
        } else {
            $data['create_time'] = time();
            $param = PurchaseModel::insertGetId($data);
            if ($param && isset($data['open_id'])) {
                $user = PersonModel::where('open_id', $data['open_id'])->find();
                if ($user) {
                    $purchaseIds = $user['purchase_ids'] ? explode(',', $user['purchase_ids']) : [];
                    $purchaseIds[] = $param;
                    PersonModel::where('Id', $user['Id'])->update([
                        'purchase_ids' => implode(',', array_filter($purchaseIds))
                    ]);
                }
            }
        }
       
        return $this->json_return($param);
    }
    /**
     * 收购详情（含浏览量统计）
     * 参数：Id
     */
    public function purchaseDetail()
    {
        $data = $this->request->param();
        $id   = isset($data['Id']) ? intval($data['Id']) : 0;

        if ($id <= 0) {
            return $this->json_result('', 400, '参数错误');
        }

        $result = PurchaseModel::where('Id', $id)->find();

        if (!$result) {
            return $this->json_result('', 404, '收购信息不存在');
        }

        $time = date('Y-m-d');
        if ($time == $result['visit_time']) {
            $today_count = $result['today_count'] + 1;
        } else {
            $result['visit_time'] = $time;
            $today_count = 1;
        }
        $count = $result['count'] + 1;
        PurchaseModel::where('Id', $id)->update([
            'count'       => $count,
            'today_count' => $today_count,
            'visit_time'  => $result['visit_time'],
        ]);

        $result['count']       = $count;
        $result['today_count'] = $today_count;

        $result = $this->formatCategoriesItem($result->toArray());

        return $this->json_result($result, 200, '操作成功');
    }
    /**
     * 收购列表（支持分页、搜索、地区筛选）
     * 参数：page, limit, keyword, region
     */
    public function purchaseList()
    {
        $data = $this->request->param();

        $page    = isset($data['page'])    ? intval($data['page'])    : 1;
        $limit   = isset($data['limit'])   ? intval($data['limit'])   : 10;
        $keyword = isset($data['keyword']) ? trim($data['keyword'])   : '';
        $region  = isset($data['region'])  ? trim($data['region'])    : '';

        $map = [];

        if ($keyword !== '') {
            $map[] = ['title|categories', 'like', "%{$keyword}%"];
        }
        if ($region !== '') {
            $map[] = ['region', 'like', "%{$region}%"];
        }

        $total = PurchaseModel::where($map)->count();
        $list  = PurchaseModel::where($map)
            ->order('update_time desc')
            ->page($page, $limit)
            ->select()
            ->toArray();
        $list = $this->formatCategoriesList($list);

        $result = [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'list'  => $list,
        ];

        return $this->json_result($result, 200, '操作成功');
    } 
    // 置顶信息
    public function topPurchase()
    {
        $data = $this->request->param();
        
        $result = PurchaseModel::where('Id', $data['Id'])->update(['top_start' => $data['top_start'],'top_end' => $data['top_end']]);
        
        return $this->json_return($result);
    }

    private function formatCategoriesItem($item)
    {
        if (is_array($item) && isset($item['categories']) && is_string($item['categories'])) {
            $item['categories'] = json_decode($item['categories'], true) ?: [];
        }
        return $item;
    }

    private function formatCategoriesList($list)
    {
        foreach ($list as &$item) {
            $item = $this->formatCategoriesItem($item);
        }
        unset($item);
        return $list;
    }
}