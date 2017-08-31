<?php

/**
 * 微信配置常量
 */
define('APPID', 'wxe7de438272befca0');
//受理商ID，身份标识
define('MCHID', '1259975001');
//商户支付密钥Key。审核通过后，在微信发送的邮件中查看（新版本中需要自己填写）
define('KEY', 'abcdefghijklmnopqrstuvwxyz123456');
//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
define('APPSECRET', '84805fd5c1985693f6e4f81ed8d258f6');
//=======【JSAPI路径设置】===================================
//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
define('JS_API_CALL_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/order/wxpay');
//=======【异步通知url设置】===================================
//异步通知url，商户根据实际开发过程设定
define('NOTIFY_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/order/wxpaynotify');

//=======【curl超时设置】===================================
//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
define('CURL_TIMEOUT', '30');

function return_json($msg) {
    $CI = &get_instance();
    $CI->load->library('json');
    $msg['data'] = (object) $msg['data'];
    echo $CI->json->encode($msg);
    exit;
}

function isLogin($type = 1) {
    $CI = &get_instance();
    $user = $CI->session->userdata('user');
    if ($user && ($user['type'] == $type)) {
        $lg_userinfo = array(
            'id' => $user['id'],
            'realname' => $user['realname'],
            'nickname' => $user['nickname'],
            'mobile' => $user['mobile']
        );
        return $lg_userinfo;
    } else {
        return false;
    }
}

/*
 * 页面跳转
 * $msg         说明
 * $url         链接
 * $redirect    自动跳转
 * $time        时间
 */

function redirect($msg, $url = '', $redirect = 0, $time = 3000) {
    $str = '';
    $str .= '<html>
		<head>
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
		</style>
		</head>
		<body>
		
		<div id="msg">
			<div id="msgtitle">
				<h1>提示信息</h1>
			</div>
			<div id="msgcontent">
				' . $msg . '<p>';
    if ($redirect && $url) {
        $str .= '<a href="' . $url . '">如果您的浏览器没有自动跳转，请点击这里</a>
					<script type="text/javascript">
						setTimeout("window.location.href =\'' . $url . '\';",' . ($time) . ');
					</script>
					';
    } else {
        $str .= '<a href="' . ($url ? $url : "javascript:history.go(-1)") . '">返回继续操作</a>';
    }
    $str .= '</p>
			</div>
		</div>
		</body>
		</html>';
    echo $str;
    die;
}

//导入EXCEL
function importexcel($path) {
    $data = array();
    if (is_file(FCPATH . $path)) {
        set_include_path(get_include_path() . PATH_SEPARATOR . 'excel/');

        //set_include_path(' http://localhost/PHPExcel/phpexcel/');
        /** PHPExcel */
        include 'static/excel/PHPExcel.php';
        /** PHPExcel_IOFactory */
        include 'static/excel/PHPExcel/IOFactory.php';

        /** include php_excel5 */
        include 'static/excel/PHPExcel/Reader/Excel5.php';

        $reader = new PHPExcel_Reader_Excel5();
        $reader->setReadDataOnly(true); // Not read styles

        $excel = $reader->load(FCPATH . $path); //excel的路径
        $data = $excel->getActiveSheet()->toArray();

        return $data;
    }
}

function excelTime($days, $time = false) {
    if (is_numeric($days)) {
        //based on 1900-1-1
        $jd = GregorianToJD(1, 1, 1970);
        $gregorian = JDToGregorian($jd + intval($days) - 25569);
        $myDate = explode('/', $gregorian);
        $myDateStr = str_pad($myDate[2], 4, '0', STR_PAD_LEFT)
                . "-" . str_pad($myDate[0], 2, '0', STR_PAD_LEFT)
                . "-" . str_pad($myDate[1], 2, '0', STR_PAD_LEFT)
                . ($time ? " 00:00:00" : '');
        return $myDateStr;
    }
    return $days;
}

/* 替换字符
 * $str 字符串
 */

function ITxt($str) {//可以改成数组替换
    $str = str_replace("../../editor_upload/", "/static/editor_upload/", $str);
    $str = str_replace("&quot;", "", $str);
    $str = str_replace("\&quot;", "", $str);
    $str = str_replace("\\", "", $str);
    $str = str_replace("<script>", "&lt;script&gt;", $str);
    $str = str_replace("</script>", "&lt;/script&gt;", $str);
    return $str;
}

//获取当前网址
function getCurrentURL() {
    $url = 'http://' . $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != 80) {
        $url .=':' . $_SERVER['SERVER_PORT'];
    }
    $url .=$_SERVER['REQUEST_URI'];
    return $url;
}

/* 截取字符串
 * $sourcestr 字符串
 * $cutlength 长度
 */

function cut_str_cn($sourcestr, $cutlength) {

    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr); //字符串的字节数
    $mb_str_length = mb_strlen($sourcestr, 'utf-8');
    while (($n < $cutlength) && ($i <= $str_length)) {
        $temp_str = substr($sourcestr, $i, 1);
        $ascnum = ord($temp_str); //得到字符串中第$i位字符的ascii码
        if ($ascnum >= 224) { //如果ASCII位高与224，
            $returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i = $i + 3; //实际Byte计为3
            $n++; //字串长度计1
        } elseif ($ascnum >= 192) { //如果ASCII位高与192，
            $returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i = $i + 2; //实际Byte计为2
            $n++; //字串长度计1
        } elseif ($ascnum >= 65 && $ascnum <= 90) { //如果是大写字母，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        } else { //其他情况下，包括小写字母和半角标点符号，
            $returnstr = $returnstr . substr($sourcestr, $i, 1);
            $i = $i + 1; //实际的Byte数计1个
            $n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($mb_str_length > $cutlength) {
        $returnstr = $returnstr . '...'; //超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/*
 * 验证验证码
 * $postcode 输入的验证码
 */

function ckgdcode($postcode) {
    $ckcode = gCookie('lk_ckcode');
    if (!$postcode || !$ckcode)
        return FALSE;
    list($t, $n) = explode("_", $ckcode);
    return md5($postcode . $t) == $n;
}

/*
 * 验证验证码
 * $var 输入的验证码
 */

function gCookie($var) {
    $lk_ckpre = 'wlc';
    $ckpre = $lk_ckpre ? substr(md5($lk_ckpre), 8, 6) . '_' : '';

    return isset($_COOKIE[$ckpre . $var]) ? $_COOKIE[$ckpre . $var] : '';
}

/*
 * 记录验证码
 * 
 */

function sCookie($name, $value, $expire = 0, $httponly = true) {
    $lk_ckpre = 'wlc';
    $lk_ckpath = '/';
    $lk_ckdomain = '';
    $timestamp = time();

    switch ($expire) {
        case 0:
            $expire = 0;
            break;
        case 1:
            $expire = $timestamp + 31536000;
            break;
        case -1:
            $expire = $timestamp - 31536000;
            break;
        default:
            $expire += $timestamp;
            break;
    }
    !$lk_ckpath && $lk_ckpath = '/';
    $secure = ($_SERVER['SERVER_PORT'] == '443') ? 1 : 0;
    $ckpre = $lk_ckpre ? substr(md5($lk_ckpre), 8, 6) . '_' : '';

    if (PHP_VERSION >= '5.2.0') {
        return setcookie($ckpre . $name, $value, $expire, $lk_ckpath, $lk_ckdomain, $secure, ($httponly ? 1 : 0));
    } else {
        return setcookie($ckpre . $name, $value, $expire, '/', $lk_ckdomain, $secure);
    }
}

/**
 * 编辑器获取函数
 * @param 2维数组 $var array('id'=>'xxx','type'='simple','content'='','width'=>'','height'=>'')
 * 
 */
function getEditor($arr) {
    $uploadurl = '/commont/editorupload?immediate=1';
    $v['type'] = isset($arr['type']) ? $arr['type'] : "{tools:'FontSize,Bold,Italic,Strikethrough,Fontface,Align,Cut,Copy,Paste,Pastetext,Blocktag,Img,Source,',upImgUrl:'{$uploadurl}',upImgExt:'jpg,jpeg,gif,png'}";
    $v['content'] = isset($arr['content']) ? $arr['content'] : '';
    $v['height'] = isset($arr['height']) && is_int($v['height']) ? (int) $arr['height'] : '300';
    $v['width'] = isset($arr['width']) && is_int($v['width']) ? (int) $arr['width'] : '550';
    $v['name'] = isset($arr['name']) ? $arr['name'] : 'content';

    $editor = '<textarea class="xheditor ' . $v['type'] . '" id="' . $v['name'] . '" name="' . $v['name'] . '" style="width: ' . $v['width'] . 'px;height:' . $v['height'] . 'px;">' . $v['content'] . '</textarea>';

    return $editor;
}

/* 替换数组关键字
 * arr        2维 数组
 * rep        需要替换的关键字
 */

function getArrReplace($arr, $rep) {
    if (is_array($arr)) {
        $result = array();
        foreach ($arr as $k => $v) {
            if (isset($v[$rep])) {
                $result[$v[$rep]] = $v;
            }
        }
        return $result;
    } else {
        return $arr;
    }
}

//取得IP
function getIP() {
    $isagent = TRUE;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $currentIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $currentIP = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $currentIP = $_SERVER['REMOTE_ADDR'];
        $isagent = FALSE;
    }
    return array((preg_match('~[\d\.]{7,15}~', $currentIP, $match) ? $match[0] : 'unknow'), $isagent);
}

//写文件
function LK_writeFile($filename, $content, $mode = 'ab', $chmod = 1) {
    strpos($filename, '..') !== FALSE && exit('Access Denied!');

    $fp = @fopen($filename, $mode);
    if ($fp) {
        flock($fp, LOCK_EX);
        fwrite($fp, $content);
        fclose($fp);
        $chmod && @chmod($filename, 0666);
        return TRUE;
    }
    return FALSE;
}

//创建文件夹
function createFolder($path) {
    if (!is_dir($path)) {
    	//防止别人通过输入文件夹名称看到里面的文件列表
        createFolder(dirname($path));
        @mkdir($path);
        @chmod($path, 0777);
	    @fclose(@fopen($path . '/index.html', 'w'));
        @chmod($path . '/index.html', 0777);
    }
}

function gdEnable() {
    static $gdenable;
    if (is_bool($gdenable))
        return $gdenable;
    $m = array();
    $gdinfo = gd_info();
    preg_match('~([0-9\.]+?)\s~', $gdinfo['GD Version'], $m);
    $gdenable = ($m[1] >= '2.0.28' && function_exists('imagecreatetruecolor')) ? TRUE : FALSE;
    return $gdenable;
}

function isImg($imgpath) {
    return (strpos($imgpath, '..') !== FALSE || !file_exists($imgpath) || !in_array(Fext($imgpath), array('jpg', 'jpeg', 'bmp', 'gif', 'png')) || (function_exists('getimagesize') && !@getimagesize($imgpath))) ? false : true;
}

//文件后缀
function Fext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}

//读取文件
function LK_readFile($filename, $mode = 'rb') {
    strpos($filename, '..') !== FALSE && exit('Access Denied!');
    if ($fp = @ fopen($filename, $mode)) {
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
function thumb($img, $width, $height, $save_prefix = 'thumb_', $repath = '', $del = false) {
    if (empty($img) || !gdEnable() || !isImg($img))
        return $img;
    $imginfo = @getimagesize($img);
    switch ($imginfo[2]) {
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

    if ($repath) {
        $savepath = $repath;
    } else {
        if ($save_prefix) {
            $imgpath = substr($img, 0, strrpos($img, '/'));
            $filename = substr($img, strrpos($img, '/') + 1);
            $savepath = $imgpath . '/' . $save_prefix . $filename;
        } else {
            $savepath = $img;
        }
    }
    if (($height >= $imginfo[1] || !$height) && ($width >= $imginfo[0] || !$width)) {
        if ($save_prefix) {
            @copy($img, $savepath) || LK_writeFile($savepath, LK_readFile($img), 'wb');
            $del && LK_del($img);
        }
        return array($savepath, floor($imginfo[1]), floor($imginfo[0]));
    }
    $realscale = $imginfo[1] / $imginfo[0];
    if ($realscale <= 1) {
        $width = ($width > $imginfo[0] || !$width) ? $imginfo[0] : $width;
        $height = ($height > $imginfo[1] || !$height) ? $imginfo[1] : $width * $realscale;
    } else {
        $height = ($height > $imginfo[1] || !$height) ? $imginfo[1] : $height;
        $width = ($width > $imginfo[0] || !$width) ? $imginfo[0] : $height / $realscale;
    }
    $width = floor($width);
    $height = floor($height);
    $dst_image = imagecreatetruecolor($width, $height);
    imagecopyresampled($dst_image, $tmp_img, 0, 0, 0, 0, $width, $height, $imginfo[0], $imginfo[1]);
    switch ($imginfo[2]) {
        case '1':
            imagegif($dst_image, $savepath, 100);
            break;
        case '2':
            imagejpeg($dst_image, $savepath, 100);
            break;
        case '3':
            imagepng($dst_image, $savepath, 100);
            break;
        default :
            imagejpeg($dst_image, $savepath, 100);
            break;
    }
    $save_prefix && $del && LK_del($img);
    return array($savepath, $height, $width);
}

//文件拷贝
function LK_copy($source, $dest) {
    return @copy($source, $dest) || LK_writeFile($dest, LK_readFile($source), 'wb');
}

//文件移动
function LK_move($source, $dest) {
    if (@copy($source, $dest) || LK_writeFile($dest, LK_readFile($source), 'wb')) {
        LK_del($source);
        return true;
    }
}

//文件删除
function LK_del($var) {
    return strpos($var, '..') === FALSE && is_file($var) && @unlink($var) ? TRUE : FALSE;
}

function mb_unserialize($serial_str) {

    $str = unserialize($serial_str);
    if (is_array($str))
        return $str;

    //$serial_str = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'",$serial_str );
    $str_arr = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str);
    $str = unserialize($str_arr);
    if (is_array($str))
        return $str;

    $str_arr = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str);
    $str_arr = str_replace("\r", "", $str_arr);
    $str = unserialize($str_arr);
    if (is_array($str))
        return $str;
}

//读取分类信息
function catehtml($cates, &$table, $cid = 0, $level = 0) {
    if (!$cates)
        return false;
    foreach ($cates as $k => $v) {
        if ($v ['sub'] == $cid) {
            $ds = $cup = $link = '';
            if ($v['sub'] != 0) {
                $ds = '<i class="lower"></i>'; //<i class="lower"></i>
            } else {
                $cup = '<i onclick="cateopen(\'' . $v ['id'] . '\')" class="expand expand_a" id="bt_' . $v ['id'] . '">&nbsp;</i>'; //'';
            }
            //$ds = '<i class="lower"></i>';
            $ds = str_repeat($ds, $level);
            $checked = '';
            if ($v['isopen']) {
                $checked = 'checked';
            }

            $table .= '
					<tr class="listTr">
						<td  class="td2"><input type="checkbox" value="' . $v ['id'] . '" name="id[]"></td>
						<td  class="td2">' . ($v['isopen'] ? '<span style="color:green;">开启</span>' : '关闭') . '</td>
						<td  class="td2"><input type="text" value="' . $v ['sort'] . '" style="width:30px;text-align:center;" class="input input_wd" name="menu[' . $v ['id'] . '][sort]" /></td>
						<td  class="td2">
						' . $ds . ' ' . $cup . ' ' . $v ['name'] . '
						</td>
						<td class="td2 adminDoBoxs" align="center">';
            if ($v['issys']) {
                $table .= '<a title="编辑" href="edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>';
            } else {
                $table .= '	<a title="编辑" href="edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>
									<a onClick="return confirm(' . "'您确认要删除该信息吗?'" . ');" href="index?job=del&id[]=' . $v ['id'] . '" class="deleteBtns" title="删除">&nbsp;</a>';
            }
            $table .= '</td></tr> ';

            catehtml($cates, $table, $v ['id'], $level + 1);
        }
    }
}

//读取没有删除功能的分类信息
function catehtmlondel($cates, &$table, $cid = 0, $level = 0) {
    if (!$cates)
        return false;
    foreach ($cates as $k => $v) {
        if ($v ['sub'] == $cid) {
            $ds = $cup = $link = '';
            if ($v['sub'] != 0) {
                $ds = '<i class="lower"></i>'; //<i class="lower"></i>
            } else {
                $cup = '<i onclick="cateopen(\'' . $v ['id'] . '\')" class="expand expand_a" id="bt_' . $v ['id'] . '">&nbsp;</i>'; //'';
            }
            //$ds = '<i class="lower"></i>';
            $ds = str_repeat($ds, $level);
            $checked = '';
            if ($v['isopen']) {
                $checked = 'checked';
            }

            $table .= '
					<tr class="listTr">
						<td  class="td2"><input type="checkbox" value="' . $v ['id'] . '" name="id[]"></td>
						<td  class="td2">' . ($v['isopen'] ? '<span style="color:green;">开启</span>' : '关闭') . '</td>
						<td  class="td2"><input type="text" value="' . $v ['sort'] . '" style="width:30px;text-align:center;" class="input input_wd" name="menu[' . $v ['id'] . '][sort]" /></td>
						<td  class="td2">
						' . $ds . ' ' . $cup . ' ' . $v ['name'] . '
						</td>
						<td class="td2 adminDoBoxs" align="center">';
            if ($v['issys']) {
                $table .= '<a title="编辑" href="edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>';
            } else {
                $table .= '	<a title="编辑" href="edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>
									';
            }
            $table .= '</td></tr> ';

            catehtml($cates, $table, $v ['id'], $level + 1);
        }
    }
}

//读取分类信息
function menuhtml($cates, &$table, $cid = 0, $level = 0) {
    if (!$cates)
        return false;
    foreach ($cates as $k => $v) {
        if ($v ['sub'] == $cid) {
            $ds = $cup = $link = '';
            if ($v['sub'] != 0) {
                $ds = '<i class="lower"></i>'; //<i class="lower"></i>
            } else {
                $cup = '<i onclick="cateopen(\'' . $v ['id'] . '\')" class="expand expand_a" id="bt_' . $v ['id'] . '">&nbsp;</i>'; //'';
            }
            //$ds = '<i class="lower"></i>';
            $ds = str_repeat($ds, $level);
            $checked = '';
            if ($v['isopen']) {
                $checked = 'checked';
            }

            $table .= '
					<tr class="listTr">
						<td class="td2"><input type="checkbox" value="' . $v ['id'] . '" name="id[]"></td>
						<td class="td2">' . ($v['isopen'] ? '<span style="color:green;">开启</span>' : '关闭') . '</td>
						<td class="td2"><input type="text" value="' . $v ['sort'] . '" style="width:30px;text-align:center;" class="input input_wd" name="menu[' . $v ['id'] . '][sort]" /></td>
						<td class="td2">
						' . $ds . ' ' . $cup . ' ' . $v ['name'] . '
						</td>
						<td class="td2">
							<span>' . $v['topkey'] . '</span>
						</td>
						<td class="td2">
							<span>' . $v['mkey'] . '</span>
						</td>
						<td class="td2 adminDoBoxs" align="left">';
            if ($v['issys']) {
                $table .= '<a title="编辑" href=edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>';
            } else {
                $table .= '	<a title="编辑" href="edit?id=' . $v ['id'] . '" class="editBtns">&nbsp;</a>
									<a onClick="return confirm(' . "'您确认要删除该信息吗?'" . ');" href="index?job=del&id[]=' . $v ['id'] . '" class="deleteBtns" title="删除">&nbsp;</a>';
            }
            $table .= '</td></tr> ';

            menuhtml($cates, $table, $v ['id'], $level + 1);
        }
    }
}

// 新闻分类列表
function catelist($cateList, &$htmlcode, $id, $cid = 0, $level = 0) {
    foreach ($cateList as $k => $v) {
        if ($v['sub'] == $cid) {
            $ds = '';
            if ($v['sub'] != 0) {
                $ds = '---';
            }
            $ds = str_repeat($ds, $level);

            $htmlcode .= '<option value="' . $v['id'] . '" ';
            if ($id == $v['id'])
                $htmlcode .= 'selected="selected"';
            $htmlcode .= '>' . $ds . $v['name'] . '</option>';

            catelist($cateList, $htmlcode, $id, $v['id'], $level + 1);
        }
    }
}

function findChild(&$arr, $id) {
    $childs = array();
    foreach ($arr as $k => $v) {
        if ($v['sub'] == $id) {
            $childs[] = $v;
        }
    }
    return $childs;
}

function menuListArr($rows, $id) {
    // global $rows;
    $childs = findChild($rows, $id);
    if (empty($childs)) {
        return null;
    }
    foreach ($childs as $k => $v) {
        $rescurTree = menuListArr($rows, $v['id']);
        if (null != $rescurTree) {
            $childs[$k]['child'] = $rescurTree;
        }
    }
    return $childs;
}

// 新闻分类列表
function bscatelist($cateList, $id) {
    $htmlcode = '';
    foreach ($cateList as $k => $v) {
        $htmlcode .= '<option value="' . $v['id'] . '" ';
        if ($id == $v['id'])
            $htmlcode .= 'selected';
        $htmlcode .= '>' . $v['name'] . '</option>';
        foreach ($cateList as $key => $val) {
            if ($val['sub'] == $v['id']) {
                $htmlcode .= '<option value="' . $val['id'] . '" ';
                if ($id == $val['id'])
                    $htmlcode .= 'selected';
                $htmlcode .= '> ┣  ' . $val['name'] . '</option>';
            }
        }
    }
    return $htmlcode;
}

//字符编码
function LKEncode($txt, $key) {
    srand((double) microtime() * 1000000);
    $encrypt_key = md5(rand(0, 32000));
    $ctr = 0;
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
    }
    return base64_encode(PKey($tmp, $key));
}

//字符解码
function LKDecode($txt, $key) {
    $txt = PKey(base64_decode($txt), $key);
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        $md5 = $txt[$i];
        $tmp .= $txt[++$i] ^ $md5;
    }
    return $tmp;
}

//加密字符
function PKey($txt, $encrypt_key) {
    $encrypt_key = md5($encrypt_key);
    $ctr = 0;
    $tmp = '';
    for ($i = 0; $i < strlen($txt); $i++) {
        $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
        $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
    }
    return $tmp;
}

//随机码
function random($length, $isNum = FALSE) {//发送短信时可能生成2个随机数
    $random = '';
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $num = '0123456789';
    if ($isNum) {
        $sequece = 'num';
    } else {
        $sequece = 'str';
    }
    $max = strlen($$sequece) - 1;
    for ($i = 0; $i < $length; $i++) {
        $random .= ${$sequece}{mt_rand(0, $max)};
    }
    return $random;
}

function getColumn($table, $parent_column = 'sub', $parent_id = 0, $t = -1) {
    $t++;
    global $temp;
    $sql = "SELECT * FROM `$table` WHERE `" . $parent_column . "`=" . $parent_id . " ORDER BY sort";
    $query = mysql_query($sql);
    $data = array();
    if ($query) {
        while ($res = mysql_fetch_assoc($query))
            $data[] = $res; //循环出父级分类
    }
    if (!empty($data)) {
        $last_one = count($data) - 1;
        foreach ($data as $key => $val) {
            if ($key != $last_one)
                $val['new_name'] = str_repeat('&nbsp;', $t * 2) . '---' . $val['name'];
            else
                $val['new_name'] = str_repeat('&nbsp;', $t * 2) . '---' . $val['name'];
            $temp[] = $val;
            getColumn($table, $parent_column, $val['id'], $t);
        }
    }
    return $temp;
}

function object_array($array) {
    if (is_object($array)) {
        $array = (array) $array;
    } if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

function mohuStr($str, $len = 3) {
    $str_1 = substr($str, 0, $len);
    $str_2 = substr($str, -$len);
    return $str_1 . '***' . $str_2;
}

function export_csv($filename, $data) {
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=" . $filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    return $data;
}

function isnull($str) {
    return $str ? strip_tags($str) : '';
}

//分页
function page($info, $num) {
    if ($info) {
        $page = (int) $info;
    } else {
        $page = $num;
    }
    return (int) $page;
}

//获取中文字的首字母
function getfirstchar($s0) {
    $fchar = ord($s0{0});
    if ($fchar >= ord("A") and $fchar <= ord("z"))
        return strtoupper($s0{0});
    //$s = iconv('UTF-8', 'GB2312//IGNORE', $s0);
    $s = iconv("UTF-8", "GBK", $s0);
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 and $asc <= -20284)
        return "A";
    if ($asc >= -20283 and $asc <= -19776)
        return "B";
    if ($asc >= -19775 and $asc <= -19219)
        return "C";
    if ($asc >= -19218 and $asc <= -18711)
        return "D";
    if ($asc >= -18710 and $asc <= -18527)
        return "E";
    if ($asc >= -18526 and $asc <= -18240)
        return "F";
    if ($asc >= -18239 and $asc <= -17923)
        return "G";
    if ($asc >= -17922 and $asc <= -17418)
        return "I";
    if ($asc >= -17417 and $asc <= -16475)
        return "J";
    if ($asc >= -16474 and $asc <= -16213)
        return "K";
    if ($asc >= -16212 and $asc <= -15641)
        return "L";
    if ($asc >= -15640 and $asc <= -15166)
        return "M";
    if ($asc >= -15165 and $asc <= -14923)
        return "N";
    if ($asc >= -14922 and $asc <= -14915)
        return "O";
    if ($asc >= -14914 and $asc <= -14631)
        return "P";
    if ($asc >= -14630 and $asc <= -14150)
        return "Q";
    if ($asc >= -14149 and $asc <= -14091)
        return "R";
    if ($asc >= -14090 and $asc <= -13319)
        return "S";
    if ($asc >= -13318 and $asc <= -12839)
        return "T";
    if ($asc >= -12838 and $asc <= -12557)
        return "W";
    if ($asc >= -12556 and $asc <= -11848)
        return "X";
    if ($asc >= -11847 and $asc <= -11056)
        return "Y";
    if ($asc >= -11055 and $asc <= -10247)
        return "Z";
    return substr($s0, 0, 1);
}

//-----------------邹慧刚自定义函数zhg

//汉字或数字转换成便宜，全部的22==>erer，我的=>wode

function TopinyinAll($str){
	
	   	$CI = &get_instance();
        $CI->load->helper('topinyin');

                //				替换掉数字
              	$patterns = array();
				$patterns[0] = '/0/';
				$patterns[1] = '/1/';
				$patterns[2] = '/2/';
				$patterns[3] = '/3/';
				$patterns[4] = '/4/';
				$patterns[5] = '/5/';
				$patterns[6] = '/6/';
				$patterns[7] = '/7/';
				$patterns[8] = '/8/';
				$patterns[9] = '/9/';
				$replacements = array();
				$replacements[9] = 'ling';
				$replacements[8] = 'yi';
				$replacements[7] = 'er';
				$replacements[6] = 'san';
				$replacements[5] = 'si';
				$replacements[4] = 'wu';
				$replacements[3] = 'liu';
				$replacements[2] = 'qi';
				$replacements[1] = 'ba';
				$replacements[0] = 'jiu';
				
				$str=preg_replace($patterns, $replacements, $str);
				
				
    			$strpy=pinyin::utf8_to($str);
    			if(!$str){
    				$str='匿名用户';
    				$strpy='nimingyonghu';//如果不存在就保存weidy
    			}
				
				return $strpy;

    		
}


 /*生成敏感词库Generation of sensitive words*/
  function Generationwords(){
   	$CI = &get_instance();
    $CI->load->helper('SimpleDict_helper.php');
	$text_file_path='text_file_path.txt';
	$output_dict_path='output_dict_path.txt';
	SimpleDict::make($text_file_path, $output_dict_path);
 }
 /*屏蔽敏感词Mask sensitive words*/
  function Maskedwords($text){
  	$CI = &get_instance();
    $CI->load->helper('SimpleDict_helper.php');
 	$output_dict_path='output_dict_path.txt';
	$dict = new SimpleDict($output_dict_path);
   //$result = $dict->search("aa nn here...");
   // 简单替换
    $replaced = $dict->replace($text, "**");
	return $replaced;
//   // 高级替换
//   $replaced = $dict->replace("some text here...", function($word, $value) {
//   return "[$word -> $value]";
//    });

  }
  
  /*生成自动分类*/
  function buildautoclass(){
  	$CI = &get_instance();
    $CI->load->helper('SimpleDict_helper.php');
	$text_file_path='input_autoclass_path.txt';
	$output_dict_path='output_autoclass_path.txt';
	SimpleDict::make($text_file_path, $output_dict_path);
  }
  /*自动分类*/
 function autoclass($text){
		
  	$CI = &get_instance();
    $CI->load->helper('SimpleDict_helper.php');
 	$output_dict_path='output_autoclass_path.txt';
	$dict = new SimpleDict($output_dict_path);
    //$result = $dict->search("aa nn here...");
   // 简单替换
   // $replaced = $dict->replace($text, "**");
	//return $replaced;
     // 高级替换
     //$text="Conference discussion: Does a Exosomal Exosomal Conference the number of Conference Exosomal performed and the learning curve for thoracoscopic resection of thymoma in patients with myasthenia gravis?.";
     return $result = $dict->search($text);
//	 $replaced = $dict->replace($text, function($word, $value) {
//   return "[$word -> $value]";
//  // return $value;
//    });
//	  
//	  return $replaced;

  
	}
 
 /*转换为swf格式*/
 function conversionswf($docpath,$fileName,$typesIf){//源文件所在目录，源文件名称，源文件格式类型。例如upload/file/123.ppt下的文件参数这样表示：/upload/file/,123.ppt,ppt
 	
 	    $docpath=$docpath?$docpath:'upload/files/doc/';
		//$docpath ='upload/files/doc/';
		$pdfpath = 'upload/files/pdf/';
		$swfpath = 'upload/files/swf/';
	
			//执行转换
		if($typesIf=='pdf'){ //PDF 转SWF
				$pdf = $fileName;
				//$swf = str_replace('pdf','swf',$pdf);
				$swfdir = WEBROOTFILE.$swfpath.str_replace('.pdf','',$pdf).'/';
				createFolder($swfdir);
				///$swf = str_replace('.pdf','page%.swf',$pdf);
				$swf='page%.swf';
				//exec('/usr/local/wenku/swftools/bin/pdf2swf -o '.$swfpath.$swf.' -T -z -t -f '.$pdfpath.$pdf.' -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -s flashversion=9');
				//              pdf2swf -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -T 9 -s poly2bitmap -s zoom=150 -s flashversion=9 "/tmp/1.pdf" -o "/tmp/%.swf"
				$swfcommand = '	pdf2swf -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -T 9 -s poly2bitmap -s zoom=150 -s flashversion=9 '.WEBROOTFILE.$pdfpath.$pdf.' -o '.$swfdir.$swf;
			    exec($swfcommand);
				$path2 = $pdfpath.$pdf;
				$path3 = $swfpath.$swf;
		}else{ //DOC 转 PDF,再转换成swf
				$doc = $fileName;
				$format = explode('.',$fileName);
				$formatName = $format[0].'.pdf';
				//java -jar /opt/openoffice4/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar /tmp/1.pptx  /tmp/1.pdf
				$command = 'java -jar /opt/openoffice4/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar '.WEBROOTFILE.$docpath.$doc.' '.WEBROOTFILE.$pdfpath.$formatName;
	            exec($command);
				$path1 = $docpath.$doc;
				$path2 = $pdfpath.$formatName;
		
				if(file_exists( $pdfpath.$formatName)){
				$pdf = $formatName;
				$swfdir = WEBROOTFILE.$swfpath.str_replace('.pdf','',$pdf).'/';
				createFolder($swfdir);
				//$swf = str_replace('.pdf','page%.swf',$pdf);
				$swf='page%.swf';
				//$swfcommand="/usr/swftools/bin/pdf2swf -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -T 9 -s poly2bitmap -s zoom=150 -s flashversion=9 /var/www/html/upload/files/pdf/b2fce2516ace329c37540c9a0d15ce23.pdf -o /var/www/html/upload/files/swf/b2fce2516ace329c37540c9a0d15ce23/page%.swf";
                $swfcommand = "/usr/swftools/bin/pdf2swf -s languagedir=/usr/share/xpdf/xpdf-chinese-simplified -T 9 -s poly2bitmap -s zoom=150 -s flashversion=9 ".WEBROOTFILE.$pdfpath.$pdf." -o ".$swfdir.$swf;
				
				exec($swfcommand);
				//system($swfcommand);
				$path3 = $swfpath.$swf;
				}
		
		}

     return $swfpath.str_replace('.pdf','',$pdf).'/';
 	
 }

function getFileNum($dirname){
	
	//递归函数实现遍历指定文件下的目录与文件数量
$dirnum=0;
$filenum=0;
  $dir=opendir($dirname);
  while($filename=readdir($dir)){
    //要判断的是$dirname下的路径是否是目录
    $newfile=$dirname."/".$filename;
	
    //is_dir()函数判断的是当前脚本的路径是不是目录
    if(is_dir($newfile)){
    	
      //通过递归函数再遍历其子目录下的目录或文件
     // getFileNum($newfile,$dirnum,$filenum);
    //  $dirnum++;
    }else{
    	//echo $newfile;
      $filenum++;
    }
  }
  closedir($dir);

  return $filenum;

}

function getfileName(){
	$dir = WEBROOTFILE;  //要获取的目录

	echo "********** 获取目录下所有文件和文件夹 ***********<hr/>";

	//先判断指定的路径是不是一个文件夹

	if (is_dir($dir)){

		if ($dh = opendir($dir)){

			while (($file = readdir($dh))!= false){

				//文件名的全路径 包含文件名

				$filePath = $dir.$file;

				//获取文件修改时间

				$fmt = filemtime($filePath);

				echo "<span style='color:#666'>(".date("Y-m-d H:i:s",$fmt).")</span> ".$filePath."<br/>";

			}

			closedir($dh);

		}

	}

}

 function is_mobile_request()
	{
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
		$mobile_browser = '0';
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
	   $mobile_browser++;
		if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))
		$mobile_browser++;
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']))
	    $mobile_browser++;
		if(isset($_SERVER['HTTP_PROFILE']))
	    $mobile_browser++;
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
		$mobile_agents = array(
				'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
				'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
				'wapr','webc','winw','winw','xda','xda-'
		);
		if(in_array($mobile_ua, $mobile_agents))
        	$mobile_browser++;
		if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
	        $mobile_browser++;
		// Pre-final check to reset everything if the user is on Windows
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
			$mobile_browser=0;
		// But WP7 is also Windows, with a slightly different characteristic
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
	        $mobile_browser++;
		if($mobile_browser>0)
		return true;
		else
		return false;
	}

//----------------------------zhg结束
