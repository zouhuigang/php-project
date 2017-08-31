<?php

class M_wap extends CI_Model {

    public function getAdvert($id) {
        $rs = $this->lk_common->getTableValue('picture', 'sub=' . $id, '*', 'sort ASC');
        return $rs;
    }

    public function getBasicInfo() {
        $this->load->model('admin/lk_configs'); //取文件控制
        $row = $this->lk_configs->getOneTableValue();
        return $row;
    }

    public function getRightOther() {
        $data = array();
        $data['nav'] = 'welcome';
        $data['lg_userinfo'] = isLogin();
        return $data;
    }

    public function getRightGoods($limit = 8) {
        $this->load->model('admin/lk_goods');
        $data = array();
        $arr['isopen'] = 1;
        $arr['is_mall'] = 1;
        $data['thisList'] = $this->lk_goods->getTableValue($arr, 'id,name,pic,mktprice,price,is_rec,is_new,is_hot,is_mall,is_free,is_give,is_team,is_spec', 'sort ASC', $limit);
        return $data;
    }

    public function getUserInfo() {
        $user = $this->session->userdata('user');
        $row['user'] = $this->lk_common->getOneTableValue('user', array('id' => $user['id']));
        $row['basic'] = $this->lk_common->getOneTableValue('user_basic', array('uid' => $user['id']));
        $row['count'] = $this->lk_common->getOneTableValue('user_count', array('uid' => $user['id']));
        return $row;
    }

    public function getAuctionCate($sid = 0) {
        $this->load->model('admin/lk_auction', '', TRUE); //取文件控制
        $topCate = $this->lk_auction->getTableGoodscateValue(array('sub' => $sid), '*', 'sort ASC');
        foreach ($topCate as $k => $v) {
            $thisSubcate = $this->lk_auction->getTableGoodscateValue(array('sub' => $v['id']), '*', 'sort ASC');
            if ($thisSubcate) {
                foreach ($thisSubcate as $key => $val) {
                    $thisSubcate[$key]['child'] = $this->lk_auction->getTableGoodscateValue(array('sub' => $val['id']), '*', 'sort ASC');
                }
            }
            $topCate[$k]['child'] = $thisSubcate;
        }
        return $topCate;
    }

    public function getHeaderOther() {
        $data = array();
        $tmp = $this->getBasicInfo();
        $data['lk_title'] = $tmp['lk_title'];
        $data['lk_sitename'] = $tmp['lk_sitename'];
        $data['lk_keywords'] = $tmp['lk_keywords'];
        $data['lk_description'] = $tmp['lk_description'];
        $data['lk_rulecontent'] = $tmp['lk_rulecontent'];
        $data['lg_userinfo'] = isLogin();
        return $data;
    }

    public function getTopOther() {
        $data = array();
        $data['redirecturl'] = getCurrentURL();
        $data['lg_userinfo'] = isLogin();
        return $data;
    }

    public function getFooterOther() {
        $tmp = $this->getBasicInfo();
        $data = array();
        $data['lk_footer'] = $tmp['lk_footer'];
        $data['lg_userinfo'] = isLogin();
        return $data;
    }

    public function setHistory($uid, $gid) {
        if (!$gid) {
            return false;
        }
        if (!$uid) {
            $cookie = gCookie('vHistory');
            $vHhistory = $cookie ? unserialize(urldecode($cookie)) : array();
            if (is_array($vHhistory)) {
                if (!in_array($gid, $vHhistory)) {
                    array_push($vHhistory, $gid);
                    if (count($vHhistory) > 20) {
                        array_shift($vHhistory);
                    }
                }
            }
            sCookie('vHistory', urlencode(serialize($vHhistory)), 86400);
        } else {
            $thisInfo = $this->lk_common->getOneTableValue('user_history', array('uid' => $uid, 'gid' => $gid));
            if ($thisInfo) {
                $arr = array('createtime' => time());
                $this->lk_common->updateTableValue('user_history', $arr, array('uid' => $uid, 'gid' => $gid));
            } else {
                $arr = array('gid' => $gid, 'uid' => $uid, 'createtime' => time());
                $this->lk_common->updateTableValue('user_history', $arr);
            }
        }
    }

    public function getHistory($uid) {
        if (!$uid) {
            if (gCookie('vHistory')) {
                $viewhistory = unserialize(urldecode(gCookie('vHistory')));
                if (isset($viewhistory)) {
                    krsort($viewhistory);
                    $goods_id = array_slice($viewhistory, 0, count($viewhistory));
                    foreach ($goods_id as $k => $val) {
                        if ($val == '') {
                            unset($goods_id[$k]);
                        }
                    }
                    if ($goods_id) {
                        return $goods_id;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $goodIds = $this->lk_common->getTableValue('user_history', array('uid' => $uid), '*', 'createtime DESC', 10);
            $str = array();
            if ($goodIds) {
                foreach ($goodIds as $k => $v) {
                    $str[] = $v['gid'];
                }
                if ($str) {
                    return $str;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function SendEmes($email, $title, $con) {
        $this->load->library('email'); //加载CI的email类
        $basicinfo = $this->getBasicInfo();

        $mail_setting = unserialize($basicinfo['lk_smtpmail']);

        //以下设置Email参数
        $config_mail = array();
        $config_mail['protocol'] = 'smtp';
        $config_mail['smtp_host'] = $mail_setting['lk_smtphost'];
        $config_mail['smtp_user'] = $mail_setting['lk_smtpuser'];
        $config_mail['smtp_pass'] = $mail_setting['lk_smtppw'];
        $config_mail['smtp_port'] = (int) $mail_setting['lk_smtpport'];
        $config_mail['charset'] = 'utf-8';
        $config_mail['wordwrap'] = TRUE;
        $config_mail['mailtype'] = 'html';
        $config_mail['newline'] = "\r\n";
        $config_mail['crlf'] = "\r\n";
        $this->email->initialize($config_mail);

        //以下设置Email内容
        $this->email->from($mail_setting['lk_smtpuser'], '紫锦城珠宝商城');
        $this->email->to($email);
        $this->email->subject($title);
        $this->email->message($con);

        if (!$this->email->send()) {
            return false;
        } else {
            return true;
        }
    }

    public function SendMmes($mobile, $con) {
        $this->load->library('sms');
		//$result = $this->sms->send_sms_yunpian($mobile, $con);
		$result = $this->sms->sendSms($mobile, $con);
        if (!$result) {
        return false;
        } else {
        return true;
        }
    }

}
