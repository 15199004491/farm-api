<?php

namespace app\farm\admin;

use app\farm\model\Factory as FactoryModel;
use app\common\controller\Common;

class Factory extends Common
{
    const INVALID_TOKEN_CODES = [40001, 40014, 42001];

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
                if (empty($cat['name'])) {
                    continue;
                }
                $price = isset($cat['price']) ? $cat['price'] : null;
                if ($price !== null && $price !== '') {
                    $price = floatval($price);
                }
                $cleaned[] = [
                    'name'   => $cat['name'],
                    'status' => isset($cat['status']) ? intval($cat['status']) : 1,
                    'price'  => $price,
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
        $img = $this->createAvatarBase($size);
        $text = trim($text);
        if ($text === '') {
            return $img;
        }

        $fontInfo = $this->resolveFont();
        $mbLen = mb_strlen($text, 'UTF-8');

        if (!$fontInfo['ok']) {
            return $this->drawFallback($img, $text, $size, $fontInfo['path']);
        }

        if ($mbLen <= 4) {
            $this->drawHorizontalText($img, $text, $fontInfo['path'], $size);
        } else {
            $this->drawRadialText($img, $text, $fontInfo['path'], $size);
        }

        return $img;
    }

    private function createAvatarBase($size)
    {
        $img = imagecreatetruecolor($size, $size);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        $green = imagecolorallocate($img, 34, 197, 94);
        $radius = $size / 2 - 2;
        imagefilledellipse($img, $size / 2, $size / 2, $radius * 2, $radius * 2, $green);
        return $img;
    }

    private function resolveFont()
    {
        try {
            $path = $this->getFontPath();
            return ['path' => $path, 'ok' => $this->isFontSupportChinese($path)];
        } catch (\Throwable $e) {
            return ['path' => null, 'ok' => false];
        }
    }

    private function drawFallback($img, $text, $size, $fontPath)
    {
        $white = imagecolorallocate($img, 255, 255, 255);
        $cx = imagesx($img) / 2;
        $cy = imagesy($img) / 2;

        $char = $this->extractFallbackChar($text);
        if ($char === '') {
            return $img;
        }

        $fs = max(5, intval($size * 0.22));
        if (preg_match('/[A-Z0-9]/', $char)) {
            $fw = imagefontwidth(5);
            $fh = imagefontheight(5);
            imagestring($img, 5, intval($cx - $fw / 2), intval($cy - $fh / 2), $char, $white);
        } elseif ($fontPath !== null) {
            $box = @imagettfbbox($fs, 0, $fontPath, $char);
            if (is_array($box)) {
                $tw = intval($box[2]) - intval($box[0]);
                $th = intval($box[1]) - intval($box[7]);
                @imagettftext($img, $fs, 0, intval($cx - $tw / 2), intval($cy + $th / 2), $white, $fontPath, $char);
            }
        }
        return $img;
    }

    private function extractFallbackChar($text)
    {
        $mbLen = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $mbLen; $i++) {
            $ch = mb_substr($text, $i, 1, 'UTF-8');
            if (preg_match('/[A-Za-z0-9]/', $ch)) {
                return strtoupper($ch);
            }
        }
        return $mbLen > 0 ? mb_substr($text, 0, 1, 'UTF-8') : '';
    }

    private function drawHorizontalText($img, $text, $fontPath, $size)
    {
        $white = imagecolorallocate($img, 255, 255, 255);
        $cx = imagesx($img) / 2;
        $cy = imagesy($img) / 2;
        $mbLen = mb_strlen($text, 'UTF-8');
        $fontSize = max(10, intval($size * 0.18));
        $singleCharWidth = $fontSize * 0.9;
        $totalWidth = $mbLen * $singleCharWidth;
        $startX = $cx - $totalWidth / 2 + $singleCharWidth / 2;
        $y = $cy + $fontSize / 3;

        for ($i = 0; $i < $mbLen; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $x = $startX + $i * $singleCharWidth;
            @imagettftext($img, $fontSize, 0, intval($x - $fontSize * 0.45), intval($y), $white, $fontPath, $char);
        }
    }

    private function drawRadialText($img, $text, $fontPath, $size)
    {
        $white = imagecolorallocate($img, 255, 255, 255);
        $cx = imagesx($img) / 2;
        $cy = imagesy($img) / 2;
        $radius = $size / 2 - 2;
        $mbLen = mb_strlen($text, 'UTF-8');

        $totalArc = $this->getArcAngle($mbLen);
        $fontSize = $this->getRadialFontSize($size, $mbLen);
        $textRadius = $radius - 10 - $fontSize * 0.55;
        $startAngle = -90 - $totalArc / 2;
        $angleStep = $totalArc / ($mbLen - 1);

        for ($i = 0; $i < $mbLen; $i++) {
            $angle = $startAngle + $i * $angleStep;
            $rad = deg2rad($angle);
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $x = $cx + $textRadius * cos($rad);
            $y = $cy + $textRadius * sin($rad);
            $charAngle = $angle + 90;

            $charImg = imagecreatetruecolor($fontSize * 3, $fontSize * 3);
            imagesavealpha($charImg, true);
            $charTransparent = imagecolorallocatealpha($charImg, 0, 0, 0, 127);
            imagefill($charImg, 0, 0, $charTransparent);

            @imagettftext($charImg, $fontSize, 0, intval($fontSize * 1.0), intval($fontSize * 2.0), $white, $fontPath, $char);
            $rotated = imagerotate($charImg, -$charAngle, $charTransparent);
            if ($rotated === false) {
                imagedestroy($charImg);
                continue;
            }
            imagealphablending($rotated, true);
            imagesavealpha($rotated, true);

            $w = imagesx($rotated);
            $h = imagesy($rotated);
            imagecopy($img, $rotated, intval($x - $w / 2), intval($y - $h / 2), 0, 0, $w, $h);
            imagedestroy($charImg);
            imagedestroy($rotated);
        }

        $centerText = $this->getCenterText($text, $mbLen);
        if ($centerText !== '') {
            $this->drawCenterText($img, $centerText, $fontPath, $size);
        }
    }

    private function getArcAngle($mbLen)
    {
        if ($mbLen <= 6) return 240;
        if ($mbLen <= 10) return 300;
        return 340;
    }

    private function getRadialFontSize($size, $mbLen)
    {
        if ($mbLen > 15) return max(10, intval($size * 0.09));
        if ($mbLen > 10) return max(10, intval($size * 0.095));
        if ($mbLen > 6) return max(10, intval($size * 0.10));
        return max(10, intval($size * 0.11));
    }

    private function getCenterText($text, $mbLen)
    {
        if ($mbLen >= 10) return mb_substr($text, 0, 2, 'UTF-8');
        if ($mbLen >= 6) return mb_substr($text, 0, 1, 'UTF-8');
        return '';
    }

    private function drawCenterText($img, $text, $fontPath, $size)
    {
        $white = imagecolorallocate($img, 255, 255, 255);
        $cx = imagesx($img) / 2;
        $cy = imagesy($img) / 2;
        $centerFontSize = intval($size * 0.18);
        $box = @imagettfbbox($centerFontSize, 0, $fontPath, $text);
        if (!is_array($box)) return;

        $textWidth = intval($box[2]) - intval($box[0]);
        $textHeight = intval($box[1]) - intval($box[7]);
        $drawX = $cx - $textWidth / 2;
        $drawY = $cy + $textHeight / 2;
        @imagettftext($img, $centerFontSize, 0, intval($drawX), intval($drawY), $white, $fontPath, $text);
    }

    private function isFontSupportChinese($fontPath)
    {
        if (!function_exists('imagettfbbox') || !file_exists($fontPath)) {
            return false;
        }
        foreach (['中', '國', '阿', '斯'] as $ch) {
            $bbox = @imagettfbbox(16, 0, $fontPath, $ch);
            if ($bbox === false) {
                return false;
            }
            if (intval($bbox[2]) - intval($bbox[0]) <= 0 || intval($bbox[1]) - intval($bbox[7]) <= 0) {
                return false;
            }
        }
        return true;
    }

    private function getFontPath()
    {
        static $foundFont = null;
        if ($foundFont !== null) {
            return $foundFont;
        }

        $fontsDir = dirname(__FILE__) . '/../../../public/static/fonts';
        if (!is_dir($fontsDir)) {
            @mkdir($fontsDir, 0755, true);
        }

        $candidates = [
            $fontsDir . '/msyh.ttc',
            $fontsDir . '/msyhbd.ttc',
            $fontsDir . '/msyhl.ttc',
            $fontsDir . '/simsun.ttc',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && $this->isFontSupportChinese($path)) {
                $foundFont = $path;
                return $foundFont;
            }
        }

        if (is_dir($fontsDir)) {
            $globPatterns = [$fontsDir . '/*.ttf', $fontsDir . '/*.ttc'];
            foreach ($globPatterns as $pattern) {
                $matches = glob($pattern);
                if (!empty($matches)) {
                    foreach ($matches as $m) {
                        if ($this->isFontSupportChinese($m)) {
                            $foundFont = $m;
                            return $foundFont;
                        }
                    }
                }
            }
        }

        $systemDirs = ['/usr/share/fonts', '/usr/local/share/fonts'];
        $skipDirKeywords = ['dejavu', 'liberation', 'gnu-free', 'abattis', 'crosextra', 'caladea', 'carlito', 'stix', 'tlwg'];
        foreach ($systemDirs as $dir) {
            if (!is_dir($dir)) continue;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                if (!$item->isFile()) continue;
                $ext = strtolower($item->getExtension());
                if ($ext !== 'ttf' && $ext !== 'ttc' && $ext !== 'otf') continue;
                $fullPath = $item->getPathname();
                $lower = strtolower($fullPath);
                $skip = false;
                foreach ($skipDirKeywords as $kw) {
                    if (strpos($lower, $kw) !== false) { $skip = true; break; }
                }
                if ($skip) continue;
                if ($this->isFontSupportChinese($fullPath)) {
                    $foundFont = $fullPath;
                    return $foundFont;
                }
            }
        }

        throw new \RuntimeException('未找到中文字体，请将 msyh.ttc / simsun.ttc 上传到：' . $fontsDir);
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

        $response = $this->fetchQrcodeFromWechat($id, $page, $width);
        if (!$response) {
            return $this->json_result('', 500, '获取access_token失败，请检查小程序AppId/AppSecret配置或网络连接');
        }

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
            $avatarImg = @$this->generateCircleAvatar($name, $avatarSize);
            if ($avatarImg) {
                $response = $this->mergeAvatarToQrcode($response, $avatarImg);
            }
        }

        $base64 = 'data:image/jpeg;base64,' . base64_encode($response);

        return $this->json_result(['qrcode' => $base64], 200, '生成成功');
    }

    private function fetchQrcodeFromWechat($id, $page, $width)
    {
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $forceRefresh = ($attempt > 0);
            $token = $this->getAccessToken($forceRefresh);
            if (!$token) {
                continue;
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
                if (in_array(intval($result['errcode']), self::INVALID_TOKEN_CODES) && $attempt === 0) {
                    continue;
                }
                return $response;
            }

            return $response;
        }

        return null;
    }

    
}