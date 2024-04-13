<?php
namespace app\farm\admin;
use think\Controller;
use app\farm\model\Wxuser as WxuserModel;
use app\farm\model\Suggest as SuggestModel;
use app\farm\model\Person as PersonModel;

class Wxuser extends Controller {
    /*获取access_token,不能用于获取用户信息的token*/
    public  function getAccessToken()
    {
        $appid = 'wx5375bc6d5a7a6227';
        $secret = 'f946359b33b372d190c2d9be6e2cb213';

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
    
    public function login() {
        $data = $this->request->post();
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

            $imageSavePath ='factory/'.uniqid().'.'.$exename;
            
            if(move_uploaded_file($_FILES['image']['tmp_name'], $imageSavePath)){
                return $this->json_result($imageSavePath, 200, '上传成功');
            }
        }
    }
    // 删除图片
    public function removeImage()
    {
        $data = $this->request->param();
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
        $data = $this->request->param();
        $data['update_time'] = time();
        $param = SuggestModel::insertGetId($data);
       
        return $this->json_return($param);
    }
    // 获取当前用户的信息
    public function getUserInfo() {
        $data = $this->request->param();
        $result = PersonModel::where('login_mobile', $data['token'])->find();
        return $this->json_result($result, 200, '操作成功');
    }
    
}
?>
