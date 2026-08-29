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

    private function generateCircleAvatar($text, $size = 200)
    {
        $img = imagecreatetruecolor($size, $size);
        imagesavealpha($img, true);

        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);

        $green = imagecolorallocate($img, 34, 197, 94);
        $white = imagecolorallocate($img, 255, 255, 255);

        $centerX = $size / 2;
        $centerY = $size / 2;
        $radius  = $size / 2 - 2;

        imagefilledellipse($img, $centerX, $centerY, $radius * 2, $radius * 2, $green);

        $text = trim($text);
        if ($text === '') {
            return $img;
        }

        $mbLen = mb_strlen($text, 'UTF-8');
        if ($mbLen <= 4) {
            $fontSize = max(10, intval($size * 0.18));
            $singleCharWidth = $fontSize * 0.9;
            $totalWidth = $mbLen * $singleCharWidth;
            $startX = $centerX - $totalWidth / 2 + $singleCharWidth / 2;
            $y = $centerY + $fontSize / 3;
            for ($i = 0; $i < $mbLen; $i++) {
                $char = mb_substr($text, $i, 1, 'UTF-8');
                $x = $startX + $i * $singleCharWidth;
                imagettftext($img, $fontSize, 0, intval($x - $fontSize * 0.45), intval($y), $white, $this->getFontPath(), $char);
            }
        } else {
            $centerAngleOffset = -90;
            $mbLen = mb_strlen($text, 'UTF-8');

            $totalArc = 300;
            if ($mbLen <= 6) {
                $totalArc = 240;
            } elseif ($mbLen <= 10) {
                $totalArc = 300;
            } else {
                $totalArc = 340;
            }

            $baseFontSize = $size * 0.11;
            if ($mbLen > 15) {
                $baseFontSize = $size * 0.09;
            } elseif ($mbLen > 10) {
                $baseFontSize = $size * 0.095;
            } elseif ($mbLen > 6) {
                $baseFontSize = $size * 0.10;
            }
            $fontSize = max(10, intval($baseFontSize));

            $textRadius = $radius - 10 - $fontSize * 0.55;

            $startAngle = $centerAngleOffset - $totalArc / 2;
            $angleStep  = $totalArc / ($mbLen - 1);

            for ($i = 0; $i < $mbLen; $i++) {
                $angle = $startAngle + $i * $angleStep;
                $rad = deg2rad($angle);
                $char = mb_substr($text, $i, 1, 'UTF-8');

                $x = $centerX + $textRadius * cos($rad);
                $y = $centerY + $textRadius * sin($rad);

                $charAngle = $angle + 90;

                $charImg = imagecreatetruecolor($fontSize * 3, $fontSize * 3);
                imagesavealpha($charImg, true);
                $charTransparent = imagecolorallocatealpha($charImg, 0, 0, 0, 127);
                imagefill($charImg, 0, 0, $charTransparent);

                imagettftext($charImg, $fontSize, 0, intval($fontSize * 1.0), intval($fontSize * 2.0), $white, $this->getFontPath(), $char);
                $rotated = imagerotate($charImg, -$charAngle, $charTransparent);
                imagealphablending($rotated, true);
                imagesavealpha($rotated, true);

                $w = imagesx($rotated);
                $h = imagesy($rotated);
                imagecopy($img, $rotated, intval($x - $w / 2), intval($y - $h / 2), 0, 0, $w, $h);

                imagedestroy($charImg);
                imagedestroy($rotated);
            }

            $centerText = '';
            if ($mbLen >= 10) {
                $centerText = mb_substr($text, 0, 2, 'UTF-8');
            } elseif ($mbLen >= 6) {
                $centerText = mb_substr($text, 0, 1, 'UTF-8');
            }

            if ($centerText !== '') {
                $centerFontSize = intval($size * 0.18);
                $box = imagettfbbox($centerFontSize, 0, $this->getFontPath(), $centerText);
                $textWidth = $box[2] - $box[0];
                $textHeight = $box[1] - $box[7];
                $cx = $centerX - $textWidth / 2;
                $cy = $centerY + $textHeight / 2;
                imagettftext($img, $centerFontSize, 0, intval($cx), intval($cy), $white, $this->getFontPath(), $centerText);
            }
        }

        return $img;
    }

    private function getFontPath()
    {
        $candidates = [
            'C:/Windows/Fonts/msyh.ttc',
            dirname(__FILE__) . '/../../../public/static/fonts/msyh.ttc',
            dirname(__FILE__) . '/../../../public/static/fonts/msyh.ttf',
            dirname(__FILE__) . '/../../../public/static/fonts/simhei.ttf',
            dirname(__FILE__) . '/../../../public/static/fonts/simsun.ttc',
            'C:/Windows/Fonts/simhei.ttf',
            'C:/Windows/Fonts/simsun.ttc',
            '/usr/share/fonts/truetype/wqy/wqy-microhei.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'C:/Windows/Fonts/msyh.ttc';
    }

    private function mergeAvatarToQrcode($qrcodeData, $avatarImg)
    {
        $qrcodeImg = imagecreatefromstring($qrcodeData);
        if (!$qrcodeImg) {
            return $qrcodeData;
        }

        $qw = imagesx($qrcodeImg);
        $qh = imagesy($qrcodeImg);

        $baseDim = min($qw, $qh);

        $overlayRatio = 0.43;

        $overlaySize = intval($baseDim * $overlayRatio);

        $resizedAvatar = imagecreatetruecolor($overlaySize, $overlaySize);
        imagesavealpha($resizedAvatar, true);
        $transparent = imagecolorallocatealpha($resizedAvatar, 0, 0, 0, 127);
        imagefill($resizedAvatar, 0, 0, $transparent);
        imagecopyresampled($resizedAvatar, $avatarImg, 0, 0, 0, 0, $overlaySize, $overlaySize, imagesx($avatarImg), imagesy($avatarImg));

        $overlayX = intval(($qw - $overlaySize) / 2);
        $overlayY = intval(($qh - $overlaySize) / 2);

        imagecopy($qrcodeImg, $resizedAvatar, $overlayX, $overlayY, 0, 0, $overlaySize, $overlaySize);

        imagedestroy($resizedAvatar);
        imagedestroy($avatarImg);

        ob_start();
        imagejpeg($qrcodeImg, null, 90);
        $output = ob_get_clean();
        imagedestroy($qrcodeImg);

        return $output;
    }

    /**
     * 生成加工厂小程序码
     * 参数：Id(加工厂ID), page(小程序页面路径，可选，默认pages/factory/detail), width(图片宽度，可选，默认430)
     *       name(环形文字，可选，默认取加工厂名称)
     * 返回：base64编码的小程序码图片
     */
    public function generateFactoryQrcode()
    {
        $data  = $this->request->param();
        $id    = isset($data['Id']) ? intval($data['Id']) : 0;
        $page  = isset($data['page']) ? trim($data['page']) : 'pages/factory/detail';
        $width = isset($data['width']) ? intval($data['width']) : 430;
        $name  = isset($data['name']) ? trim($data['name']) : '';

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

        if ($name === '') {
            $name = isset($factory['name']) ? trim($factory['name']) : '';
        }

        if ($name !== '' && function_exists('imagecreatetruecolor')) {
            $avatarSize = 360;
            $avatarImg = $this->generateCircleAvatar($name, $avatarSize);
            if ($avatarImg) {
                $response = $this->mergeAvatarToQrcode($response, $avatarImg);
            }
        }

        $base64 = 'data:image/jpeg;base64,' . base64_encode($response);

        return $this->json_result(['qrcode' => $base64], 200, '生成成功');
    }

    
}