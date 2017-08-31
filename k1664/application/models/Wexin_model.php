<?php

use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\File;
class Wexin_model extends CI_Model{

	//初始化数据
	public function __construct(){
		parent::__construct();
		// 参数依次为 AppId, AppKey, MasterKey
		Client::initialize("MEPjAFvYiOuqm4ji2duqPsMJ-gzGzoHsz", "giIdvt6HNbJ7tsGR3C0uHtGU", "fUQuzyIAkADsqmyLVd7gkLcn");
		// 启用中国节点（默认启用）
		Client::useRegion("CN");
		$this->table='user_weixin';
	}

	private function getWeixinInfo($openid){//确保表存在
			$data=array();		
			if(!$openid){
				return $data;
			}

			$query = new Query("user_weixin");
			$query->equalTo("openid", $openid);
			$todos = $query->find();
			forEach($todos as $k=>$v) {
				$data=array(
					"objectId"=>$v->getObjectId(),
					"openid"=>$v->get("openid"),
					"nickname"=>$v->get("nickname"),
					"headid"=>$v->get("headid"),
				);
			}
			return $data;

	}

	//保存图片
	private function saveImgToServer($newFileName,$url){
		$file = File::createWithUrl($newFileName, $url);
		$file->save();
		$photoid=$file->getObjectId();
		return $photoid;
	}

	//注册
	private function reg($userinfo){
	$data=array();
	$todo = new Object($this->table);
    $todo->set("openid", $userinfo["openid"]);
	$todo->set("nickname",  $userinfo["nickname"]);
	$headid=$this->saveImgToServer($userinfo["openid"].'png',$userinfo["headimgurl"]);
	$todo->set("headid",$headid);
    try {
        $todo->save();
		//存储成功
     } catch (CloudException $ex) {
			//失败的话，请检查网络环境以及 SDK 配置是否正确
			
	}

	$data=array(
		'objectId'=>$todo->getObjectId(),
		'openid'=>$userinfo["openid"],
		'nickname'=>$userinfo["nickname"],
		'headid'=>$headid,
	);
	return $data;
				
				

				
			
}





		//微信授权登陆
public function weixinLogin($urls){
		     $APPID='wxc638ad20926dc892';//公众号在微信的appid
             $APPSECRET= "17f8f3c6062ddf6f7bd31891eed20cbb";
			 $REDIRECT_URI=$urls;
			 //print_r($REDIRECT_URI);die;
			 
			 $objectId = gCookie('objectId');

			 if($objectId){//有记录
				 $data=array(
					'objectId'=>$objectId,
					'openid'=>gCookie('openid'),
					'headid'=>gCookie('headid'),
					'nickname'=>gCookie('nickname'),
				);
				 return $data;
			 }

		if($this->is_weixin()){
				
							if (!isset($_GET['code'])){
									//print_r($REDIRECT_URI);die;
									 //重定向到微信授权登陆
									
									//$SCOPE='snsapi_base';//获取已关注公众号的用户信息，获取不到未关注的用户信息，只能获取到openid和unionid
									$SCOPE='snsapi_userinfo';//可以获取未关注用户的信息，但是会弹出授权---需用户授权
								    //$url='https://open.weixin.qq.com/connect/oauth2/authorize?response_type=code&scope='.$SCOPE.'&state=123&appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'#wechat_redirect';
							        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$SCOPE.'&state=123#wechat_redirect';
								    header("Location:".$url);
									die;
							}
				
							if($_GET['code']){
								    $code=$_GET['code'];
							         //根据code获得access_token
							        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$APPID&secret=$APPSECRET&code=$code&grant_type=authorization_code";
							        $ch = curl_init();
									curl_setopt($ch, CURLOPT_URL, $url);
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
									curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
									$output = curl_exec($ch);
									curl_close($ch);
									$jsoninfo = json_decode($output, true);
									$access_token = $jsoninfo["access_token"];//授权Access token
									$openid = $jsoninfo["openid"];
							}
					
					
			        if($access_token){
			        	     //一种是使用AppID和AppSecret获取的access_token，一种是OAuth2.0授权中产生的access_token，分别称为全局Access Token和授权Access 
					         //根据access_token得到用户信息,,需snsapi_userinfo
			                 $url_u="https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
						     // $url_u="https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
					
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url_u);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$output = curl_exec($ch);
					curl_close($ch);
					$userinfo = json_decode($output, true);
					}else{
						echo '未获取到token';die;
					}
					//subscribe用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
					
					  //获取微信账户信息等
					 $openid=$userinfo['openid'];
			
                	//获取到openid之后，如果有用户，则自动登录
	           		//查询出用户的openid，如果以前登录过则，直接登录成功，如果没有，则插入用户信息
		        	$infos=$this->getWeixinInfo($openid);
				
				    if($infos){
			         	$data['user_have']=1;
					}else{
						 $data['user_have']=0;
						//注册
						$infos=$this->reg($userinfo);
					
					}
					  //注册成功，自动登录
					sCookie('objectId', $infos['objectId'], 36400*365);
					sCookie('nickname', $infos['nickname'], 36400*365);
					sCookie('openid', $infos['openid'], 36400*365);
					sCookie('headid', $infos['headid'], 36400*365);
					
                     header("Location:".$REDIRECT_URI);
					die;
					
					
		}else{//不是微信登陆
			   echo "请使用微信客户端打开该网址";
			   die();
		}
		
		return $data;
					
	

					
	
}





//验证微信
private function is_weixin(){ 

    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
       
        return true;

    }  

        return false;

}





}

?>
