<?php

class Sms {

    public function __construct() {
//         $this->username = "";
//         $this->password = "";
//         $this->url = "http://www.jianzhou.sh.cn/JianzhouSMSWSServer/http/sendBatchMessage";

     	/*$this->username = "summer_fenghao@163.com";
    	$this->password = "pink89131";
		$this->url = "http://sms-api.luosimao.com/v1/send.json"; */

		$this->url = "http://sms-api.luosimao.com/v1/send.json";
		
    }

    /**
     * 发送短信
     * @param type $strmobile 手机号码
     * @param type $content 短信内容
     * @return boolean
     */
    public function sendSms_test($strmobile, $content) {
        $post_data = "account={$this->username}&password={$this->password}&destmobile={$strmobile}&msgText=" . rawurlencode($content);
        //密码可以使用明文密码或使用32位MD5加密
        $gets = $this->Post_test($post_data, $this->url);
        return $gets;
    }

    public function Post_test($curlPost, $url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }
    //真实发送短信
	public function sendSms($strmobile, $content) {
		$content = $content."【信宝袋】";
		$post_data=array('mobile' => $strmobile,'message' => $content);
    	$gets = $this->semao_Post($post_data, $this->url);
    	return $gets;
    }
	public function semao_Post($post_data,$url){
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL,$url);
    	
    	curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_1 );
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	curl_setopt($ch, CURLOPT_HEADER, FALSE);
    	
    	curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
		//curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-9a59f8ecb5201cc2728bb31719bc35e4');//姿猫
		curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-bceabee9f7e8b85272a6be54652bbf82');//体育委员
        
    	
    	curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $mobile,'message' => $message) );
    	
    	$res = curl_exec( $ch );
    	curl_close( $ch );
    	//$res  = curl_error( $ch );
    	//var_dump($res);
    	return $res;
    }
	
	/**
* 智能匹配模版接口发短信
* apikey 为云片分配的apikey
* text 为短信内容
* mobile 为接受短信的手机号
*/
public function send_sms_yunpian($mobile,$text){
	   /*云片短信*/
    $apikey="3043dbde3f927adcb2eb542f12f912e5";
    $url="http://yunpian.com/v1/sms/send.json";
    $encoded_text = urlencode("$text");
    $mobile = urlencode("$mobile");
    $post_string="apikey=$apikey&text=$encoded_text&mobile=$mobile";
	return $this->sock_post_yunpian($url, $post_string);
}
	
	
/**
 * 为云片分配的apikey
 *
* url 为服务的url地址
* query 为请求串
*/
 public function sock_post_yunpian($url,$query){
    $data = "";
    $info=parse_url($url);
    $fp=fsockopen($info["host"],80,$errno,$errstr,30);
    if(!$fp){
        return $data;
    }
    $head="POST ".$info['path']." HTTP/1.0\r\n";
    $head.="Host: ".$info['host']."\r\n";
    $head.="Referer: http://".$info['host'].$info['path']."\r\n";
    $head.="Content-type: application/x-www-form-urlencoded\r\n";
    $head.="Content-Length: ".strlen(trim($query))."\r\n";
    $head.="\r\n";
    $head.=trim($query);
    $write=fputs($fp,$head);
	$header = "";
    while ($str = trim(fgets($fp,4096))) {
        $header.=$str;
    }
    while (!feof($fp)) {
        $data .= fgets($fp,4096);
    }
	fclose($fp);
	//file_put_contents(time().random(4).'.txt',$head);
    return $data;
}


}
