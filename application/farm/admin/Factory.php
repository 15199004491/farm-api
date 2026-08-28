<?php

namespace app\farm\admin;

use app\farm\model\Factory as FactoryModel;
use app\common\controller\Common;

class Factory extends Common
{
    private function parseJsonField($data, $key)
    {
        if (!is_array($data) || !isset($data[$key])) {
            return [];
        }
        if (is_array($data[$key])) {
            return $data[$key];
        }
        if (is_string($data[$key])) {
            return json_decode($data[$key], true) ?: [];
        }
        return [];
    }

    private function extractCoords($location)
    {
        $lat = 0;
        $lng = 0;

        if (is_array($location) && !empty($location)) {
            $lat = isset($location['latitude'])  ? floatval($location['latitude'])  : (isset($location['lat']) ? floatval($location['lat']) : 0);
            $lng = isset($location['longitude']) ? floatval($location['longitude']) : (isset($location['lng']) ? floatval($location['lng']) : 0);
        }

        if (($lat == 0 && $lng == 0) && !empty($location)) {
            $raw = is_string($location) ? $location : json_encode($location, JSON_UNESCAPED_UNICODE);
            if (preg_match('/(?:latitude|lat)\s*[:=]\s*(-?\d+(?:\.\d+)?)/i', $raw, $m)) {
                $lat = floatval($m[1]);
            }
            if (preg_match('/(?:longitude|lng)\s*[:=]\s*(-?\d+(?:\.\d+)?)/i', $raw, $m)) {
                $lng = floatval($m[1]);
            }
        }

        return compact('lat', 'lng');
    }

    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function formatCategory($item)
    {
        if (is_array($item)) {
            $item['category'] = $this->parseJsonField($item, 'category');
            $item['location'] = $this->parseJsonField($item, 'location');
        }
        return $item;
    }

    private function formatCategoryList($list)
    {
        foreach ($list as &$item) {
            $item = $this->formatCategory($item);
        }
        unset($item);
        return $list;
    }

    /**
     * 加工厂列表（支持分页、搜索、认证状态筛选）
     */
    public function factoryList()
    {
        $data = $this->request->param();

        $page      = isset($data['page'])      ? intval($data['page'])      : 1;
        $limit     = isset($data['limit'])     ? intval($data['limit'])     : 10;
        $keyword   = isset($data['keyword'])   ? trim($data['keyword'])     : '';
        $distance  = isset($data['distance'])  ? $data['distance']          : '';
        $latitude  = isset($data['latitude'])  ? $data['latitude']          : ($data['lat'] ?? '');
        $longitude = isset($data['longitude']) ? $data['longitude']         : ($data['lng'] ?? '');

        $map = [];
        if ($keyword !== '') {
            $map[] = ['name|category', 'like', "%{$keyword}%"];
        }

        $allRows = FactoryModel::where($map)
            ->order('update_time desc')
            ->limit(10000)
            ->select()
            ->toArray();

        $allRows = $this->formatCategoryList($allRows);

        if ($distance !== '' && $latitude !== '' && $longitude !== '') {
            $radius  = floatval($distance);
            $userLat = floatval($latitude);
            $userLng = floatval($longitude);

            if ($radius > 0 && !($userLat == 0 && $userLng == 0)) {
                $filtered = [];
                foreach ($allRows as $row) {
                    $coords = $this->extractCoords($row['location'] ?? null);
                    if ($coords['lat'] == 0 && $coords['lng'] == 0) {
                        continue;
                    }
                    $dist = $this->haversineDistance($userLat, $userLng, $coords['lat'], $coords['lng']);
                    if ($dist <= $radius) {
                        $row['_distance'] = round($dist, 2);
                        $filtered[] = $row;
                    }
                }
                $allRows = $filtered;
            }
        }

        $total = count($allRows);
        $start = ($page - 1) * $limit;
        $list  = array_slice($allRows, $start, $limit);

        return $this->json_result(compact('total', 'page', 'limit', 'list'), 200, '操作成功');
    }

    /**
     * 当前用户发布的加工厂列表
     * 参数：open_id
     */
    public function factorySelf()
    {
        $data   = $this->request->param();
        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        if (empty($openId)) {
            return $this->json_result('', 403, '无权限查看');
        }

        $list = FactoryModel::where('open_id', $openId)
            ->order('update_time desc')
            ->select()
            ->toArray();

        $list = $this->formatCategoryList($list);

        return $this->json_result($list, 200, '操作成功');
    }

    /**
     * 加工厂详情（含浏览量统计）
     */
    public function factoryDetail()
    {
        $data = $this->request->param();
        $id   = isset($data['Id']) ? intval($data['Id']) : 0;

        $result = FactoryModel::where('Id', $id)->find();

        if (!$result) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        $time = date('Y-m-d');
        if ($time == $result['visit_time']) {
            $today_count = $result['today_count'] + 1;
        } else {
            $result['visit_time'] = $time;
            $today_count = 1;
        }
        $count = $result['count'] + 1;
        FactoryModel::where('Id', $id)->update([
            'count'       => $count,
            'today_count' => $today_count,
            'visit_time'  => $result['visit_time'],
        ]);

        $result['count']       = $count;
        $result['today_count'] = $today_count;

        $result = $this->formatCategory($result->toArray());

        return $this->json_result($result, 200, '操作成功');
    }

    /**
     * 新增 / 编辑 加工厂（入驻）
     * 参数：全部表单字段 + open_id（发布者openid）
     */
    public function addFactory()
    {
        $data   = $this->request->param();
        $id     = isset($data['id']) ? intval($data['id']) : 0;
        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        $data = $this->normalizeCategory($data);

        $data['update_time'] = time();

        if ($id > 0) {
            return $this->updateFactory($data, $id, $openId);
        }

        return $this->createFactory($data, $openId);
    }

    private function normalizeCategory($data)
    {
        if (isset($data['category']) && is_array($data['category'])) {
            $cleaned = [];
            foreach ($data['category'] as $cat) {
                if (empty($cat['name']) || empty($cat['price'])) {
                    continue;
                }
                $cleaned[] = [
                    'name'   => $cat['name'],
                    'status' => $cat['status'],
                    'price'  => $cat['price'],
                    'unit'   => isset($cat['unit']) ? $cat['unit'] : '公斤',
                    'remark' => isset($cat['remark']) ? $cat['remark'] : '',
                ];
            }
            $data['category'] = json_encode($cleaned, JSON_UNESCAPED_UNICODE);
        }
        if (isset($data['location']) && is_array($data['location'])) {
            $data['location'] = json_encode($data['location'], JSON_UNESCAPED_UNICODE);
        }
        return $data;
    }

    private function updateFactory($data, $id, $openId)
    {
        $factory = FactoryModel::where('Id', $id)->find();

        if (!$factory) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        if ($openId === '' || $factory['open_id'] !== $openId) {
            return $this->json_result('', 403, '无权修改该加工厂');
        }

        unset($data['open_id']);

        $data['identification'] = null;

        $result = FactoryModel::where('Id', $id)->update($data);

        if ($result === false) {
            return $this->json_result('', 500, '修改失败');
        }

        return $this->json_result(['Id' => $id], 200, '修改成功');
    }

    private function createFactory($data, $openId)
    {
        if ($openId === '') {
            return $this->json_result('', 403, '无权限创建');
        }

        $data['create_time'] = time();
        $data['open_id']     = $openId;
        $data['count']       = 0;
        $data['today_count'] = 0;
        $data['visit_time']  = date('Y-m-d');
        $data['verified']    = isset($data['verified']) ? intval($data['verified']) : 0;
        $data['top_start']   = 0;
        $data['top_end']     = 0;

        $newId = FactoryModel::insertGetId($data);

        if (!$newId) {
            return $this->json_result('', 500, '添加失败');
        }

        return $this->json_result(['Id' => $newId], 200, '添加成功');
    }

    /**
     * 删除加工厂
     * 参数：Id, open_id（验证是否本人发布）
     */
    public function deleteFactory()
    {
        $data   = $this->request->param();
        $id     = isset($data['Id']) ? intval($data['Id']) : 0;
        $openId = isset($data['open_id']) ? $data['open_id'] : '';

        $factory = FactoryModel::where('Id', $id)->find();
        if (!$factory) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        if ($openId === '' || $factory['open_id'] !== $openId) {
            return $this->json_result('', 403, '无权删除该加工厂');
        }

        $result = FactoryModel::where('Id', $id)->delete();

        if ($result !== false) {
            return $this->json_result('', 200, '删除成功');
        }
        return $this->json_result('', 500, '删除失败');
    }

    /**
     * 加工厂发布（通知+备注+品类 一次性保存）
     */
    public function publishFactoryInfo()
    {
        $data = $this->request->param();
        $id   = isset($data['id']) ? intval($data['id']) : 0;

        $factory = FactoryModel::where('Id', $id)->find();
        if (!$factory) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        $updateData = ['update_time' => time()];

        if (isset($data['notice'])) {
            $updateData['notice'] = $data['notice'];
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            $data['category'] = $data['categories'];
            $data = $this->normalizeCategory($data);
            $updateData['category'] = $data['category'];
        }

        FactoryModel::where('Id', $id)->update($updateData);

        $result = FactoryModel::where('Id', $id)->find();
        $result = $this->formatCategory($result->toArray());

        return $this->json_result($result, 200, '发布成功');
    }

    /**
     * 加工厂认证
     * 参数：open_id, license（营业执照号）, id_card（身份证号）, Id（可选，加工厂ID）
     */
    public function verifyFactory()
    {
        $data   = $this->request->param();
        $openId = isset($data['open_id']) ? $data['open_id'] : '';
        $license = isset($data['license']) ? trim($data['license']) : '';
        $idCard  = isset($data['id_card']) ? trim($data['id_card']) : '';
        $id      = isset($data['Id']) ? intval($data['Id']) : 0;

        if ($openId === '') {
            return $this->json_result('', 403, '无权限操作');
        }

        if ($license === '' || $idCard === '') {
            return $this->json_result('', 400, '请填写完整认证信息');
        }

        if ($id > 0) {
            $factory = FactoryModel::where('Id', $id)
                ->where('open_id', $openId)
                ->find();
        } else {
            $factory = FactoryModel::where('open_id', $openId)->find();
        }

        if (!$factory) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        $result = FactoryModel::where('Id', $factory['Id'])->update([
            'license'      => $license,
            'id_card'      => $idCard,
            'identification'     => 0,
            'update_time'  => time(),
        ]);

        if ($result === false) {
            return $this->json_result('', 500, '认证失败');
        }

        return $this->json_result(['Id' => $factory['Id']], 200, '认证成功');
    }

    /**
     * 生成加工厂小程序码
     * 参数：Id(加工厂ID), page(小程序页面路径，可选，默认pages/factory/detail), width(图片宽度，可选，默认430)
     * 返回：base64编码的小程序码图片
     */
    public function generateFactoryQrcode()
    {
        $data  = $this->request->param();
        $id    = isset($data['Id']) ? intval($data['Id']) : 0;
        $page  = isset($data['page']) ? trim($data['page']) : 'pages/factory/detail';
        $width = isset($data['width']) ? intval($data['width']) : 430;

        if ($id <= 0) {
            return $this->json_result('', 400, '加工厂ID不能为空');
        }

        $factory = FactoryModel::where('Id', $id)->find();
        if (!$factory) {
            return $this->json_result('', 404, '加工厂不存在');
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return $this->json_result('', 500, '获取access_token失败');
        }

        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token={$token}";

        $postData = [
            'scene'      => strval($id),
            'page'       => $page,
            'width'      => $width,
            'auto_color' => false,
            'line_color' => ['r' => 0, 'g' => 0, 'b' => 0],
            'is_hyaline' => false,
        ];

        $response = $this->http_request($url, $postData, true);

        $result = json_decode($response, true);
        if (is_array($result) && isset($result['errcode']) && $result['errcode'] !== 0) {
            $errmsg = isset($result['errmsg']) ? $result['errmsg'] : '生成小程序码失败';
            return $this->json_result('', 500, $errmsg);
        }

        $base64 = 'data:image/jpeg;base64,' . base64_encode($response);

        return $this->json_result(['qrcode' => $base64], 200, '生成成功');
    }

    
}