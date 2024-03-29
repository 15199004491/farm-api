<?php
namespace app\farm\admin;
use think\Controller;
use app\farm\model\Wxuser as WxuserModel;

class Wxuser extends Controller {
    /*获取access_token,不能用于获取用户信息的token*/
    public  function getAccessToken()
    {
        $appid = 'wx5375bc6d5a7a6227';
        $secret = '7942cffdecd4862b5746a5bafd17a93b';

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";

        $data = file_get_contents($url);
        return $data;
    }
    //图片合法性验证
    public function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);

        return $output;
    }
    //  获取手机号
    public function getuserphonenumber() {
        $tmp = $this->getAccessToken();
        $tmptoken = json_decode($tmp);
        $token = $tmptoken->access_token;
        $data['code'] = $this->request->post()['code'];

        $url = "https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=$token";
        $info = $this->http_request($url,json_encode($data),'json');
        // 一定要注意转json，否则汇报47001错误
        $tmpinfo = json_decode($info);

        $code = $tmpinfo->errcode;
        $phone_info = $tmpinfo->phone_info;
        //手机号
        $phoneNumber = $phone_info->phoneNumber;
        if($code == '0'){
            return $this->json_result(['code'=>200,'phoneNumber'=>$phoneNumber], 200, '操作成功');
        }else{
            return $this->json_result([], 409, '请求失败');
        }
    }
    // 用户登录
    public function login() {
        $data = $this->request->param();
        $result = WxuserModel::where('mobile', $data['mobile'])->find();
        // $result? WxuserModel::where('mobile', $data['mobile'])->update($data) : WxuserModel::insertGetId($data);
        if($result) {
            $login_time = time();
            WxuserModel::where('mobile', $data['mobile'])->update($data);
        } else {
            WxuserModel::insertGetId($data);
        }
    }
}
?>
