<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\File;

class Welcome extends CI_Controller {

		public function __construct() { 
		parent::__construct();
		// 参数依次为 AppId, AppKey, MasterKey
		Client::initialize("MEPjAFvYiOuqm4ji2duqPsMJ-gzGzoHsz", "giIdvt6HNbJ7tsGR3C0uHtGU", "fUQuzyIAkADsqmyLVd7gkLcn");
		// 启用中国节点（默认启用）
		Client::useRegion("CN");
		$this->datas=array();
		$this->load->model('wexin_model');
		$this->data=array();
		}


		private function getImgUrl($objectid){
			if(!$objectid){
				return "";
			}

			try {
			$query = new Query("_File");
			$todo  = $query->get($objectid);
			$url=$todo->get("url");
			} catch (Exception $e) {
				//print $e->getMessage();
				return "";
			}

			return $url;
		}

		public function logout(){
					sCookie('objectId', "", -1);
					sCookie('nickname', "", -1);
					sCookie('openid', "", -1);
					sCookie('headid',"",-1);
		}

		public function index(){

			//微信授权登录
			//$userInfo=array();
			$userInfo=$this->wexin_model->weixinLogin('http://k1664dev.octoapps.com/');
			//print_r($userInfo);die;
			$list = Query::doCloudQuery("SELECT * FROM gallery  order by createdAt desc");
			$data=array();
			forEach($list["results"] as $k=>$v) {
				$imgid=$v->get("imgid");
				$photoid=$v->get("photoid");
				$data[]=array(
					"description"=>$v->get("description"),
					"mobile"=>$v->get("mobile"),
					"nickname"=>$v->get("nickname")?$v->get("nickname"):'微信用户',
					"imgurl"=>$this->getImgUrl($imgid),
					"photourl"=>$this->getImgUrl($photoid),
					"filterclass"=>$v->get("filterclass"),
				);
			}
		
			$this->data['position'] = 'index';
			$this->data['list']=$data;
			$this->data['headimgurl']=$this->getImgUrl($userInfo["headid"]);
			$this->data['headid']=$userInfo["headid"];
			$this->data['nickname']=$userInfo["nickname"];
			$this->data['share']=$this->shareInfo();
		    $this->load->view('index/index.html',$this->data);
		}

		private function shareInfo(){
			$info=array(
					'url'=>urlencode(WEBROOT),//分享链接
					'sharetitle'=>urlencode("尽享法式风情"),//分享链接
					'sharecontent'=>urlencode("将一点法式风情融入你的生活瞬间，和凯旋1664一起践行法式生活艺术。"),//分享
					'shareimg'=>urlencode('http://k1664dev.octoapps.com/public/static/img/welcome.jpg'),//分享
				);

				return $info;
		}

		public function share(){
			$objectid=$this->input->get_post("id");//59a668108d6d81005701dd19

			try {
			$query = new Query("gallery");
    		$todo=$query->get($objectid);
			} catch (Exception $e) {
				//print $e->getMessage();
				echo "参数错误";
				exit();
			}

	
			$imgid=$todo->get("imgid");
			$photoid=$todo->get("photoid");
			$data=array(
					"description"=>$todo->get("description"),
					"mobile"=>$todo->get("mobile"),
					"nickname"=>$todo->get("nickname")?$todo->get("nickname"):'微信用户',
					"imgurl"=>$this->getImgUrl($imgid).'?imageView2/1/w/351/h/351',
					"photourl"=>$this->getImgUrl($photoid),
				);
			$this->data['info']=$data;
		
		    $this->load->view('index/share.html',$this->data);

		}


}
