<?php

class M_sql extends CI_Model {

    private $_sql;
    private $is_memcached = false;

    public function __construct() {
        parent::__construct();
        //导入ci数据库类
        $this->load->database();
        //设置缓存类型
        $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        //开启memcached缓存
        //$this->is_memcached = $this->cache->memcached->is_supported();
    }

    /** 设置memcached key 值
     * 去除格式的键值
     */
    function _clearstyle($table, $str = '', $filed = '*', $order = '', $limit = '', $offset = '') {
        $key = $table . "#";
        if ($str) {
            if (is_array($str)) {
                foreach ($str as $k => $v) {
                    $key .= $k . $v;
                }
                $key .= "#";
            } else {
                $key .= $str . "#";
            }
        }
        /*
          if($filed!='*'){
          if(is_array($filed)){
          foreach($filed as $k=>$v){
          $key .= $k.$v;
          }
          $key .= "#";
          }else{
          $key .= $filed."#";
          }
          }
         */
        if ($order) {
            if (is_array($order)) {
                $key .= implode('', $order) . "#";
                foreach ($order as $k => $v) {
                    $key .= $k . $v;
                }
                $key .= "#";
            } else {
                $key .= $order . "#";
            }
        }
        if ($limit) {
            if (is_array($limit)) {
                foreach ($limit as $k => $v) {
                    $key .= $k . $v;
                }
                $key .= "#";
            } else {
                $key .= str_replace(',', '#', $limit) . "#";
            }
        }
        if ($offset) {
            if (is_array($offset)) {
                foreach ($offset as $k => $v) {
                    $key .= $k . $v;
                }
                $key .= "#";
            } else {
                $key .= $offset . "#";
            }
        }
        $key = preg_replace('/[^#^!\w]/', '', $key);
        /*
          /^([a-zA-Z]|[#])*$/}[^h]{1}[^
          echo $key;
          echo '<br>';
          echo strlen($key);
          echo '<br>';
         */
        if (strlen($key) > 250) {
            return false;
        } else {
            return $key;
        }
    }

    /*     * 转 where sql 语句。
     * 判断 是直接读数组还是 重写 sql 语句
     */

    public function _where($arr) {
        if (!$arr)
            return false;

        if (is_array($arr)) {
            $this->_sql = false;
            $this->db->where($arr);
        } else {
            $this->_sql = true;
            return 'WHERE ' . $arr;
        }
    }

    /*     * 转 order sql 语句。
     * 得到 sql 的 order
     * 字符串值 或 数组方式
     * 数组 字段 = 值
     */

    public function _order($arr) {
        if (!$arr)
            return false;
        $order = 'ORDER BY ';
        if (is_array($arr)) {
            if ($this->_sql) {
                foreach ($arr as $k => $v) {
                    $str[] = $k . ' ' . $v;
                }
                $order .= implode(',', $str);
                return $order;
            } else {
                foreach ($arr as $k => $v) {
                    $this->db->order_by($k, $v);
                }
            }
        } else {
            if ($this->_sql) {
                return $order . $arr;
            } else {
                $this->db->order_by($arr);
            }
        }
    }

    /* 转 limit sql 语句。
     * 得到 sql 的 limit
     * 字符串值  单个 或 2个
     * 结果2  limit $offset,$limit
     * 结果1  limit $limit
     */

    public function _limit($limit = '', $offset = '') {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $rs = 'LIMIT ';
        if ($offset && $limit) {
            if ($this->_sql) {
                $rs .= $offset . ',' . $limit;
                return $rs;
            } else {
                $this->db->limit($limit, $offset);
            }
        } elseif ($limit) {
            if ($this->_sql) {
                $rs .= $limit;
                return $rs;
            } else {
                $this->db->limit($limit);
            }
        }
    }

    /*     * 读 多条内容的sql 语句。
     * 得到 sql 语句值
     * $str  条件  数组与字符串都可以.绝定生成sql 语句.还是直接传入类
     * $filed  字段  字符串
     * $order  排序  字符串与数组(字段 = 值)
     * $limit  可以 10,10 与可以单独 10  不能传入数组
     * $offset 可以合并至$limit参数中.
     * $keys   做为键值的字段
     */

    public function _getWhere($table, $str = '', $filed = '*', $order = '', $limit = '', $offset = '', $keys = '') {
        $key = false;
        //是否开启memcached缓存
        if ($this->is_memcached)
            $key = $this->_clearstyle($table, $str, $filed, $order, $limit, $offset);
        if ($key) {
            //echo $key;
            //$this->cache->memcached->delete($key);
            //判断有没有此缓存内容
            $cache = $this->cache->memcached->get($key);
            //print_r($cache);die;
            if (!empty($cache[0])) {
                return $cache[0];
            }
        }

        $where = $this->_where($str);
        $ord = $this->_order($order);
        $lim = $this->_limit($limit, $offset);

        //$this->db->cache_on(); 开启 把结果 缓存到文件 
        if ($this->_sql) {
            $sql = "SELECT {$filed} FROM {$table} {$where} {$ord} {$lim}";
            $query = $this->db->query($sql);
        } else {
            $this->db->select($filed); //字段
            $query = $this->db->get($table);
        }

        //echo $this->db->last_query();die;
        $this->_sql = null;
        $rowArr = $query->result_array($keys);
        $query->free_result();
        //设置 memcached缓存
        if ($key && $rowArr) {
            $this->cache->memcached->save($key, $rowArr, 86400);
        }
        ///print_r($rowArr);
        return $rowArr;
    }

    /* 统计总数 */

    public function _getNums($table, $str = '', $filed = '*') {
        $where = $this->_where($str);
        if ($this->_sql) {
            $sql = "SELECT {$filed} FROM {$table} {$where}";
            $query = $this->db->query($sql);
        } else {
            $this->db->select($filed); //字段
            $query = $this->db->get($table);
        }
        $this->_sql = null;
        $rowArr = $query->num_rows();
        $query->free_result();
        return $rowArr;
    }

    /* 分页
     * $table     表名
     * $w_str     条件  字符串 与 数组 传给  _getWhere
     * $filed     字段  字符串
     * $w_order   条件  字符串 与 数组
     * $p         页码  当前页数
     * $links     链接  分页数字的连接地址
     * $pageSize  行数  每页多少条
     * $keys   做为键值的字段
     */

    public function _getPage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $links = '?', $pageSize = '10', $keys = '') {
        $p = (int) $p;
        $pageSize = (int) $pageSize;
        $nums = $this->_getNums($table, $w_str, $filed);
        $page = ceil($nums / $pageSize);
        $p = $p <= $page ? $p : $page;
        $beginNo = ($p - 1) * $pageSize;
        $beginNo = $beginNo >= 0 ? $beginNo : '0';
        $rs = $this->_getWhere($table, $w_str, $filed, $w_order, $pageSize, $beginNo, $keys);
        //echo $this->db->last_query();
        $str_page = $this->multLink($p, $nums, $links, $pageSize);
        $data['nums'] = $nums;
        $data['rs'] = $rs;
        $data['page'] = $page;
        $data['str_page'] = $str_page;
        return $data;
    }

    /* ajax分页
      fcun 方法名
     */

    public function _getAjaxPage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $fcun, $para = '', $pageSize = '10') {

        if (!$fcun)
            return false;

        $p = (int) $p;
        $pageSize = (int) $pageSize;
        $nums = $this->_getNums($table, $w_str, $filed);
        $page = ceil($nums / $pageSize);
        $p = $p <= $page ? $p : $page;
        $beginNo = ($p - 1) * $pageSize;
        $beginNo = $beginNo >= 0 ? $beginNo : '0';
        $rs = $this->_getWhere($table, $w_str, $filed, $w_order, $pageSize, $beginNo);
        //echo $this->db->last_query();die;
        $str_page = $this->ajaxMultLink($p, $nums, $fcun, $para, $pageSize);

        $data['nums'] = $nums;
        $data['rs'] = $rs;
        $data['page'] = $page;
        $data['str_page'] = $str_page;
        return $data;
    }

    /* 删除数据
     * table      表名
     * str        条件
     */

    public function delTable($table, $str) {
        $result = '';
        if (is_array($str)) {
            $result = $this->db->delete($table, $str);
        } else {
            $sql = "DELETE FROM " . $table . " where " . $str . "";
            $result = $this->db->query($sql);
        }
        if ($this->is_memcached)
            $this->cache->memcached->clean();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /* 读取一条记录
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     * $keys   做为键值的字段
     */

    public function getOneTableInfo($table, $arr = '', $filed = '*', $order = '', $keys = '') {
        $key = false;
        if ($this->is_memcached)
            $key = $this->_clearstyle($table, $arr, $filed, $order);
        if ($key) {
            $cache = $this->cache->memcached->get($key);
            if (!empty($cache[0])) {
                return $cache[0];
            }
        }

        $result = array();
        $this->db->select($filed); //字段
        if ($order)
            $this->db->order_by($order);
        $this->db->limit('1'); //字段
        if (is_array($arr)) {
            $this->db->where($arr);
        } else {
            $this->db->where($arr, '', FALSE);
        }
        $queryResult = $this->db->get($table);
		/* $rowArr=$queryResult->row(); */
		//echo $this->db->last_query();die;
        $rowArr = $queryResult->result_array($keys);
        if (!empty($rowArr)) {
            $result = $rowArr[0];
        }
        if ($key && $result) {
            $this->cache->memcached->save($key, $result, 86400);
        }
        //echo $this->db->last_query();die; //输出sql
        return $result;
    }

    /* 表所有数据 以ID做关键字
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     * isid       是否用ID做键值

      function getTableInfo($table,$arr='',$filed='*',$isid=''){
      $result = array();
      $this->db->select($filed); //字段
      if($arr){
      $queryResult=$this->db->get_where($table,$arr);
      }else{
      $queryResult=$this->db->get_where($table);
      }
      $rowArr=$queryResult->result_array();
      if(!empty($rowArr)){
      if($isid){
      foreach($rowArr as $k=>$v){
      $result[$v['id']]=$v;
      }
      }else{
      $result=$rowArr;
      }
      }
      return $result;
      }
     */
    /* 修改数据
     * table      表名
     * str        where 条件    数组 ( 字段 = 值 )
     * arr        修改的值       数组 ( 字段 = 值 )
     */
    /*
      $data = array('name' => $name, 'email' => $email, 'url' => $url);
      $where = "author_id = 1 AND status = 'active'";
      $str = $this->db->update_string('table_name', $data, $where);
      第一个参数是表名，第二个是被更新数据的关联数组，第三个参数是"where"参数。上面的例子生成的效果为：
      UPDATE table_name SET name = 'Rick', email = 'rick@example.com', url = 'example.com' WHERE author_id = 1 AND status = 'active'
     */

    public function getUpdateTable($table, $str, $arr) {
        if ($str && $arr && $table) {
            if (is_array($str)) {
                foreach ($str as $k => $v) {
                    $this->db->where($k, $v, false);
                }
                $result = $this->db->update($table, $arr);
            } else {
                $result = $this->db->update($table, $arr, $str);
            }
            //echo $this->db->last_query();die; //输出sql
            if ($this->is_memcached)
                $this->cache->memcached->clean();
            return $result;
        }else {
            return false;
        }
    }

    /* 添加数据
     * table      表名
     * arr        添加的值       数组 ( 字段 = 值 )
     */
    /*
      $data = array('name' => $name, 'email' => $email, 'url' => $url);
      $str = $this->db->insert_string('table_name', $data);
      第一个参数是表名，第二个是被插入数据的联合数组，上面的例子生成的效果为：
      INSERT INTO table_name (name, email, url) VALUES ('Rick', 'rick@example.com', 'example.com')
      被插入的数据会被自动转换和过滤，生成安全的查询语句。
     */

    public function getAddTable($table, $arr) {
        if (is_array($arr) && $arr && $table) {
            $rs = $this->db->insert($table, $arr);
            $row = $this->db->insert_id();
            if ($this->is_memcached)
                $this->cache->memcached->clean();
            if ($row) {
                return $row;
            } else {
                return $rs;
            }
        } else {
            return false;
        }
    }

    /* 直接读取sql  取多条数据
     * sql   sql语句
     * $keys   做为键值的字段
     */

    public function getSqlValue($sql, $keys = '') {
        $query = $this->db->query($sql);
        $rowArr = $query->result_array($keys);
        //echo $this->db->last_query();die; //输出sql
        if (!empty($rowArr)) {
            $result = $rowArr;
        } else {
            $result = array();
        }
        return $result;
    }

    /* 直接读取sql  取一条数据
     * sql   sql语句
     * $keys   做为键值的字段
     */

    public function getONESqlValue($sql, $keys = '') {
        $query = $this->db->query($sql);
        $rowArr = $query->result_array($keys);
        if (!empty($rowArr)) {
            $result = $rowArr[0];
        } else {
            $result = array();
        }
        return $result;
    }

    /*   分页函数
     *   currentPage    当前页
     *   totalRecords   总页数
     *   url            跳转链接
     *   pageSize       一页显示
     */

    public function multLink($currentPage, $totalRecords, $url, $pageSize = 10) {
        $lang_prev = '&nbsp;';
        $lang_next = '&nbsp;';
        if ($totalRecords <= $pageSize)
            return '';
        $mult = '';
        $totalPages = ceil($totalRecords / $pageSize);
        $mult .= '<div class="pages">';
        $currentPage < 1 && $currentPage = 1;
        if ($currentPage > 1) {
            $mult .= '<a href="' . $url . 'p=' . ($currentPage - 1) . '" class="page_icon">' . $lang_prev . '</a>';
        } else {
            $mult .= '<b class="page_icon">' . $lang_prev . '</b>';
        }
        if ($totalPages < 13) {
            for ($counter = 1; $counter <= $totalPages; $counter++) {
                if ($counter == $currentPage) {
                    $mult .= '<b>' . $counter . '</b>';
                } else {
                    $mult .= '<a href="' . $url . 'p=' . $counter . '">' . $counter . '</a>';
                }
            }
        } elseif ($totalPages > 11) {
            if ($currentPage < 7) {
                for ($counter = 1; $counter < 10; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . 'p=' . $counter . '">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="' . $url . 'p=' . ($totalPages - 1) . '">' . ($totalPages - 1) . '</a><a href="' . $url . 'p=' . $totalPages . '">' . $totalPages . '</a>';
            } elseif ($totalPages - 6 > $currentPage && $currentPage > 6) {
                $mult .= '<a href="' . $url . 'p=1">1</a><a href="' . $url . 'p=2">2</a><span>&#8230;</span>';
                for ($counter = $currentPage - 3; $counter <= $currentPage + 3; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . 'p=' . $counter . '">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="' . $url . 'p=' . ($totalPages - 1) . '">' . ($totalPages - 1) . '</a><a href="' . $url . 'p=' . $totalPages . '">' . $totalPages . '</a>';
            } else {
                $mult .= '<a href="' . $url . 'p=1">1</a><a href="' . $url . 'p=2">2</a><span>&#8230;</span>';
                for ($counter = $totalPages - 8; $counter <= $totalPages; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . 'p=' . $counter . '">' . $counter . '</a>';
                    }
                }
            }
        }
        if ($currentPage < $counter - 1) {
            $mult .= '<a href="' . $url . 'p=' . ($currentPage + 1) . '" class="nextprev">' . $lang_next . '</a>';
        } else {
            $mult .= '<b class="nextprev">' . $lang_next . '</b>';
        }
        //$mult .= '<div class="fl">记录<strong style="color:red;">'.$totalRecords.'</strong>条&nbsp;&nbsp;共<strong style="color:red;">'.$totalPages.'</strong>页</div>';
        $mult .= '</div>';
        return $mult;
    }

    /*   分页函数
     *   currentPage    当前页
     *   totalRecords   总页数
     *   url            跳转链接
     *   pageSize       一页显示
     */

    public function frontpage($currentPage, $totalRecords, $url, $pageSize = 10) {
        $lang_prev = '上一页';
        $lang_next = '下一页';
        if ($totalRecords <= $pageSize)
            return '';
        $mult = '';
        $totalPages = ceil($totalRecords / $pageSize);
        $mult .= '<div class="pages">';
        $currentPage < 1 && $currentPage = 1;
        if ($currentPage > 1) {
            $mult .= '<a href="' . $url . ($currentPage - 1) . '.htm">' . $lang_prev . '</a>';
        } else {
            $mult .= '<b>' . $lang_prev . '</b>';
        }
        if ($totalPages < 13) {
            for ($counter = 1; $counter <= $totalPages; $counter++) {
                if ($counter == $currentPage) {
                    $mult .= '<b>' . $counter . '</b>';
                } else {
                    $mult .= '<a href="' . $url . $counter . '.htm">' . $counter . '</a>';
                }
            }
        } elseif ($totalPages > 11) {
            if ($currentPage < 7) {
                for ($counter = 1; $counter < 10; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . $counter . '.htm">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="' . $url . ($totalPages - 1) . '.htm">' . ($totalPages - 1) . '</a><a href="' . $url . $totalPages . '.htm">' . $totalPages . '</a>';
            } elseif ($totalPages - 6 > $currentPage && $currentPage > 6) {
                $mult .= '<a href="' . $url . '1.htm">1</a><a href="' . $url . '2.htm">2</a><span>&#8230;</span>';
                for ($counter = $currentPage - 3; $counter <= $currentPage + 3; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . $counter . '.htm">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="' . $url . ($totalPages - 1) . '.htm">' . ($totalPages - 1) . '</a><a href="' . $url . $totalPages . '.htm">' . $totalPages . '</a>';
            } else {
                $mult .= '<a href="' . $url . '1.htm">1</a><a href="' . $url . '2.htm">2</a><span>&#8230;</span>';
                for ($counter = $totalPages - 8; $counter <= $totalPages; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b>' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="' . $url . $counter . '.htm">' . $counter . '</a>';
                    }
                }
            }
        }
        if ($currentPage < $counter - 1) {
            $mult .= '<a href="' . $url . ($currentPage + 1) . '.htm" class="nextprev">' . $lang_next . '</a>';
        } else {
            $mult .= '<b>' . $lang_next . '</b>';
        }
        $mult .= '<div class="fl">记录<strong style="color:red;">' . $totalRecords . '</strong>条&nbsp;&nbsp;共<strong style="color:red;">' . $totalPages . '</strong>页</div>';
        $mult .= '</div>';
        return $mult;
    }

    /*   ajax分页函数
     *   currentPage    当前页
     *   totalRecords   总页数
     *   func           方法
     *   pageSize       一页显示
     */

    public function ajaxMultLink($currentPage, $totalRecords, $func, $para, $pageSize = 10) {
        $lang_prev = '&nbsp;';
        $lang_next = '&nbsp;';
        $para = $para ? '\'' . $para . '\'' : '';
        if ($totalRecords <= $pageSize)
            return '';
        $mult = '';
        $totalPages = ceil($totalRecords / $pageSize);
        $mult .= '<div class="pages"><div>';
        $currentPage < 1 && $currentPage = 1;
        if ($currentPage > 1) {
            $mult .= '<a href="javascript:;" class="page_icon" onclick="' . $func . '(' . ($currentPage - 1) . ',' . $para . ');">' . $lang_prev . '</a>';
        } else {
            $mult .= '<b class="page_icon">' . $lang_prev . '</b>';
        }
        if ($totalPages < 13) {
            for ($counter = 1; $counter <= $totalPages; $counter++) {
                if ($counter == $currentPage) {
                    $mult .= '<b class="current">' . $counter . '</b>';
                } else {
                    $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . ',' . $para . ')">' . $counter . '</a>';
                }
            }
        } elseif ($totalPages > 11) {
            if ($currentPage < 7) {
                for ($counter = 1; $counter < 10; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . ',' . $para . ')">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="javascript:;" onclick="' . $func . '(' . ($totalPages - 1) . ',' . $para . ')">' . ($totalPages - 1) . '</a><a href="javascript:;" onclick="' . $func . '(' . $totalPages . ',' . $para . ')">' . $totalPages . '</a>';
            } elseif ($totalPages - 6 > $currentPage && $currentPage > 6) {
                $mult .= '<a href="javascript:;" onclick="' . $func . '(1,' . $para . ')">1</a><a href="javascript:;" onclick="' . $func . '(2,' . $para . ')">2</a><span>&#8230;</span>';
                for ($counter = $currentPage - 3; $counter <= $currentPage + 3; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . ',' . $para . ')">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="javascript:;" onclick="' . $func . '(' . ($totalPages - 1) . ',' . $para . ')">' . ($totalPages - 1) . '</a><a href="javascript:;" onclick="' . $func . '(' . $totalPages . ',' . $para . ')">' . $totalPages . '</a>';
            } else {
                $mult .= '<a href="javascript:;" onclick="' . $func . '(1,' . $para . ')">1</a><a href="javascript:;" onclick="' . $func . '(2,' . $para . ')">2</a><span>&#8230;</span>';
                for ($counter = $totalPages - 8; $counter <= $totalPages; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . ',' . $para . ')">' . $counter . '</a>';
                    }
                }
            }
        }
        if ($currentPage < $counter - 1) {
            $mult .= '<a href="javascript:;" onclick="' . $func . '(' . ($currentPage + 1) . ',' . $para . ')" class="nextprev">' . $lang_next . '</a>';
        } else {
            $mult .= '<b class="nextprev">' . $lang_next . '</b>';
        }
        //$mult .= '<div class="fl">共<strong style="color:red;">'.$totalRecords.'</strong>条记录&nbsp;&nbsp;  每页<strong style="color:red;">'.$pageSize.'/'.$totalPages.'</strong>记录</div>';

        $mult .= '</div></div>';
        return $mult;
    }

    /**
     *
     * 用于翻页
     * @param intege $page
     * @param intege $totalRecords
     * @param intege $pageSize
     */
    public function pp($page, $totalRecords, $pageSize) {
        $pp = array();
        $totalPages = ceil($totalRecords / $pageSize);
        $pp ['total'] = $totalRecords;
        $pp ['cur_page'] = $page;
        $pp ['total_page'] = $totalPages;
        if ($page < $totalPages)
            $pp ['next'] = $page + 1;
        if ($page > 1 && $totalPages > 0)
            $pp ['prev'] = $page - 1;
        return $pp;
    }

    public function is_Page($page) {
        return !empty($page) && preg_match('/^([0-9]+)$/', $page);
    }

}
