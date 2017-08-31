<?php

class M_app extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('mysql', '', TRUE);
        $this->load->model('publics', '', TRUE);
        $this->load->model('commonmodel', '', TRUE);
        $this->table = 'areas';
    }

    public function getBasicInfo() {
        $row = $this->commonmodel->getInfo('configs', 'id>1 ');
        return $row;
    }

    //检测Token
    public function chkToken($uid, $token) {
        if (!$uid || !$token) {
            $msg['status'] = 502;
            $msg['info'] = '您尚未登录';
            $msg['data'] = (object) array();
            $this->publics->return_json($msg);
        }
		$thisToken = $this->commonmodel->getInfo('user_token', "uid={$uid} and token = '{$token}' ");
	
        if (!$thisToken) {
            $msg['status'] = 502;
            $msg['info'] = '您尚未登录';
            $msg['data'] = (object) array();
            $this->publics->return_json($msg);
        } else {
            if ($thisToken['expiretime'] > date('Y-m-d H:i:s')) {
                if ($thisToken['token'] != $token) {
                    $msg['status'] = 502;
                    $msg['info'] = '您的账户已在其他终端登录';
                    $msg['data'] = (object) array();
                    $this->publics->return_json($msg);
                } else {
                    return true;
                }
            } else {
                $msg['status'] = 502;
                $msg['info'] = '登录已过期';
                $msg['data'] = (object) array();
                $this->publics->return_json($msg);
            }
        }
    }

    //添加Token
    public function addToken($arr) {

        $data = array();
        $data['uid'] = $arr['uid'];
        $data['token'] = $arr['token'];
        $data['lasttime'] = $arr['last_time'];
        $data['expiretime'] = $arr['expire_time'];
        $insertID = $this->commonmodel->getUpdate('user_token', '', $data);
        if ($insertID) {
            return $insertID;
        } else {
            return false;
        }
    }

    //更新Token
    public function updateToken($arr, $uid) {
        if (!(int) $uid) {
            return false;
        }
        $data = array();
        $data['token'] = $arr['token'];
        $data['lasttime'] = $arr['last_time'];
        $data['expiretime'] = $arr['expire_time'];

        $insertID = $this->commonmodel->getUpdate('user_token', "uid = {$uid}", $data);
        if ($insertID) {
            return true;
        } else {
            return false;
        }
    }

    //获取Token
    public function getToken($uid, $token = '') {
        if (!$uid) {
            return false;
        }
        if ($token) {
            $result_arr = $this->mysql->getOneSqlValue("SELECT * FROM user_token WHERE uid={$uid} and token = {$token}");
        } else {
            $result_arr = $this->mysql->getOneSqlValue("SELECT * FROM user_token WHERE uid={$uid}");
        }
        return $result_arr;
    }

    //删除Token记录
    public function delToken($uid) {
        $this->mysql->delTable('user_token', array('uid' => $uid));
        $msg['status'] = 200;
        $msg['info'] = '账户已安全退出！';
        $msg['data'] = (object) array();
        $this->pulbics->return_json($this->msg);
    }

    public function goodsInfo($id, $type = '') {
        $goods = array();
        if ($type == 'fruit' || $type == 'equipment' || $type == 'lease') {
            if ($type == 'fruit') {
                $table = 'store_fruit';
            } elseif ($type == 'equipment') {
                $table = 'store_equipment';
            } elseif ($type == 'lease') {
                $table = 'store_lease';
            }
            
            $goods = $this->commonmodel->getInfo($table, "isopen=1 and id = {$id}");
            $goods['type'] = $type;
        }
        return $goods;
    }

}
