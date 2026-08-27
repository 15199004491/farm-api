<?php


namespace app\admin\model;

use think\Model;

/**
 * 后台配置模型
 * @package app\admin\model
 */
class Config extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'admin_config';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

    /**
     * 获取配置信息
     * @param  string $name 配置名
     * @return mixed
     */
    public function getConfig($name = '')
    {
        $configs = self::column('value,type', 'name');

        $result = [];
        foreach ($configs as $config) {
            switch ($config['type']) {
                case 'array':
                    $result[$config['name']] = parse_attr($config['value']);
                    break;
                case 'checkbox':
                    if ($config['value'] != '') {
                        $result[$config['name']] = explode(',', $config['value']);
                    } else {
                        $result[$config['name']] = [];
                    }
                    break;
                default:
                    $result[$config['name']] = $config['value'];
                    break;
            }
        }

        // 系统必需配置默认值兜底，防止数据库缺失记录时报错
        $defaultConfig = [
            'develop_mode'   => 0,
            'app_trace'      => 0,
            'list_rows'      => 20,
            'system_name'    => 'DolphinPHP',
            'system_title'   => 'DolphinPHP管理系统',
            'web_site_title' => 'DolphinPHP',
            'index_template' => 'default',
            'upload_image_ext' => 'gif,jpg,jpeg,bmp,png',
            'upload_file_ext'  => 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,zip,rar,gz,bz2',
            'upload_image_size' => 2097152,
            'upload_file_size'  => 2097152,
            'data_backup_path'   => '../data/',
            'data_backup_part_size' => 20971520,
            'data_backup_compress' => 1,
            'data_backup_compress_level' => 9,
        ];
        foreach ($defaultConfig as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }

        return $name != '' ? (isset($result[$name]) ? $result[$name] : null) : $result;
    }
}