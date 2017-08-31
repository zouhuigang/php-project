<?php

/**
 * 远程数据库
 * https://leancloud.cn
 * https://leancloud.cn/docs/leanstorage_guide-php.html#%E6%9F%A5%E8%AF%A2%E7%BB%93%E6%9E%9C%E6%95%B0%E9%87%8F%E5%92%8C%E6%8E%92%E5%BA%8F

    不支持在 select 中使用 as 关键字为列增加别名。
    update 和 delete 不提供批量更新和删除，只能根据 objectId（where objectId=xxx）和其他条件来更新或者删除某个文档。
    不支持 join，关联查询提供 include、relatedTo 等语法来替代（关系查询）。
    仅支持部分 SQL 函数（内置函数）。
    不支持 group by、having、max、min、sum、distinct 等分组聚合查询语法。
    不支持事务。
    不支持锁。
 
 
 */
use \LeanCloud\Client;
use \LeanCloud\Object;
use \LeanCloud\Query;
class LeanDb extends CI_Model {

    //初始化数据
    public function __construct() {
        parent::__construct();
		// 参数依次为 AppId, AppKey, MasterKey
		Client::initialize("MEPjAFvYiOuqm4ji2duqPsMJ-gzGzoHsz", "giIdvt6HNbJ7tsGR3C0uHtGU", "fUQuzyIAkADsqmyLVd7gkLcn");
		// 启用中国节点（默认启用）
		Client::useRegion("CN");
    }

    //列表--带分页
    public function getTablePage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $links = '?', $pageSize = '20') {
        //$list = $this->mysql->_getPage($table, $w_str, $filed, $w_order, $p, $links, $pageSize);
        return $list;
    }

    //读取信息详情
    public function getInfo($tables, $wstr, $fileds='*') {
//        echo "SELECT {$fileds} FROM {$tables} WHERE {$wstr}";exit;
        $info = array();
		$infos = Query::doCloudQuery("SELECT {$fileds} FROM {$tables} WHERE {$wstr} limit 2");
/*$query = new Query($tables);
$query->contains("title","工程师周会");
$infos = $query->find();

forEach($infos as $k=>$v) {
	print_r($k);

	print_r($v->get("title"));
}*/


		forEach($infos["results"] as $k=>$v) {
			//print_r($k);
		print_r($v->get("title"));


}
		


		
        if (!$info) {
            return false;
        } else {
            return $info;
        }
    }

    //获取所有记录
    public function getList($table, $w_str, $fileds = '*') {
        $rs = '';
        //$rs = $this->mysql->getSqlValue("SELECT {$fileds} FROM " . $table . " WHERE {$w_str} ");
        return $rs;
    }

    //求和
    public function getSum($table, $w_str, $fileds = '*') {
        $rs = '';
        //$rs = $this->mysql->getOneSqlValue("SELECT {$fileds} FROM " . $table . " WHERE {$w_str} ");
        return $rs;
    }

    //添加、编辑数据
    public function getUpdate($table, $w_str = array(), $data ) {
        $rs = '';
        if ($w_str) {
            //$rs = $this->mysql->getUpdateTable($table, $w_str, $data);
        } else {
            //$rs = $this->mysql->getAddTable($table, $data);
        }
        return $rs;
    }

    //删除数据
    public function getDel($table, $Arr) {
        $rs = '';
        //$rs = $this->mysql->delTable($table, $Arr);
        return $rs;
    }

    // 根据查询条件统计
	public function getCount($table, $where = null) {
		$_sql="select count(*) from {$table}";
		if($where){
			$_sql.=$where;
		}
	    $result = Query::doCloudQuery($_sql);
        return $result["count"];
    }

}
