<?php

class Publics extends CI_Model{

	//系统访问路径
	public $lk_url = '/admin/';
	//系统展示访问路径
	public $lk_furl = '/';
	//cookies名称
	public $lk_ckpre = 'Ohu1AH';

	//日期时间格式化
	public function gdate($time = '', $format = ''){
		global $lk_timezone, $lk_timeformat, $lk_dateformat;
		!$time && $time = time();
		if(!$format){
			//24 OR 12 time format
			$hour = $lk_timeformat ? 'H:i' : 'h:i A';
			$format = $lk_dateformat . ' ' . $hour;
		}
		return gmdate($format, ($time + $lk_timezone * 3600));
		//return gmdate($format, $time);
	}

	//后台验证是否登录
	public function validateLogin(){
		$loginFlag = $this->gCookie('admin_administrator');
		if(!$loginFlag){
			return false;
			exit();
		} else{
			return $loginFlag;
		}
	}

	//验证商家登录
	public function validComLogin(){
		$loginInfo = $this->gCookie('lk_administrator');
		if(!$loginInfo){
			return false;
			exit();
		} else{
			return $loginInfo;
		}
	}

	//用户中心验证是否登录
	public function validStaffLogin(){
		$lg_userInfo = $this->gCookie('lk_stafflogin');
		if(!$lg_userInfo){
			return false;
			exit();
		}
		return $lg_userInfo;
	}

	//用户中心验证是否登录
	public function validateUserLogin(){
		$lg_userInfo = $this->gCookie('lg_userInfo');
		if(!$lg_userInfo){
			//$this->redirect('对不起，您还没有登陆！', APP_URL . 'ucenter / login');
			return false;
			exit();
		}
		return $lg_userInfo;
	}

	//验证客服登录状态
	public function validateServiceLogin(){
		$lk_serverslogin = $this->gCookie('lk_serverslogin');
		if(!$lk_serverslogin){
			//$this->redirect('对不起，您还没有登陆！', APP_URL . 'ucenter / login');
			return false;
			exit();
		}
		return $lk_serverslogin;
	}

	/*
	* 页面跳转
	* $msg         文字说明
	* $url         转到链接
	* $redirect    自动跳转
	* $time        等待时间
	*/
	public function redirect($msg, $url, $redirect = 0, $time = 3000){
		$str = '';
		$str .= '<html><head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>提示信息</title>
		<style type="text/css">
		*{margin:0;padding:0px}
		body{background:#fff;color:#333;font:12px Verdana, Tahoma, sans-serif;text-align:center;margin:0 auto;}
		a{text-decoration:none;color:#29458C}
		a:hover{text-decoration:underline;color:#f90}
		#msg{border:1px solid #c5d7ef;text-align:left;margin:10% auto; width:50%}
		#msgtitle{padding:5px 10px;background:#f0f6fb;border-bottom:1px #c5d7ef solid}
		#msgtitle h1{font-size:14px;font-weight:bold;padding-left:10px;border-left:3px solid #acb4be;color:#1f3a87}
		#msgcontent {padding:20px 50px;}
		#msgcontent li{display:block;padding:5px;list-style:none;}
		#msgcontent p{text-align:center;margin-top:10px;padding:0}
		</style></head><body>
		<div id="msg"><div id="msgtitle"><h1>提示信息</h1></div><div id="msgcontent">' . $msg . '<p>';
		if($redirect && $url){
			$str .= '<a href="' . $url . '">如果您的浏览器没有自动跳转，请点击这里</a>
			<script type="text/javascript">
			setTimeout("window.location.href =\'' . $url . '\';",' . ($time) . ');
			</script>';
		} else{
			$str .= '<a href="' . ($url ? $url : "javascript:history.go(-1)") . '">返回继续操作</a>';
		}
		$str .= '</p></div></div></body></html>';
		echo $str;
		die;
	}

	//导入EXCEL
	public function importexcel($path){
		$data = array();
		if(is_file(FCPATH . $path)){
			set_include_path(get_include_path() . PATH_SEPARATOR . 'excel/');
			//set_include_path(' http://localhost / PHPExcel / phpexcel / ');
			/** PHPExcel */
			include 'PHPExcel.php';
			/** PHPExcel_IOFactory */
			include 'PHPExcel/IOFactory.php';
			/** include php_excel5 */
			include 'PHPExcel/Reader/Excel5.php';
			$reader = new PHPExcel_Reader_Excel5();
			$reader->setReadDataOnly(true); // Not read styles
			$excel = $reader->load(FCPATH . $path); //excel的路径
			$data = $excel->getActiveSheet()->toArray();
			return $data;
		}
	}

	/* 替换字符
	* $str 字符串
	*/

	public function ITxt($str){//可以改成数组替换
		$str = str_replace("../../editor_upload/", "/static/editor_upload/", $str);
		$str = str_replace("&quot;", "", $str);
		$str = str_replace("\&quot;", "", $str);
		$str = str_replace("\\", "", $str);
		$str = str_replace("<script>", "&lt;script&gt;", $str);
		$str = str_replace("</script>", "&lt;/script&gt;", $str);
		return $str;
	}

	/* 截取字符串
	* $sourcestr 字符串
	* $cutlength 长度
	*/

	public function cut_str($sourcestr, $cutlength){
		$returnstr = '';
		$i = 0;
		$n = 0;
		$str_length = strlen($sourcestr); //字符串的字节数
		$mb_str_length = mb_strlen($sourcestr, 'utf-8');
		while(($n < $cutlength) && ($i <= $str_length)){
			$temp_str = substr($sourcestr, $i, 1);
			$ascnum = ord($temp_str); //得到字符串中第$i位字符的ascii码
			if($ascnum >= 224){ //如果ASCII位高与224，
				$returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF - 8编码规范，将3个连续的字符计为单个字符
				$i = $i + 3; //实际Byte计为3
				$n++; //字串长度计1
			} elseif($ascnum >= 192){ //如果ASCII位高与192，
				$returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF - 8编码规范，将2个连续的字符计为单个字符
				$i = $i + 2; //实际Byte计为2
				$n++; //字串长度计1
			} elseif($ascnum >= 65 && $ascnum <= 90){ //如果是大写字母，
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; //实际的Byte数仍计1个
				$n++; //但考虑整体美观，大写字母计成一个高位字符
			} else{ //其他情况下，包括小写字母和半角标点符号，
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; //实际的Byte数计1个
				$n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
			}
		}
		if($mb_str_length > $cutlength){
			$returnstr = $returnstr . '...'; //超过长度时在尾处加上省略号
		}
		return $returnstr;
	}

	/*
	去除html标签,取出指定长度的字符串..
	参数$sourcestr  原字符串
	参数$cutlength  指定长度
	返回值:字符串
	去除空格
	*/
	public function cut_nstr($sourcestr, $cutlength){
		$sourcestr = strip_tags($sourcestr); //去除所有html标签
		//$sourcestr = str_replace(" & nbsp;","",$sourcestr);
		$sourcestr = preg_replace('#\s+#', '  ', trim($sourcestr)); //去除所有空白字符
		$returnstr = '';
		$i = 0;
		$n = 0;
		$str_length = strlen($sourcestr); //字符串的字节数
		$mb_str_length = mb_strlen($sourcestr, 'utf-8');
		while(($n < $cutlength) && ($i <= $str_length)){
			$temp_str = substr($sourcestr, $i, 1);
			$ascnum = ord($temp_str); //得到字符串中第$i位字符的ascii码
			if($ascnum >= 224){ //如果ASCII位高与224，
				$returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF - 8编码规范，将3个连续的字符计为单个字符
				$i = $i + 3; //实际Byte计为3
				$n++; //字串长度计1
			} elseif($ascnum >= 192){ //如果ASCII位高与192，
				$returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF - 8编码规范，将2个连续的字符计为单个字符
				$i = $i + 2; //实际Byte计为2
				$n++; //字串长度计1
			} elseif($ascnum >= 65 && $ascnum <= 90){ //如果是大写字母，
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; //实际的Byte数仍计1个
				$n++; //但考虑整体美观，大写字母计成一个高位字符
			} else{ //其他情况下，包括小写字母和半角标点符号，
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; //实际的Byte数计1个
				$n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
			}
		}
		return $returnstr;
	}

	/*
	* 验证验证码
	* $postcode 输入的验证码
	*/
	public function ckgdcode($postcode){
		$ckcode = $this->gCookie('lk_ckcode');
		if(!$postcode || !$ckcode)
		return FALSE;
		list($t, $n) = explode("\t", $ckcode);
		return md5($postcode . $t) == $n;
	}

	//字符编码
	public function LKEncode($txt, $key){
		srand((double) microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++){
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
		}
		return base64_encode($this->PKey($tmp, $key));
	}

	//加密字符
	public function PKey($txt, $encrypt_key){
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++){
			$ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
			$tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}

	/**
	* 编辑器获取函数
	* @param 2维数组 $var array('id'=>'xxx','type'='simple','content'='','width'=>'','height'=>'')
	*
	*/
	public function getEditor($arr){
		$editor = array();
		$jssrc = '<script language="javascript" src="/static/editor/kindeditor-min.js"></script><script language="javascript" src="/static/editor/lang/zh_CN.js"></script>';
		$editorjstxt = "<script>KindEditor.ready(function(K) {";
		foreach($arr as $v){
			$editorjstxt .= "editor = K.create('textarea[name={$v['id']}]', { allowFileManager : true, afterCreate : function() { this.sync(); }, afterBlur:function(){ this.sync(); } });";
		}
		$editorjstxt .= "});</script>";
		foreach($arr as $v){
			$editor[$v['id']] = '<textarea cols="' . $v['width'] . '" rows="' . $v['height'] . '" id="' . $v['id'] . '" name="' . $v['id'] . '">' . $this->rteSafe($v['content']) . '</textarea>';
		}
		$editor['jssrc'] = $jssrc;
		$editor['editorjstxt'] = $editorjstxt;
		return $editor;
	}

	public function rteSafe($strText){
		//	$strText = str_replace(chr(145), chr(39), $strText);
		//	$strText = str_replace(chr(146), chr(39), $strText);
		//	$strText = str_replace("'", " & #39;", $strText);
		//	$strText = str_replace(chr(147), chr(34), $strText);
		//	$strText = str_replace(chr(148), chr(34), $strText);
		//	$strText = str_replace(chr(10), " < br />", $strText);
		//	$strText = str_replace(chr(13), " < br />", $strText);
		$strText = str_replace(' &nbsp;  &nbsp;', '&#x000A;&#x000A;  ', $strText);
		$strText = str_replace('&nbsp;', ' ', $strText);
		//	$strText = str_replace(' > ', ' & gt;', $strText);
		return $strText;
	}

	/* 替换数组关键字
	* arr        2维 数组
	* rep        需要替换的关键字
	*/

	function getArrReplace($arr, $rep){
		if(is_array($arr)){
			$result = array();
			foreach($arr as $k => $v){
				if(isset($v[$rep])){
					$result[$v[$rep]] = $v;
				}
			}
			return $result;
		} else{
			return $arr;
		}
	}

	//取得IP
	public function getIP(){
		$isagent = TRUE;
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$currentIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
			$currentIP = $_SERVER['HTTP_CLIENT_IP'];
		} else{
			$currentIP = $_SERVER['REMOTE_ADDR'];
			$isagent = FALSE;
		}
		return array((preg_match('~[\d\.]{7,15}~', $currentIP, $match) ? $match[0] : 'unknow'), $isagent);
	}

	//写文件
	function LK_writeFile($filename, $content, $mode = 'ab', $chmod = 1){
		strpos($filename, '..') !== FALSE && exit('Access Denied!');

		$fp = @fopen($filename, $mode);
		if($fp){
			flock($fp, LOCK_EX);
			fwrite($fp, $content);
			fclose($fp);
			$chmod && @chmod($filename, 0666);
			return TRUE;
		}
		return FALSE;
	}

	//创建文件夹
	function createFolder($path){
		if(!is_dir($path)){
			$this->createFolder(dirname($path));
			@mkdir($path);
			@chmod($path, 0777);
			@fclose(@fopen($path . '/index.html', 'w'));
			@chmod($path . '/index.html', 0777);
		}
	}

	function gdEnable(){
		static $gdenable;
		if(is_bool($gdenable))
		return $gdenable;
		$m = array();
		$gdinfo = gd_info();
		preg_match('~([0-9\.]+?)\s~', $gdinfo['GD Version'], $m);
		$gdenable = ($m[1] >= '2.0.28' && function_exists('imagecreatetruecolor')) ? TRUE : FALSE;
		return $gdenable;
	}

	//判断是否为图片
	function isImg($imgpath){
		return (strpos($imgpath, '..') !== FALSE || !file_exists($imgpath) || !in_array($this->Fext($imgpath), array('jpg', 'jpeg', 'bmp', 'gif', 'png')) || (function_exists('getimagesize') && !@getimagesize($imgpath))) ? false : true;
	}

	//获取文件后缀
	function gExt($filename){
		return strtolower(trim(substr(strrchr($filename, '.'), 1)));
	}

	//读文件
	function LK_readFile($filename, $mode = 'rb'){
		strpos($filename, '..') !== FALSE && exit('Access Denied!');
		if($fp = @ fopen($filename, $mode)){
			flock($fp, LOCK_SH);
			$filedata = @ fread($fp, filesize($filename));
			fclose($fp);
		}
		return $filedata;
	}

	/**
	* 创建图片缩略图片
	* create images thumb
	* @param string $img
	* @param int $height
	* @param int $width
	* @param string  $save_prefix
	* @param bool $del
	*/
	function thumb($img, $width, $height, $save_prefix = 'thumb_', $repath = '', $del = false){
		if(empty($img) || !$this->gdEnable() || !$this->isImg($img))
		return $img;
		$imginfo = @getimagesize($img);
		switch($imginfo[2]){
			case 1:
			$tmp_img = @imagecreatefromgif($img);
			break;
			case 2:
			$tmp_img = imagecreatefromjpeg($img);
			break;
			case 3:
			$tmp_img = imagecreatefrompng($img);
			break;
			default:
			$tmp_img = imagecreatefromstring($img);
			break;
		}

		if($repath){
			$savepath = $repath;
		} else{
			if($save_prefix){
				$imgpath = substr($img, 0, strrpos($img, '/'));
				$filename = substr($img, strrpos($img, '/') + 1);
				$savepath = $imgpath . '/' . $save_prefix . $filename;
			} else{
				$savepath = $img;
			}
		}
		if(($height >= $imginfo[1] || !$height) && ($width >= $imginfo[0] || !$width)){
			if($save_prefix){
				@copy($img, $savepath) || $this->LK_writeFile($savepath, $this->LK_readFile($img), 'wb');
				$del && $this->LK_del($img);
			}
			return array($savepath, floor($imginfo[1]), floor($imginfo[0]));
		}
		$realscale = $imginfo[1] / $imginfo[0];
		if($realscale <= 1){
			$width = ($width > $imginfo[0] || !$width) ? $imginfo[0] : $width;
			$height = ($height > $imginfo[1] || !$height) ? $imginfo[1] : $width * $realscale;
		} else{
			$height = ($height > $imginfo[1] || !$height) ? $imginfo[1] : $height;
			$width = ($width > $imginfo[0] || !$width) ? $imginfo[0] : $height / $realscale;
		}
		$width = floor($width);
		$height = floor($height);
		$dst_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($dst_image, $tmp_img, 0, 0, 0, 0, $width, $height, $imginfo[0], $imginfo[1]);
		switch($imginfo[2]){
			case '1':
			imagegif($dst_image, $savepath);
			break;
			case '2':
			imagejpeg($dst_image, $savepath);
			break;
			case '3':
			imagepng($dst_image, $savepath);
			break;
			default :
			imagejpeg($dst_image, $savepath);
			break;
		}
		$save_prefix && $del && $this->LK_del($img);
		return array($savepath, $height, $width);
	}

	//文件拷贝
	function LK_copy($source, $dest){
		return @copy($source, $dest) || $this->LK_writeFile($dest, $this->LK_readFile($source), 'wb');
	}

	//文件移动
	function LK_move($source, $dest){
		if(@copy($source, $dest) || $this->LK_writeFile($dest, $this->LK_readFile($source), 'wb')){
			$this->LK_del($source);
			return true;
		}
	}

	//文件删除
	function LK_del($var){
		return strpos($var, '..') === FALSE && is_file($var) && @unlink($var) ? TRUE : FALSE;
	}

	function getRealSize($size){
		if($size < 1024){
			return $size . ' Byte';
		}
		if($size < 1048576){
			return round($size / 1024, 2) . ' KB';
		}
		if($size < 1073741824){
			return round($size / 1048576, 2) . ' MB';
		}
		if($size < 1099511627776){
			return round($size / 1073741824, 2) . ' GB';
		}
	}

	//获取首字母
	function getfirstchar($s0){
		$fchar = ord($s0{0});
		if($fchar >= ord("A") and $fchar <= ord("z"))
		return strtoupper($s0{0});
		$s1 = @iconv("UTF-8", "gb2312", $s0);
		$s2 = @iconv("gb2312", "UTF-8", $s1);
		if($s2 == $s0){
			$s = $s1;
		} else{
			$s = $s0;
		}
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if($asc >= - 20319 and $asc <= - 20284)
		return "A";
		if($asc >= - 20283 and $asc <= - 19776)
		return "B";
		if($asc >= - 19775 and $asc <= - 19219)
		return "C";
		if($asc >= - 19218 and $asc <= - 18711)
		return "D";
		if($asc >= - 18710 and $asc <= - 18527)
		return "E";
		if($asc >= - 18526 and $asc <= - 18240)
		return "F";
		if($asc >= - 18239 and $asc <= - 17923)
		return "G";
		if($asc >= - 17922 and $asc <= - 17418)
		return "H";
		if($asc >= - 17417 and $asc <= - 16475)
		return "J";
		if($asc >= - 16474 and $asc <= - 16213)
		return "K";
		if($asc >= - 16212 and $asc <= - 15641)
		return "L";
		if($asc >= - 15640 and $asc <= - 15166)
		return "M";
		if($asc >= - 15165 and $asc <= - 14923)
		return "N";
		if($asc >= - 14922 and $asc <= - 14915)
		return "O";
		if($asc >= - 14914 and $asc <= - 14631)
		return "P";
		if($asc >= - 14630 and $asc <= - 14150)
		return "Q";
		if($asc >= - 14149 and $asc <= - 14091)
		return "R";
		if($asc >= - 14090 and $asc <= - 13319)
		return "S";
		if($asc >= - 13318 and $asc <= - 12839)
		return "T";
		if($asc >= - 12838 and $asc <= - 12557)
		return "W";
		if($asc >= - 12556 and $asc <= - 11848)
		return "X";
		if($asc >= - 11847 and $asc <= - 11056)
		return "Y";
		if($asc >= - 11055 and $asc <= - 10247)
		return "Z";
		return null;
	}

	function getinitial($zh){
		$zh = $this->cut_nstr($zh, 1);
		$ret = "";
		$s1 = @iconv("UTF-8", "gb2312", $zh);
		$s2 = @iconv("gb2312", "UTF-8", $s1);
		if($s2 == $zh){
			$zh = $s1;
		}
		for($i = 0; $i < strlen($zh); $i++){
			$s1 = substr($zh, $i, 1);
			$p = ord($s1);
			if($p > 160){
				$s2 = substr($zh, $i++, 2);
				$ret .= $this->getfirstchar($s2);
			} else{
				$ret .= $s1;
			}
		}
		return $ret;
	}

	function verfiyCode(){
		include_once APP_PATH . "/mylib/class/seccode/checkcode.php";
	}

	function gdCode($num, $type){
		$numeral = '23456789';
		$string = 'ABCEFGHMNPRSTUVWXY';
		//1:num 2:str 4:both
		$randstr = $type & 1 ? $numeral : ($type & 2 ? $string : $numeral . $string);
		$randlen = strlen($randstr) - 1;
		mt_srand();
		$returndata = array();
		while($num--){
			$returnstr[] = strtolower($randstr[mt_rand(0, $randlen)]);
		}
		return $returnstr;
	}

	//读取系统基本信息
	function getSysConfigs(){
		$sysConfigs = $this->Setts->getConfig();
		foreach($sysConfigs as $row){
			$data[$row['ckey']] = $row['cvalue'];
		}
		return $data;
	}

	//随机码
	function random($length, $isNum = FALSE){
		$random = '';
		$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$num = '0123456789';
		if($isNum){
			$sequece = 'num';
		} else{
			$sequece = 'str';
		}
		$max = strlen($$sequece) - 1;
		for($i = 0; $i < $length; $i++){
			$random .= ${$sequece
			}{mt_rand(0, $max)
			};
		}
		return $random;
	}

	function return_json($msg){

		//        $CI = & get_instance();
		//
		//        $CI->load->library('json');
		//        $msg['data'] = (object) $msg['data'];
		echo json_encode($msg);
		exit;
	}

	/**
	* 获得其他内容标题图缩略图路径
	* @param String $file      大图地址
	* @param String $size      尺寸  middle  small
	*/
	public function getThumbImagePath($file, $size){
		$newFileName = "";
		$size = $size == "middle" ? "middle" : "small";
		$pathArr = explode("/", $file);
		$newFileName = str_replace($pathArr[count($pathArr) - 1], "{$size}_" . $pathArr[count($pathArr) - 1], $file);
		return $newFileName;
	}

	/**
	* 删除上传的图片等附件
	* @param String $url 图片访问url
	*/
	public function delImages($url){
		if(strpos($url, IMG_URL) !== false){
			return @unlink(str_replace(IMG_URL, IMAGE_PATH, $url));
		} else{
			return @unlink(IMAGE_PATH . $url);
		}
	}

	/**
	* 判断wap管理模块功能是否开启
	* @param String $code  栏目控制code标志
	*/
	public function wapPriv($code){
		//修改是 cookie 不能及时更新 换成实时读数据库
		//$privCodes = unserialize(base64_decode($this->gCookie('lk_project'))) ? unserialize(base64_decode($this->gCookie('lk_project'))) : array();
		$companyuid = $this->gCookie('lk_company_id');
		$info = $this->mysql->getOneSqlValue("SELECT * FROM lk_company WHERE id = {$companyuid}");
		$privCodes = unserialize(base64_decode($info['wapprojects'])) ? unserialize(base64_decode($info['wapprojects'])) : array();
		if(!in_array($code, @$privCodes)){
			$this->redirect('该模块未开启！', "/wapadmin/home/", 1);
		}
	}

	/**
	* 获得商家WAP站地址
	* @param String $uid
	*/
	public function getMyUrl($uid){
		//$domain = $this->mysql->getSqlValue("SELECT * FROM " . WAP_PREFIX . "userdomain WHERE uid = '$uid' order by type");
		$domain = $this->mysql->getOneSqlValue("SELECT * FROM " . WAP_PREFIX . "config WHERE uid={$uid}");
		if($domain){
			/* if (count($domain) == 2) {
			$mydomain = $domain[1]['type'] == 2 ? $domain[1]['value'] : $domain[0]['value'] . "." . WAP_DOMAIN;
			} elseif (count($domain) == 1) {
			$mydomain = $domain[0]['type'] == 1 ? $domain[0]['value'] . "." . WAP_DOMAIN : $domain[0]['value'];
			} */
			$mydomain = $domain['domain'] ? $domain['domain'] : "http://" . $domain['tpldomain'] . "." . WAP_DOMAIN . "/";
			//$mydomain = $domain['domain'] ? $domain['domain']: "http://".$domain['tpldomain'].".".WAP_DOMAIN." / 1000 / ";
			return $mydomain;
		} else{
			return "";
		}
	}

	/**
	* 删除某个目录下所有文件
	* @param String $path  绝对路径
	*/
	public function delFilesByDir($path = '.'){
		$current_dir = opendir($path);    //opendir()返回一个目录句柄,失败返回false
		while(($file = readdir($current_dir)) !== false){    //readdir()返回打开目录句柄中的一个条目
			$sub_dir = $path . DIRECTORY_SEPARATOR . $file;    //构建子目录路径
			if($file == '.' || $file == '..'){
				continue;
			} else
			if(is_dir($sub_dir)){    //如果是目录,进行递归
				//echo 'Directory ' . $file . ': < br > ';
				traverse($sub_dir);
			} else{    //如果是文件,直接输出
				//echo 'File in Directory ' . $path . ': ' . $file . ' < br > ';
				@unlink($sub_dir);
			}
		}
	}

	public function curl($url, $postFields = null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		if($postFields){
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
		}
		$reponse = curl_exec($ch);
		if(curl_errno($ch)){
			throw new Exception(curl_error($ch), 0);
		} else{
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if(200 !== $httpStatusCode){
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}

	public function md5_16($str){
		return substr(md5($str), 8, 16);
	}

	//mongodb创建编辑记录
	public function updateMongoRecord($code, $tablename, $values = array(), $type = "insert", $wstr){
		//生成mongodb
		switch($type){
			case "insert":
			$sql = "select column_name from information_schema.columns where table_schema='" . MONGO_DBNAME . "' and table_name='{$tablename}'";
			$filelist = $this->m_sql->getSqlValue($sql);
			$val = array();
			foreach($filelist as $row => $v){
				$val[$v['column_name']] = isset($values[$v['column_name']]) ? $values[$v['column_name']] : '';
			}
			$this->MongoPHP->insert("{$tablename}{$code}", $val);
			break;
			case "update":
			$this->MongoPHP->update("{$tablename}{$code}", $values, $wstr);
			break;
		}
	}

	//初始化mongoDB数据
	public function initializeMongo($code, $tablename, $wstr){
		//echo $code;die;
		$sql = "select column_name,data_type from information_schema.columns where table_schema='" . MONGO_DBNAME . "' and table_name='{$tablename}'";
		$filelist = $this->m_sql->getSqlValue($sql);
		$val = $this->mysql->getSqlValue("SELECT * FROM {$tablename} WHERE {$wstr} ");
		//print_r($filelist);die;
		if($val){
			foreach($val as $row => $v){
				foreach($filelist as $rs => $k){
					if($k['data_type'] == "int" || $k['data_type'] == "tinyint"){
						$insert[$k['column_name']] = isset($v[$k['column_name']]) ? (int) $v[$k['column_name']] : 0;
					} elseif($k['data_type'] == "float"){
						$insert[$k['column_name']] = isset($v[$k['column_name']]) ? (float) $v[$k['column_name']] : 0;
					} else{
						$insert[$k['column_name']] = isset($v[$k['column_name']]) ? $v[$k['column_name']] : '';
					}
				}
				$this->MongoPHP->insert("{$tablename}{$code}", $insert);
				$insert = array();
				//print_r($insert);die;
			}
		}
	}

	/*     * ************************************************************
	*
	*  使用特定function对数组中所有元素做处理
	*  @param  string  &$array     要处理的字符串
	*  @param  string  $function   要执行的函数
	*  @return boolean $apply_to_keys_also     是否也应用到key上
	*  @access public
	*
	* *********************************************************** */

	public function arrayRecursive( & $array, $function, $apply_to_keys_also = false){
		static $recursive_counter = 0;
		if(++$recursive_counter > 1000){
			die('possible deep recursion attack');
		}
		foreach($array as $key => $value){
			if(is_array($value)){
				$this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else{
				$array[$key] = $function($value);
			}
			if($apply_to_keys_also && is_string($key)){
				$new_key = $function($key);
				if($new_key != $key){
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
		$recursive_counter--;
	}

	/*     * ************************************************************
	*
	*  将数组转换为JSON字符串（兼容中文）
	*  @param  array   $array      要转换的数组
	*  @return string      转换得到的json字符串
	*  @access public
	*
	* *********************************************************** */

	public function JSON($array){
		$this->arrayRecursive($array, 'urlencode', true);
		$json = json_encode($array);
		return urldecode($json);
	}

	public function getContent($url, $data = array()){
		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $url);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		if($data){
			curl_setopt($ch2, CURLOPT_POST, 1); //设置为POST传输
			curl_setopt($ch2, CURLOPT_POSTFIELDS, $data); //post过去数据
		}
		$return_inof = curl_exec($ch2);
		curl_close($ch2);
		return $return_inof;
	}

	/** 记录设置cookie * */
	public function sCookie($name, $value, $expire = 0, $httponly = true){
		$lk_ckpre = $this->lk_ckpre;
		$lk_ckpath = '/';
		$lk_ckdomain = '';
		$timestamp = time();
		switch($expire){
			case 0:
			$expire = 0;
			break;
			case 1:
			$expire = $timestamp + 31536000;
			break;
			case - 1:
			$expire = $timestamp - 31536000;
			break;
			default:
			$expire += $timestamp;
			break;
		}
		!$lk_ckpath && $lk_ckpath = '/';
		$secure = ($_SERVER['SERVER_PORT'] == '443') ? 1 : 0;
		$ckpre = $lk_ckpre ? substr(md5($lk_ckpre), 8, 6) . '_' : '';
		if(PHP_VERSION >= '5.2.0'){
			return setcookie($ckpre . $name, $value, $expire, $lk_ckpath, $lk_ckdomain, $secure, ($httponly ? 1 : 0));
		} else{
			echo $lk_ckdomain;die;
			return setcookie($ckpre . $name, $value, $expire, '/', $lk_ckdomain, $secure);
		}
	}

	/*
	* 验证验证码
	* $var 输入的验证码
	*/

	public function gCookie($var){
		$lk_ckpre = $this->lk_ckpre;
		$ckpre = $lk_ckpre ? substr(md5($lk_ckpre), 8, 6) . '_' : '';
		return isset($_COOKIE[$ckpre . $var]) ? $_COOKIE[$ckpre . $var] : '';
	}

	public function authcode($string, $operation){
		/**
		* 获取密码算子,如未指定，采取系统默认算子
		* 默认算子是论坛授权码和用户浏览器信息的md5散列值
		* $GLOBALS['discuz_auth_key']----全局变量
		* 取值为:md5($_DCACHE['settings']['authkey'].$_SERVER['HTTP_USER_AGENT'])
		*/
		$lk_ckpre = $this->lk_ckpre;
		$key = md5($lk_ckpre);
		$key_length = strlen($key);
		/**
		* 如果解密，先对密文解码
		* 如果加密,将密码算子和待加密字符串进行md5运算后取前8位
		* 并将这8位字符串和待加密字符串连接成新的待加密字符串
		*/
		$string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
		$string_length = strlen($string);
		$rndkey = $box = array();
		$result = '';
		/**
		* 初始化加密变量,$rndkey和$box
		*/
		for($i = 0; $i <= 255; $i++){
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}
		/**
		* $box数组打散供加密用
		*/
		for($j = $i = 0; $i < 256; $i++){
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		/**
		* $box继续打散,并用异或运算实现加密或解密
		*/
		for($a = $j = $i = 0; $i < $string_length; $i++){
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'DECODE'){
			if(substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)){
				return substr($result, 8);
			} else{
				return '';
			}
		} else{
			return str_replace('=', '', base64_encode($result));
		}
	}

	//验证APPS端token
	public function chkToken($uid, $token){
		if(!$uid || !$token){
			show_simple_json(array('status' => 502, 'data' => array(), 'info' => '参数错误！'));
		}
		$userToken = $this->commonModel->getInfo(FRONT_PREFIX . 'user_token', "uid={$uid} AND token='{$token}'");
		if($userToken['token'] != $token || $userToken['expire_time'] <= date('Y-m-d H:i:s')){
			show_simple_json(array('status' => 502, 'data' => array(), 'info' => 'token已过期，请重新登录！'));
		}
	}

	//判断生日
	function birthdayTips($birthday){
		$date = explode("-", $birthday);
		$birthYear = date("Y") . "-" . $date[1] . "-" . $date[2];
		$differenceVal = (strtotime($birthYear) - strtotime(date('Y-m-d'))) / 86400;
		if($differenceVal == 0){
			return 1;
			die;
		} elseif($differenceVal <= 10 && $differenceVal >= - 3){
			return 2;
			die;
		} else{
			return 3;
			die;
		}
	}

	//卡路里计算公式
	/* double Calculate_Calorie_6min (int step_high_C,int step_medium_C,int step_low_C,double height_C,double weight_C){
	double calorie_high_C=0,calorie_medium_C=0,calorie_low_C=0,calorie_stop_C,calorie_6min_C;
	double step_length_high_C,step_length_medium_C,step_length_low_C;
	double velocity_high_C,velocity_medium_C,velocity_low_C;
	double time_C;
	height_C = height_C/100;
	if (step_high_C < 0 || step_medium_C < 0 || step_low_C < 0)
	calorie_6min_C = 0;
	else
	{ step_length_high_C = height_C;
	velocity_high_C = step_length_high_C * 3.5;
	calorie_high_C = velocity_high_C * weight_C  * step_high_C / 2800;
	step_length_medium_C = height_C / 1.2;
	velocity_medium_C = step_length_medium_C * 2.5;
	calorie_medium_C = velocity_medium_C * weight_C * step_medium_C / 2000;
	step_length_low_C = height_C / 3;
	velocity_low_C = step_length_low_C * 1.5;
	calorie_low_C = velocity_low_C * weight_C * step_low_C / 1200;
	time_C = step_high_C / 3.5 + step_medium_C / 2.5 + step_low_C / 1.5;
	if (time_C < 360)
	calorie_stop_C = (360 - time_C) * weight_C / 3600;
	else
	calorie_stop_C = 0;
	calorie_6min_C = calorie_high_C + calorie_medium_C + calorie_low_C + calorie_stop_C;
	}
	return calorie_6min_C;
	} */
	public function calorie6min($step_high_C, $step_medium_C, $step_low_C, $height_C, $weight_C){
		$calorie_high_C = $calorie_medium_C = $calorie_low_C = $calorie_stop_C = $calorie_6min_C = 0;
		$step_length_high_C = $step_length_medium_C = $step_length_low_C = 0;
		$velocity_high_C = $velocity_medium_C = $velocity_low_C = 0;
		$time_C = 0;
		$height_C = $height_C / 100;
		if($step_high_C < 0 || $step_medium_C < 0 || $step_low_C < 0){
			$calorie_6min_C = 0;
		} else{
			$step_length_high_C = $height_C;
			$velocity_high_C = $step_length_high_C * 3.5;
			$calorie_high_C = $velocity_high_C * $weight_C * $step_high_C / 2800;
			$step_length_medium_C = $height_C / 1.2;
			$velocity_medium_C = $step_length_medium_C * 2.5;
			$calorie_medium_C = $velocity_medium_C * $weight_C * $step_medium_C / 2000;
			$step_length_low_C = $height_C / 3;
			$velocity_low_C = $step_length_low_C * 1.5;
			$calorie_low_C = $velocity_low_C * $weight_C * $step_low_C / 1200;
			$time_C = $step_high_C / 3.5 + $step_medium_C / 2.5 + $step_low_C / 1.5;
		}
		if($time_C < 360){
			$calorie_stop_C = (360 - $time_C) * $weight_C / 3600;
		} else{
			$calorie_stop_C = 0;
			$calorie_6min_C = $calorie_high_C + $calorie_medium_C + $calorie_low_C + $calorie_stop_C;
		}
		return $calorie_6min_C;
	}

	//获取用户图像
	public function getUserPhoto($uid, $path, $types){
		$baseurl = "static/attachments/photo/{$uid}/{$types}_$path";
		$default_path = "static/attachments/photo/{$types}_default.jpg";
		if($path){
			if(file_exists(getcwd() . "/" . $baseurl)){
				$header = APP_URL . $baseurl;
            }else if(@$user['icon']){
                $header = $user['icon'];
            }else{
				$header = APP_URL . $default_path;
			}
		} else{
			if($uid){				
				$user = $this->commonmodel->getInfo("user_basic", "uid={$uid}", "icon");
				if($user['icon']){
					$header = $user['icon'];
				}else{
					$header = APP_URL . $default_path;
				}
			}else{
				$header = APP_URL . $default_path;
			}
		}
		return $header;
	}

	//获取列表页图片
	public function getPhoto($path, $files, $types){
		if($types){
			$baseurl = "static/attachments/{$files}/{$types}_{$path}";
		} else{
			$baseurl = "static/attachments/{$files}/{$path}";
		}
		$default_path = "static/attachments/{$files}/default.jpg";
		if($path){
			if(file_exists(getcwd() . "/" . $baseurl)){
				$header = APP_URL . $baseurl;
			} else{
				$header = APP_URL . $default_path;
			}
		} else{
			$header = APP_URL . $default_path;
		}
		return $header;
	}

	//将用户加入移除圈子操作
	public function circleUserAdmin($uid, $planid, $types){
		$this->load->model('commonModel', '', TRUE);
		//读取计划群id
		$circleInfo = $this->commonModel->getInfo("circle", "planid={$planid}", "id,planid");
		switch($types){
			case "add":
				$arr = array();
				$arr['uid'] = $uid;
				$arr['circleid'] = $circleInfo['id'];
				$arr['planid'] = $circleInfo['planid'];
				$arr['createtime'] = time();
				$result = $this->commonModel->getUpdate("circle_user", "", $arr);
				break;
			case "del":
				//删除圈子成员记录
				$result = $this->commonModel->getDel("circle_user", array('uid' => $uid, 'circleid' => $circleInfo['id']));
				//echo $this->db->last_query();
				//删除圈子成员聊天记录
				$result = $this->commonModel->getDel("circle_chat_log", array('uid' => $uid, 'circleid' => $circleInfo['id']));				
				break;
		}
	}

	//运动计划2奖4礼返还
	public function payPlanPoint($tplid, $planid, $types){
		//读取计划模板奖励规则数据
		$rewardRules = $this->commonModel->getInfo("plan_tpl_reward", "tplid={$tplid}", "*");
		//读取运动计划详情
		$planInfo = $this->commonModel->getInfo("userplan", "id={$planid}", "*");
		//读取计划群参与用户列表
		$planuserlist = $this->commonModel->getList(
			"userplan_order a LEFT JOIN user_account b ON a.uid=b.uid", "a.pid={$planid} AND a.state=1 AND a.audit=1 AND a.paystate=1", "a.pid,a.uid,b.points"
		);
		switch($types){
			case "joingive": //开学礼
				if($rewardRules['joingive'] && (int)$rewardRules['joingive']>0){
					$this->getPoints($planuserlist, $rewardRules['joingive'], '开学礼');
				}
				break;
		}
	}

	//返还积分、插入日志记录
	/**
	*
	* @param undefined $userlist   用户列表（对应计划id, 用户id, 当前账户积分值）
	* @param undefined $points    此次积分增减状况
	* @param undefined $notice    积分说明
	*
	* @return
	*/
	public function getPoints($userlist, $points, $notice, $dotype = 'plan'){
		$syspara = $this->systemModel->getList("ckey like '%lk_%'");
		//读取积分人民币比例
		foreach($syspara as $row=>$v){
			if($v['ckey'] == "lk_point_money"){
				$lk_point_money = $v['cvalue'];
			}
		}
		$lk_point_money = 1; //$lk_point_money ? $lk_point_money : 1;
		foreach($userlist as $row => $v){
			$backpoints = $points + $v['points'] * $lk_point_money;
			//判断是否有记录存在
			$rs = $this->commonModel->getCount("user_account", array("uid" => $v['uid']));
			if($rs){
				$result = $this->commonModel->getUpdate("user_account", array("uid" => $v['uid']), array('points' => $backpoints));
			} else{
				$result = $this->commonModel->getUpdate("user_account", '', array('points' => $backpoints, "uid" => $v['uid']));
			}
			if($result){
				$outin = ($points > 0) ? 'in' : 'out';
				$logArr = array(
					'uid' => $v['uid'],
					'outin' => $outin,
					'objid' => $v['pid'],
					'points' => $points ? $points*$lk_point_money : 0,
					'content' => $notice,
					'dotype' => $dotype,
					'createtime' => time()
				);
				$this->commonModel->getUpdate("user_points_log", "", $logArr);  //积分日志
			}
		}
		return true;
	}

	//根据地址id获取地址
	public function getAttStr($ints){
		$att = '';
		if($ints){
			//var_dump($ints);
			$att = $this->commonModel->getInfo('common_area', 'areaid = ' . $ints);
			//var_dump($att);
			if($att){
				$att = $att['name'];
			}
		}
		return $att;
	}

	//根据字符串获取地址ID
	public function getAttInt($str){
		$att = '';
		if($str){
			$att = $this->commonModel->getInfo('common_area', 'name =  ' . $str);
			if($att){
				$att = $att['areaid'];
			}
		}
		return $att;
	}

	//获取购物车图片
	public function getGoodsPhoto($id, $type){
		$photo = '';
		if($type && ($type == 'fruit' || $type == 'equipment' || $type == 'lease')){
			if($type == 'fruit'){
				$table = 'store_fruit';
			} elseif($type == 'equipment'){
				$table = 'store_equipment';
			} elseif($type == 'lease'){
				$table = 'store_lease';
			}
			$goods = $this->commonmodel->getInfo($table, "id = {$id}");
			if($goods){
				if($type == 'fruit' || $type == 'lease'){
					$album = explode(",", trim($goods['album'], "[]"));
					if(is_array($album)){
						$photo = "http://" . $_SERVER['HTTP_HOST'] . "/static/" . 'attachments/store/' . trim($album[0], '"');
					}
				} else{
					$photo = $goods['photo'] ? "http://" . $_SERVER['HTTP_HOST'] . "/static/" . 'attachments/store/' . $goods['photo'] : '';
				}
			}
		}
		return $photo;
	}


	/*****************************************************
	**													**
	** 多维数组排序（转换为二维数组）					**
	** @para  $old_arr	原始数组 						**
	** @para  $arr1 	按元素一排序（第一位）			**
	** @para  $arr2 	按元素二排序（第二位）			**
	**													**
	******************************************************/

	public function multipleArraySort($old_arr,$arr1,$arr2){
		//排序第一个元素
		$old_arr = $this->array_sort($old_arr,$arr1[0],$arr1[1]);
		//print_r($old_arr);
		$temp_array = $new_temp_array = array();
		foreach($old_arr as $k => $v){
			$key_count = count($old_arr)-1;
			$temp_array[$k] = $v;
			if($k>0){
		            if(($v[$arr1[0]] != $old_arr[$k-1][$arr1[0]] && !empty($temp_array)) || $key_count==$k){
					//添加最新元素
					$temp_array[$k] = $v;
					//数组大于1时，进行排序处理
					if(count($temp_array) > 1){
						if($key_count != $k){
							//删除最后一个（与上一个不相等元素）
							unset($temp_array[$k]);
						}
						//不是最后或者最后相同，进行排序
						if($key_count != $k || $v[$arr1[0]] == $old_arr[$k-1][$arr1[0]]){
							//排序第二个元素
							$temp_array = $this->array_sort($temp_array,$arr2[0],$arr2[1]);
						}
						
						//排序后把排序的数组，插入原数组排序前位置
						$count = count($new_temp_array)==0 ? 0 : count($new_temp_array);
						foreach($temp_array as $val_t){
							$new_temp_array[$count] = $val_t;
							$count++;
						}
						//写入新数组后清空原数组
						$temp_array = array();
						//添加最新元素
						$temp_array[$k] = $v;
					}
				}
			}
		}
		return $new_temp_array;
	}

/*****************************************************
**													**
** 二维数组排序==========         					**
** @para  $old_arr	原始数组 						**
** @para  $Firste 	按元素一排序（第一位）			**
** @para  $Seconde 	按元素二排序（第二位）			**
**													**
******************************************************/
public function multipleArraySortErArr($data,$Firste,$Seconde){
	    //先按allpersonalStandard排序，再按complete_avg排序
		foreach ($data as $key => $value) {
		
		$Firstelement[$key] = $value[$Firste];
		
		$Secondelement[$key] = $value[$Seconde];
		
		}
		
		array_multisort($Firstelement, $Secondelement, $data);
		return  $data;
}

/*多维数组排序
$multi_array:多维数组名称
$sort_key:二维数组的键名
$sort:排序常量 SORT_ASC || SORT_DESC
*/
function multi_array_sort(&$multi_array,$sort_key,$sort=SORT_DESC){
				if(is_array($multi_array)){
						foreach ($multi_array as $row_array){
								if(is_array($row_array)){
								//把要排序的字段放入一个数组中，
								$key_array[] = $row_array[$sort_key];
								}else{
								return false;
								}
						}
				}else{
			         	return false;
				}
				//对多个数组或多维数组进行排序
				array_multisort($key_array,$sort,$multi_array);
				return $multi_array;
} 

	/*****************************************************
	**													**
	** 根据数组元素排序									**
	** @para  $arr 		数组 							**
	** @para  $keys 	排序元素 						**
	** @para  $type 	排序方式asc升序  desc降序		**
	**													**
	******************************************************/
	public function array_sort($arr, $keys, $type='asc'){
		$keysvalue = $new_array = array();
		//获取元素值，生成新数组
		foreach($arr as $k=>$v){
			$keysvalue[$k] = $v[$keys];
		}
		//排序，默认为正序
		if($type == 'asc'){
			asort($keysvalue);
		}else{
			arsort($keysvalue);
		}
		reset($keysvalue);
		$count_s = 0;
		//恢复键值
		foreach ($keysvalue as $k=>$v){
			$new_array[$count_s] = $arr[$k];
			$count_s++;
		}
		return $new_array;
	}


/*二维数组倒序array_reduce()*/
public function TwoReverse(array $array){
$count=count($array);
//$tmp = array_map(array($this, 'rev'), $array);//将二维数组中的一维数组一个一个拉去运算
$tmp=array();
foreach($array as $key=>$value){
	$tmp[]=$array[$count-$key-1];
}
//print_r($tmp);
return $tmp;
}

/*public function rev($v) {
	
	$arr[]=$v;
	
    return $arr;
}
*/
	
//app接口判断是否为空
	public function isnull($str) {
	    return $str ? strip_tags($str) : '';
	}

	/******
	* 给出出生日期，计算年龄
	* @para  birthday  出生日期
	******/
	public function getage($birthday){
		$nowtime = time();
		$birthtime = ($birthday) ? $birthday: strtotime("1970-01-01");
		$agetime = $nowtime - $birthtime;
		$age = floor($agetime/86400/365);
		return $age;
	}

	/*******************************************
	** app接口输出
	** @para   status   状态
	** @para   info     状态说明
	** @para   data     状态返回数据 数组array()
	********************************************/
	public function apioutput($status, $info, $data){
		//echo strtotime("1970-01-01 00:00:01");
		$msg['status'] = $status;
        $msg['info'] = $info;
        $msg['data'] = $data;
        $this->return_json($msg);
	}

	/*******************************************************
	**
	** 任务计划个人排名计算
	**
	** @para   planid   	计划id
	** @para   uid      	用户id
	** @para   types    	查询类型1.当日排行  2.总排行
	** @para   datetime 	types为单日的时候传输的时间戳
	**
	********************************************************/
	public function planrank($planid, $uid, $types, $datetime=''){
		$rank = 0;
		$planInfo = $this->commonModel->getInfo("userplan", "id={$planid}", "groupnum,enddate,startdate");
		switch($types){
			case 1: //单日排行
				//步数读取
				$datesList = $this->commonModel->getList(
					"userplan_order b LEFT JOIN user_datas a ON a.uid=b.uid",
					"b.pid={$planid} AND a.type=1 AND a.datetime={$datetime} AND b.state=1 AND b.audit=1 ORDER BY a.sumsteps DESC",
					"a.uid,a.sumsteps,b.group"
				);
				if($datesList){
					$ranklist = array();
					if($planInfo['groupnum']==1){	  //单人组排序-------------------------					
						foreach($datesList as $row=>$v){
							@$ranklist[$row]['sumsteps'] = $v['sumsteps'];
							@$ranklist[$row]['ismy'] = ($v['uid']==$uid) ? 1: 0;
						}
					}else{  	//多人组排序-------------------------
						foreach($datesList as $row=>$v){
							@$ranklist[$v['group']]['sumsteps'] += $v['sumsteps'];
							@$ranklist[$v['group']]['ismy'] += ($v['uid']==$uid) ? 1: 0;
						}
					}					
					usort($ranklist,
						function($a, $b){
							$al = $a['sumsteps'];
							$bl = $b['sumsteps'];
							if($al == $bl) return 0;
							return ($al > $bl) ? - 1 : 1;
						}
					);
					foreach($ranklist as $row=>$v){
						if($v['ismy']){
							$rank = $row+1;
						}
					}
				}
				
				break;
			case 2: //总排行
				//步数读取
				$datesList = $this->commonModel->getList(
					"userplan_order b LEFT JOIN user_datas a ON a.uid=b.uid LEFT JOIN user_points_log c ON c.objid={$planid} AND dotype='plan'",
					"b.pid={$planid} AND a.type=1 AND a.datetime BETWEEN {$planInfo['startdate']} AND {$planInfo['enddate']} AND b.state=1 AND b.audit=1",
					"a.uid,a.sumsteps,b.group,c.points"
				);
				if($datesList){
					$sumstepsList = array();
					if($planInfo['groupnum']==1){  //单人组排序-------------------------					
						foreach($datesList as $row=>$v){
							@$sumstepsList[$row]['sumsteps'] += $v['sumsteps'];
							@$sumstepsList[$row]['points'] += $v['points'];
							@$sumstepsList[$row]['ismy'] = ($v['uid']==$uid) ? 1: 0;
						}
					}else{  //多人组排序-------------------------
						foreach($datesList as $row=>$v){
							@$sumstepsList[$v['group']]['sumsteps'] += $v['sumsteps'];
							@$sumstepsList[$v['group']]['points'] += $v['points'];
							@$sumstepsList[$v['group']]['ismy'] += ($v['uid']==$uid) ? 1: 0;
						}
					}
					if(count($sumstepsList)>1){
						$pointsRank = array('points', 'desc');
						$sumstepRank = array('sumsteps', 'desc');
						$ranklist = $this->multipleArraySort($sumstepsList,$pointsRank,$sumstepRank);
						//print_r($ranklist);die;
						foreach($ranklist as $row=>$v){
							if($v['ismy']){
								$rank=$row+1;
							}
						}
					}else{
						$rank=1;
					}					
				}
				break;
		}
		return $rank;
	}

	/*******************************************************
	** 今日步数排名
	********************************************************/
	
	public function TodayStepsRank($planid, $uid,$datetime=''){
		
		
		$rank = 0;
		$planInfo = $this->commonModel->getInfo("userplan", "id={$planid}", "id");
		
		//步数读取
		$datesList = $this->commonModel->getList(
			"userplan_order b LEFT JOIN user_datas a ON a.uid=b.uid",
			"b.pid={$planid} AND a.type=1 AND a.datetime={$datetime} AND b.state=1 AND b.audit=1 ORDER BY a.sumsteps DESC",
			"a.uid,a.sumsteps"
		);
		$mysteps=0;
		if($datesList){
			foreach($datesList as $v){
				if($uid==$v['uid']){
					$mysteps=$v['sumsteps'];
				}
			}
		}
		$array=array_column($datesList,'sumsteps');
	    return $this->GetMyParallelRank($array,$mysteps);
		
		
	}



	
	
		/*******************************************************
	**
	** 判断抵用券能不能使用,当前的商品类别
	**
	** @para   id    抵用券id
	** @para   code      	抵用券编码
	**$uid
	*$voucher_type 1全平台使用,2单个商品水果，3单类商品，4,单个计划，5单类计划,6计划通用,7商品通用,8,单个/多个-装备商品,9单个/多个-租赁商品
		 * $type 订单类型：plan 计划，shop商城
		 * $orderid 计划id或商品订单orderid
		 * $order_cate 订单分类,,计划tplid或商品类别'all','fruit','equipment','lease'
		 * 例如：计划is_voucher_order_item('','VC151224111756BSXVS',11165,'plan','13','12');
		 * 商品           is_voucher_order_item('','VC151224111756BSXVS',11165,'shop','fruit','PO145092707611220gw453');
	********************************************************/
public function is_voucher_order_item($id='',$code='',$uid='',$type="plan",$order_cate='',$orderid=''){
	   $this->load->model('commonmodel', 'commonModel');
	   $this->datas=$this->msg=array();
	   	$voucher_goods_id_Arr=$shopidArr=array();
	   if($uid<=0){
	   		 	$this->msg['status'] = 500;
	            $this->msg['info'] = '抵用券所有者不能为空';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
				
	   }
	   
	   if(!$id&&!$code){
	   		 	$this->msg['status'] = 500;
	            $this->msg['info'] = '抵用券参数错误';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
				
	   }
	   
	  if(!$orderid||!$order_cate){
	   		 	$this->msg['status'] = 500;
	            $this->msg['info'] = '订单参数错误';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
				
	   }
		  
	   //voucher_order_item,得到我的未使用抵用券信息
	   if($id){
	   $user_voucher_info = $this->commonModel->getInfo('voucher_order_item'," id={$id} and uid={$uid} and state=0",'*');
		   
	   }else{
	    $user_voucher_info = $this->commonModel->getInfo('voucher_order_item',"code='{$code}' and uid={$uid} and state=0",'*');
	   }
	  
	   if($user_voucher_info){
	   	     
			 if($user_voucher_info['validity_ctime']<time()&&!($user_voucher_info['validity_ctime']==0)){
			 	$this->msg['status'] = 500;
	            $this->msg['info'] = '抵用券已过期';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
			 }
			 $voucher_goods_id_Arr=explode(',', $user_voucher_info['voucher_goods_id']);
			 if($type=='plan'&&$order_cate){//计划订单4,单个计划，5单类计划,6计划通用,1全平台使用,
			 		if($user_voucher_info['voucher_type']==4&&in_array($orderid, $voucher_goods_id_Arr)){
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==5&&in_array($order_cate, $voucher_goods_id_Arr)){
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==6||$user_voucher_info['voucher_type']==1){
						return $user_voucher_info;
					}
					 
			 }
			 
				 if($type=='shop'&&$order_cate){//商城订单,7商品通用,2水果,8装备,9租赁,1全平台使用,，3单类商品
				 
				    $shopidArr=$this->commonModel->getList('orders_item',"orderid='{$orderid}' ");
				    $shopidArr=array_column($shopidArr,'pid');
					//$shopidArr=array_values($shopidArr);
					
					
                    $result=array_intersect($shopidArr,$voucher_goods_id_Arr);//计算交集
                 
			 		if($user_voucher_info['voucher_type']==2&&$order_cate=='fruit'&&count($result)){//水果
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==8&&$order_cate=='equipment'&&count($result)){//装备
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==9&&$order_cate=='lease'&&count($result)){//租赁
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==3&&in_array($order_cate, $voucher_goods_id_Arr)){
						return $user_voucher_info;
					}
					if($user_voucher_info['voucher_type']==7||$user_voucher_info['voucher_type']==1){
						return $user_voucher_info;
					}
					 
			 }
			
			
			    $this->msg['status'] = 500;
	            $this->msg['info'] = '您当前的抵用券适用范围受限,不能使用';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
		
	   }else{
	        	$this->msg['status'] = 500;
	            $this->msg['info'] = '您输入的抵用券不存在或已使用';
	            $this->msg['data'] = (object) $this->datas;
	            $this->return_json($this->msg);
	   }
	    
		
}


/*
 * 
 * 显示抵用券===显示将赠送的抵用券信息
 * */
 
 public function voucher_show($tplid='',$uid=''){
 		$this->load->model('commonmodel', 'commonModel');
			//查询将赠送的优惠券
		
		$voucher_arr = $this->commonModel->getList(
			"plan_tpl_vouchers a LEFT JOIN voucher b ON a.voucher_id=b.id",
			"a.tplid={$tplid}", "a.*,b.title,b.photo "
		);
		
		$voucherlist=array();
		if($voucher_arr){
			foreach($voucher_arr as $rv){//gift_types---0不限制次数，1平台总次数限制，2个人总次数限制
				
				if($rv['gift_types']==1){
					//查询抵用券的总数
					$vouCount= $this->commonModel->getCount("voucher_order_item", array("vid"=>$rv['voucher_id']));
					if($rv['gift_nums']<=$vouCount){
						continue;
					}
					
				}else if($rv['gift_types']==2){
					$vouCount= $this->commonModel->getCount("voucher_order_item", array("vid"=>$rv['voucher_id'],'uid'=>$uid));
					if($rv['gift_nums']<=$vouCount){
						continue;
					}
					
				}
				
				$voucherlist[]=array(
				'id'=>$rv['voucher_id'],//抵用券id
				'title'=>$rv['title'],
				'photo'=>$rv['photo']?(WEBROOT.IMAGE_PATH."voucher/".$rv['photo']):'',
				);
				
			}
		}
		
		return $voucherlist;
		//查询将赠送的优惠券
		
 }

/*
 * 赠送抵用券===生成用户的抵用券
 * 
 * */
public function voucher_gift($tplid='',$uid=''){
	
	$this->load->model('commonmodel', 'commonModel');
	

	$voucher_arr = $this->commonModel->getList(
			"plan_tpl_vouchers a LEFT JOIN voucher b ON a.voucher_id=b.id",
			"a.tplid={$tplid}", "a.*,b.* "
		);
	//gift_types,0不限制次数，1平台总次数限制，2个人总次数限制
	
		if($voucher_arr){
				foreach($voucher_arr as $rv){//gift_types---0不限制次数，1平台总次数限制，2个人总次数限制
					if($rv['gift_types']==1){
						//查询抵用券的总数
						$vouCount= $this->commonModel->getCount("voucher_order_item", array("vid"=>$rv['voucher_id']));
						if($rv['gift_nums']>$vouCount){
								$this->Generatevoucher($uid,$rv);
						}
						
					}else if($rv['gift_types']==2){
						$vouCount= $this->commonModel->getCount("voucher_order_item", array("vid"=>$rv['voucher_id'],'uid'=>$uid));
						if($rv['gift_nums']>$vouCount){
							$this->Generatevoucher($uid,$rv);
						}
						
					}else{
						 $this->Generatevoucher($uid,$rv);
					}
					
					
				}
				
			}
		return FALSE;
	
}




//为某个用户---生成抵用券
//$uid,用户id
//抵用券信息$vou_v array()
public function Generatevoucher($uid,$vou_v){
		                //$this->load->model('commonmodel', 'commonModel');
							//计算有效期
									
							if($vou_v['validity_day']>0){
								$validity_ctime=time()+$vou_v['validity_day']*86400;
							}else if($vou_v['validity_ctime']>0){
								$validity_ctime=$vou_v['validity_ctime'];
							}else{
								$validity_ctime=0;
							}
							$inserArr = array();
							$inserArr['vid'] = $vou_v['id'];
							$inserArr['uid'] = $uid;
							$inserArr['price'] = $vou_v['price'];
							$inserArr['validity_ctime']=$validity_ctime;
							$inserArr['voucher_type'] = $vou_v['voucher_type'];
							$inserArr['voucher_goods_id'] = $vou_v['voucher_goods_id'];
							$lastcode = implode("",$this->publics->gdCode(5, 4));
							$inserArr['code'] = "VC".date("ymdhis").strtoupper($lastcode);
							$inserArr['createtime'] = time();
							$result = $this->commonModel->getUpdate("voucher_order_item", "", $inserArr);	
}




/*
    快速排序,其实快速排序之所以称之快速，就是因为，冒泡排序是每次对比只交换相邻的两个值的位置，
 * 这样每个值要移动到它最终的排序结果中所对应的位置，
 * 可能需要很多次位置的变化。但是快速排序可在一次划分中，就确定你选定的那个对比值在最终排序好的队列中的位置
 * -----------------
 * 他的思想是先对数组进行分割， 把大的元素数值放到一个临时数组里，把小的元素数值放到另一个临时数组里
 * （这个分割的点可以是数组中的任意一个元素值，一般用第一个元素，即$array[0]），
 * 然后继续把这两个临时数组重复上面拆分，最后把小的数组元素和大的数组元素合并起来。这里用到了递归的思想
*/

// 快速排序---将一个打乱的一维数组，按从小到大的顺序排列
function quickSort($array)
{
    if(!isset($array[1])){
    	return $array;
    }
    $mid = $array[0]; //获取一个用于分割的关键字，一般是首个元素
    $leftArray = array();
    $rightArray = array();

    foreach($array as $v)
    {
        if($v>$mid){
            $rightArray[] = $v;  //把比$mid大的数放到一个数组里
		}
        if($v<$mid){
            $leftArray[] = $v;   //把比$mid小的数放到另一个数组里
		}
    }

    $leftArray = $this->quickSort($leftArray); //把比较小的数组再一次进行分割
    $leftArray[] = $mid;        //把分割的元素加到小的数组后面，不能忘了它哦

    $rightArray = $this->quickSort($rightArray);  //把比较大的数组再一次进行分割
    return array_merge($leftArray,$rightArray);  //组合两个结果
}

/*并列排名算法
公式为： 名次=总人数--比自己小的数的个数-这个分数重复次数+1（加上自己)
得到名次的数组再根据对应的id写入到数据库，就实现rank的计算功能
（当然这个也可以改成这样195,180,180,165,名次是这样的1，2，2，3）
//获得一组数的名次的数组，，取得所有的排名*/
public function  ParallelRank(array $array){
        foreach($array as $val){
        	    $repeat=$this->get_array_repeats($val,$array);
                $num=$this->gt_array_values($val,$array);
                $rank[]=count($array)-$num-$repeat+1;
        }
        return $rank;
}

//得到我的并列排名，名次
public function GetMyParallelRank(array $array,$myrank){
	
		foreach($array as $val){
	        	    $repeat=$this->get_array_repeats($val,$array);
			        $num=$this->gt_array_values($val,$array);
					if($myrank==$val){
						$rank=count($array)-$num-$repeat+1;
					}
	                
	        }
		if(!$rank){
			return 0;
		}
        return $rank;
	
}
//获得比自己数小的个数
public function gt_array_values($val,array $array){
        $num=0;
        for($i=0;$i<count($array);$i++){
                if($val>$array[$i]){
                        $num++;
                }
        }
        return $num;
}
//获得这个数的重复次数
public function get_array_repeats($string,array $array) {
	    $array =implode(',', $array);
		$array =explode(',',$array);//需要转换成标准array(50,12,22);格式
        $count = array_count_values($array);//有些一维数组会失败
        foreach ($count as $key => $value) {
                 if ($key == $string) {
                  return $value;
                  }
         }
}


//过滤微信特殊符号
public function RemoveEmoji($nickname) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $nickname);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
	$clean_text = preg_replace($regexDingbats, '', $clean_text);

	//过滤ios下苹果的表情
	$tmpStr = json_encode($clean_text); ////可以为收到的微信消息，可能包含二进制emoji表情字符串暴露出unicode
	$re = '/\\\\ue[0-9a-f]{3}/';
	$tmpStr = preg_replace($re, '', $tmpStr);
    $clean_text = json_decode($tmpStr);

    return $clean_text;
}



//end class
}

