<?php
namespace app\farm\admin;
use think\Controller;
use app\farm\model\Wxuser as WxuserModel;
use app\farm\model\Suggest as SuggestModel;
use app\farm\model\Person as PersonModel;
use think\facade\Env;

class Wxuser extends Controller {
    public function __construct() {
		$this->appId = 'wx5375bc6d5a7a6227';
		$this->appSecret = 'f946359b33b372d190c2d9be6e2cb213';
	}
    public function to_url_params($params){
        $buff = "";
        foreach ($params as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    /*微信文字敏感内容检测*/
    public function msgSecCheck()
    {
        $data = request()->post();
        $data = json_encode(array('content' => $data['msg']), JSON_UNESCAPED_UNICODE);
        $token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/msg_sec_check?access_token=$token";
        $info = $this->http_request($url, $data);
        return $this->json_return(json_decode($info),true);
    }

    /*获取access_token,不能用于获取用户信息的token*/
    public function getAccessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->http_request($url));
        $access_token = $res->access_token;
        return $access_token;
    }
    public function getToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->http_request($url));
        $access_token = $res->access_token;
        return $this->json_return($access_token);
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
    public function getuserphonenumber() {
        $token =  $this->getAccessToken();
        $data = request()->param();
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
    
    public function login() {
        $data = request()->param();
        $model = new WxuserModel;
        $session = $model->login($data);
        $data['open_id'] = $session['openid'];
		$data['nick_name'] = preg_replace('/[\xf0-\xf7].{3}/', '', $data['nick_name']);
		$data['gender'] = $data['gender'] + 1;
        $data['update_time']=time();
        $info = PersonModel::where('login_mobile',$data['login_mobile'])->find();
        if($info) {
            $result = PersonModel::where('login_mobile',$data['login_mobile'])->update($data);
        } else {
            $data['create_time']=time();
            $result = PersonModel::insertGetId($data);
        }
		
        return $this->json_return($result);
    }
    // 上传图片
    public function updateImage()
    {
        if(!empty($_FILES['image'])){
            //获取扩展名
            $exename = $this->getExeName($_FILES['image']['name']);

            $imageSavePath ='farm/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
    // 删除图片
    public function removeImage()
    {
        $data = request()->param();
        $file = $data['path'];
        if (file_exists($file)) {
            unlink($file);
            return $this->json_result([], 200, '图片删除成功');
        } else {
            return $this->json_result([], 200, '图片删除成功');
        }
    }
    // 添加投诉/建议
    public function addSuggest() {
        $data = request()->param();
        $data['update_time'] = time();
        $param = SuggestModel::insertGetId($data);
       
        return $this->json_return($param);
    }
    // 获取当前用户的信息
    public function getUserInfo() {
        $data = request()->param();
        $result = PersonModel::where('login_mobile', $data['token'])->find();
        return $this->json_result($result, 200, '操作成功');
    }
    
}
?>
