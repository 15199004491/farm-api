<?php

namespace app\user\admin;

use app\common\controller\Common;
use think\facade\Env;

/**
 * 
 * @package
 */
class Regular extends Common
{
    // 删除图片
    public function deleteImage()
    {
        $data = $this->request->param();
        if($data['filePath']) {
            $dir = Env::get('root_path').'public';
            $file = $dir . '/' . $data['filePath'];
            $result = unlink($file);
            return $this->json_result($result, 200, '删除成功');
        }
    }
}
