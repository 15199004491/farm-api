<?php


namespace app\common\controller;
use think\facade\Cache;
use think\Controller;

/**
 * 项目公共控制器
 * @package app\common\controller
 */
class Common extends Controller
{
    protected $appId;
    protected $appSecret;

    /**
     * 初始
     * @author
     */
    protected function initialize()
    {
        parent::initialize();
        $this->appId     = config('wechat.wx_appid') ?: 'wx5375bc6d5a7a6227';
        $this->appSecret = config('wechat.wx_appsecret') ?: 'f946359b33b372d190c2d9be6e2cb213';
    }

    /**
     * 通用 HTTP 请求（支持 GET/POST/JSON）
     */
    protected function http_request($url, $data = null, $useJson = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($useJson && is_array($data)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        }

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return json_encode(['errcode' => -1, 'errmsg' => 'HTTP请求失败', 'http_code' => $httpCode]);
        }

        return $output;
    }

    /**
     * 获取微信小程序 access_token（带缓存）
     */
    protected function getAccessToken()
    {
        $cacheKey = 'wx_access_token';
        $token = Cache::get($cacheKey);
        if ($token) {
            return $token;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $res = json_decode($this->http_request($url), true);

        if (isset($res['access_token'])) {
            Cache::set($cacheKey, $res['access_token'], 7000);
            return $res['access_token'];
        }

        return '';
    }

    /**
     * 获取筛选条件
     * @author
     * @alter 小乌 <82950492@qq.com>
     * @return array
     */
    final protected function getMap()
    {
        $search_field     = input('param.search_field/s', '', 'trim');
        $keyword          = input('param.keyword/s', '', 'trim');
        $filter           = input('param._filter/s', '', 'trim');
        $filter_content   = input('param._filter_content/s', '', 'trim');
        $filter_time      = input('param._filter_time/s', '', 'trim');
        $filter_time_from = input('param._filter_time_from/s', '', 'trim');
        $filter_time_to   = input('param._filter_time_to/s', '', 'trim');
        $select_field     = input('param._select_field/s', '', 'trim');
        $select_value     = input('param._select_value/s', '', 'trim');
        $search_area      = input('param._s', '', 'trim');
        $search_area_op   = input('param._o', '', 'trim');

        $map = [];

        // 搜索框搜索
        if ($search_field != '' && $keyword !== '') {
            $map[] = [$search_field, 'like', "%$keyword%"];
        }

        // 下拉筛选
        if ($select_field != '') {
            $select_field = array_filter(explode('|', $select_field), 'strlen');
            $select_value = array_filter(explode('|', $select_value), 'strlen');
            foreach ($select_field as $key => $item) {
                if ($select_value[$key] != '_all') {
                    $map[] = [$item, '=', $select_value[$key]];
                }
            }
        }

        // 时间段搜索
        if ($filter_time != '' && $filter_time_from != '' && $filter_time_to != '') {
            $map[] = [$filter_time, 'between time', [$filter_time_from.' 00:00:00', $filter_time_to.' 23:59:59']];
        }

        // 表头筛选
        if ($filter != '') {
            $filter         = array_filter(explode('|', $filter), 'strlen');
            $filter_content = array_filter(explode('|', $filter_content), 'strlen');
            foreach ($filter as $key => $item) {
                if (isset($filter_content[$key])) {
                    $map[] = [$item, 'in', $filter_content[$key]];
                }
            }
        }

        // 搜索区域
        if ($search_area != '') {
            $search_area = explode('|', $search_area);
            $search_area_op = explode('|', $search_area_op);
            foreach ($search_area as $key => $item) {
                list($field, $value) = explode('=', $item);
                $value = trim($value);
                $op    = explode('=', $search_area_op[$key]);
                if ($value != '') {
                    switch ($op[1]) {
                        case 'like':
                            $map[] = [$field, 'like', "%$value%"];
                            break;
                        case 'between time':
                        case 'not between time':
                            $value = explode(' - ', $value);
                            if ($value[0] == $value[1]) {
                                $value[0] = date('Y-m-d', strtotime($value[0])). ' 00:00:00';
                                $value[1] = date('Y-m-d', strtotime($value[1])). ' 23:59:59';
                            }
                        default:
                            $map[] = [$field, $op[1], $value];
                    }
                }
            }
        }
        return $map;
    }

    /**
     * 获取字段排序
     * @param string $extra_order 额外的排序字段
     * @param bool $before 额外排序字段是否前置
     * @author
     * @return string
     */
    final protected function getOrder($extra_order = '', $before = false)
    {
        $order = input('param._order/s', '');
        $by    = input('param._by/s', '');
        if ($order == '' || $by == '') {
            return $extra_order;
        }
        if ($extra_order == '') {
            return $order. ' '. $by;
        }
        if ($before) {
            return $extra_order. ',' .$order. ' '. $by;
        } else {
            return $order. ' '. $by . ',' . $extra_order;
        }
    }

    /**
     * 渲染插件模板
     * @param string $template 模板名称
     * @param string $suffix 模板后缀
     * @author
     * @return mixed
     */
    /**
     * 渲染插件模板
     * @param string $template 模板文件名
     * @param string $suffix 模板后缀
     * @param array $vars 模板输出变量
     * @param array $config 模板参数
     * @author
     * @return mixed
     */
    final protected function pluginView($template = '', $suffix = '', $vars = [], $config = [])
    {
        $plugin_name = input('param.plugin_name');

        if ($plugin_name != '') {
            $plugin = $plugin_name;
            $action = 'index';
        } else {
            $plugin = input('param._plugin');
            $action = input('param._action');
        }
        $suffix = $suffix == '' ? 'html' : $suffix;
        $template = $template == '' ? $action : $template;
        $template_path = config('plugin_path'). "{$plugin}/view/{$template}.{$suffix}";
        return parent::fetch($template_path, $vars, $config);
    }
}