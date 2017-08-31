<?php

defined('BASEPATH') OR exit('No direct script access allowed');
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
use \LeanCloud\File;
class UploadApi extends CI_Controller {
	public function __construct() { 
		parent::__construct();
		$this->load->model('LeanDb');
		// 参数依次为 AppId, AppKey, MasterKey
		Client::initialize("MEPjAFvYiOuqm4ji2duqPsMJ-gzGzoHsz", "giIdvt6HNbJ7tsGR3C0uHtGU", "fUQuzyIAkADsqmyLVd7gkLcn");
		// 启用中国节点（默认启用）
		Client::useRegion("CN");
		$this->datas=array();

	}

	public function index(){
		$count=$this->LeanDb->getCount("Todo");
		echo $count;

		$info=$this->LeanDb->getInfo("Todo","title like '工程师周会%'");
		print_r($info);
	}


	public function formList(){
		 $photoid=$this->input->get_post('photoid');
		 $imgid=$this->input->get_post('imgid');
		 $description=$this->input->get_post('description');
		 $mobile=$this->input->get_post('mobile');
		 $nickname=$this->input->get_post('nickname');
		 $filterclass=$this->input->get_post('filter')?$this->input->get_post('filter'):'';

		 //上传图片
		 $savePath = getcwd().'/upload/';	  /*设置上传路径*/
		if($_FILES ['photo'] ['tmp_name']){
	
			$tmp_file = $_FILES ['photo'] ['tmp_name'];
			$file_types = explode ( ".", $_FILES ['photo'] ['name'] );
    		$file_type = $file_types [count ( $file_types ) - 1];
	
   			 /*以时间来命名上传的文件*/
    		 $str = date("Ymdhis",time());
    		 $file_name = $str . "." . $file_type;
	
			 /*是否上传成功*/
			 if(!copy($tmp_file,$savePath.$file_name)){
				 $this->error( '上传失败');
			 }

	 		$file = File::createWithLocalFile($savePath.$file_name);
			//$file->setMeta("width", 100);
			//$file->setMeta("height", 100);
			$file->setMeta("author", "k1664dev");
			$file->save();
	  		$imgid=$file->getObjectId();

	}


    	$todo = new Object("gallery");//表
		$todo->set("photoid",$photoid);//头像
		$todo->set("imgid", $imgid);//字段
		$todo->set("description", $description); 
		$todo->set("mobile", $mobile); 
		$todo->set("nickname", $nickname); 
		$todo->set("filterclass", $filterclass); //滤镜
		try {
			$todo->save();
			
			//分享信息
			$objectid=$todo->getObjectId();
			$photourl=$this->getImgUrl($photoid);
			$this->datas["share"]=$this->shareInfo($nickname,$photourl,$objectid);

			$this->msg['status'] = 200;
     		$this->msg['info'] = '保存成功！';
     		$this->msg['data'] = $this->datas;
			$this->return_json($this->msg);

    		// 存储成功
		} catch (CloudException $ex) {
			// 失败的话，请检查网络环境以及 SDK 配置是否正确
			$this->msg['status'] = 501;
     		$this->msg['info'] = '保存失败！';
     		$this->msg['data'] = $this->datas;
	 		$this->return_json($this->msg);
		}

	 	


	}


		private function shareInfo($nickname,$photourl,$objectid){
			$info=array(
					'url'=>urlencode(WEBROOT.'welcome/share?id='.$objectid),//分享链接
					'title'=>urlencode("尽享法式风情"),//分享链接
					'desc'=>urlencode("将一点法式风情融入你的生活瞬间，和凯旋1664一起践行法式生活艺术。"),//分享
					'shareimg'=>urlencode('http://k1664dev.octoapps.com/public/static/img/welcome.jpg'),//分享
				);

				return $info;
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


	private function return_json($msg){
		echo json_encode($msg);
		exit;
	}

	public function img(){
		 /*设置上传路径*/
		$savePath = getcwd().'/upload/';


	if($_FILES ['photo'] ['tmp_name']){
	
	$tmp_file = $_FILES ['photo'] ['tmp_name'];
	$file_types = explode ( ".", $_FILES ['photo'] ['name'] );
    $file_type = $file_types [count ( $file_types ) - 1];
	
    /*以时间来命名上传的文件*/
     $str = date("Ymdhis",time());
     $file_name = $str . "." . $file_type;
	
	 /*是否上传成功*/
	 if(!copy($tmp_file,$savePath.$file_name)){
		 $this->error( '上传失败');
	 }

	 $file = File::createWithLocalFile($savePath.$file_name);
		//$file->setMeta("width", 100);
		//$file->setMeta("height", 100);
	$file->setMeta("author", "k1664dev");
	 $file->save();
	 $this->datas["objid"]=$file->getObjectId();
	 $this->msg['status'] = 200;
     $this->msg['info'] = '图片上传成功！';
     $this->msg['data'] = $this->datas;
	 $this->return_json($this->msg);

	}
	
	 $this->msg['status'] = 501;
     $this->msg['info'] = '图片上传失败！';
     $this->msg['data'] = $this->datas;
	 $this->return_json($this->msg);	 

	}


}

