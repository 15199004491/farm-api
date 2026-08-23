<?php
namespace app\user\admin;
use think\Controller;
use app\user\model\Wxuser  as WxuserModel;;

class Wxuser extends Controller {

    protected function initialize()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin');
        header('Access-Control-Max-Age: 3600');

        if ($this->request->isOptions()) {
            return response('', 200);
        }
    }

	/**
     * 用户自动登录
     */
    public function login() {
        $model = new WxuserModel;
        $user_id = $model->login($this->request->post());
        $token = $model->getToken();  
        return $this->json_result(['code'=>200,'user_id' => $user_id,'token'=>$token], 200, '登陆成功');
    }

	/**
     * 获取用户信息
     */
     public function loginInfo() {
		 if (!$token = $this->request->param("token")) {
			throwError('缺少必要的参数',409);
		 }
		 if (!$user = WxuserModel::getUser($token)) {
            throwError('没有找到用户信息',409);
         }
         return $this->json_result(['code'=>200,'data'=>$user], 200, '操作成功');
     }
    /*获取access_token,不能用于获取用户信息的token*/
    public  function getAccessToken()
    {
        $appid = 'wx5375bc6d5a7a6227';
        $secret = '1a2s3d4f5g1a2s3d4f5g1a2s3d4f5g1a';

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret."";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    //发送数据
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
    public function getuserphonenumber(){
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
}
?>