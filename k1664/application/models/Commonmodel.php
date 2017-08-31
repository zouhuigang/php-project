<?php

/**
* 后台基本模块
*/
class commonModel extends CI_Model{

	//初始化数据
	public function __construct(){
		parent::__construct();
		$this->load->database();

		$this->load->model('mysql', '', TRUE);
	}

	//列表--带分页
	public function getTablePage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $links = '?', $pageSize = '20'){
		$list = $this->mysql->_getPage($table, $w_str, $filed, $w_order, $p, $links, $pageSize);
		return $list;
	}

    //ajax分页$fcun函数名称,$para函数的第二个参数
    public function getTablePageAjax($table,$w_str = '', $filed = '*', $w_order = '', $p = '1', $fcun, $para = '', $pageSize = '20') {

        if (!$fcun)
            return false;

        $rs = $this->m_sql->_getAjaxPage($table, $w_str, $filed, $w_order, $p, $fcun, $para, $pageSize);
        return $rs;
    }


    //读取信息详情
    public function getInfo($tables, $wstr, $fileds='*') {
		//        echo "SELECT {$fileds} FROM {$tables} WHERE {$wstr}"."</br>";
		
        $info = '';
		$info = $this->mysql->getOneSqlValue("SELECT {$fileds} FROM {$tables} WHERE {$wstr}");
	
        if (!$info) {
            return false;
        } else {
            return $info;
        }
	}

	   //取一条数据
    public function getOneTableValue($table, $data, $filed = '*', $order = '') {
        $this->tableName = $this->prefix . $table;
        $rs = '';
        if ($data) {
            $rs = $this->m_sql->getOneTableInfo($this->tableName, $data, $filed, $order);
        }
        return $rs;
	}




    //获取所有记录
    public function getList($table, $w_str, $fileds = '*') {
		//       echo "SELECT {$fileds} FROM " . $table . " WHERE {$w_str} "."</br>";

        $rs = '';
		$rs = $this->mysql->getSqlValue("SELECT {$fileds} FROM " . $table . " WHERE {$w_str} ");
		
        return $rs;
    }



	//求和
	public function getSum($table, $w_str, $fileds = '*'){
		$rs = '';
		$rs = $this->mysql->getOneSqlValue("SELECT {$fileds} FROM " . $table . " WHERE {$w_str} ");
		return $rs;
	}

/*并列排行
 * select a.*,count(b.id)+1 as rank from activity_jd_total_ago a left join activity_jd_total_ago b on a.totalrice < b.totalrice group by a.id order by rank asc;
 * 
 * 全部:
 *
select a.*,count(b.id)+1 as rank from (
SELECT id,uid,types,totalrice,totalsteps FROM `activity_jd_total_today` where 1=1 and types=0 GROUP BY uid ORDER BY totalrice DESC,totalsteps DESC LIMIT 0,10
) a 
left join (
SELECT id,uid,types,totalrice,totalsteps FROM `activity_jd_total_today` where 1=1 and types=0 GROUP BY uid ORDER BY totalrice DESC,totalsteps DESC LIMIT 0,10
) b on a.totalrice < b.totalrice group by a.id order by rank asc,totalsteps desc; 
 * 
 * $singlefiled 单个字段 排名
 * */
public function TiedRank($table,$singlefiled,$limit='0', $pagesize = '20',$orderby='id asc',$w_str='1=1'){
	    
	    $sql="select a.*,count(b.id)+1 as rank from ({$table}) a left join ({$table}) b on a.{$singlefiled} < b.{$singlefiled} where {$w_str} group by a.id order by rank asc,$orderby LIMIT $limit,$pagesize";
	    $rs = '';
        $rs = $this->mysql->getSqlValue($sql);
        return $rs;
}
//select t.* from ( select (@rowNum:=@rowNum+1) rank ,uid,sumsteps,ctime  from activity_user_datas,(Select (@rowNum :=0) ) b )t where t.uid=11220 单个排名
//SELECT temp.pm FROM (SELECT @rownum:=@rownum+1 pm,`user`.* FROM (SELECT @rownum:=0) a, `user` ORDER BY `number`,`id`) temp WHERE temp.id = 3

//查询单个用户的排名
//public function sigleRank(){
//	//SELECT temp.pm FROM (SELECT @rownum:=@rownum+1 pm,`user`.* FROM (SELECT @rownum:=0) a, `user` ORDER BY `number`,`id`) temp WHERE temp.id = 3
//}

	//添加、编辑数据
	public function getUpdate($table, $w_str = array(), $data){
		$rs = '';
		if($w_str){
			$rs = $this->mysql->getUpdateTable($table, $w_str, $data);
		} else{
			$rs = $this->mysql->getAddTable($table, $data);
		}
		return $rs;
	}
	
	//大批量修改数据,需要先建立唯一索引:唯一的索引意味着两个行不能拥有相同的索引值
	//replace into 跟 insert 功能类似，不同点在于：replace into 首先尝试插入数据到表中， 
	//1. 如果发现表中已经有此行数据（根据主键或者唯一索引判断）则先删除此行数据，然后插入新的数据。 
	//2. 否则，直接插入新数据。
	//要注意的是：插入数据的表必须有主键或者是唯一索引！否则的话，replace into 会直接插入数据，这将导致表中出现重复的数据。
	//创建索引
	//replace into activity_classify_totalsteps_ago (cateid,totalsteps,ctime) values (1,130,'1452591738'),(2,1002,'1452591738');
	public function BigReplaceInto($table,$multifield,$multivalue){
		$sql="REPLACE INTO {$table} ({$multifield}) VALUES  $multivalue";
		$rs=$this->db->query($sql);
		return $rs;//返回受影响的行数目
	}

	//删除数据
	public function getDel($table, $Arr){
		$rs = '';
		$rs = $this->mysql->delTable($table, $Arr);
		return $rs;
	}

	// 根据查询条件统计
	public function getCount($table, $where = null, $groupby='') {
		$this->db->from($table);
		if ($where){
			$this->db->where($where);
		}
		if($groupby){
			$this->db->group_by($groupby);			
		}
		return $this->db->count_all_results();
	}
	//循环插入多条数据
	//INSERT INTO notice(uid, content) VALUES (1, '姚明'), (2, '比尔.盖茨'), (3, '火星人'); 
	public function InsertMultiple($table, $data){
		
            $sql = "INSERT INTO {$table} VALUES {$data}";
       
           return $query = $this->db->query($sql);
    
	}
	
	//删除多个字段重复的数据，保留id最小的值
	public function del_copy_stay_mix_id($table,$field){
		
		//select min(id) from user_qq group by uid,type having count(id)>1
		$wstr = " group by {$field} having count(id)>1";
		$get_min_id=$this->getInfo($table, $wstr,"min(id) as id ");
		$get_info=$this->getInfo($table, "id={$get_min_id['id']}",$field);
//		foreach(){
//			
//		$sql_del_other="delete from user_qq where ask_id=xxx and cate=xxx and id<>117";
//		}
//		
		
	}









}
