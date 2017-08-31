<?php

class mysql extends CI_Model {

    public $cateOption = '';
    /* getOneTableInfo
      echo $this->db->last_query(); 输出sql 语句
      $this->db->affected_rows() 当执行写入操作（insert,update等）的查询后，显示被影响的行数
      echo $this->db->count_all('my_table'); 计算出指定表的总行数并返回。在第一个参数中写入被提交的表名
      $query = $this->db->get('mytable');可以获取一个表的全部数据
      $query = $this->db->get('mytable', 10, 20);第二和第三个参数允许你设置一个结果集每页纪录数(limit)和结果集的偏移(offset)
      $this->db->get_where('mytable', array('id' => $id), $limit, $offset); 跟上面的函数一样，只是它允许你在函数的第二个参数那里添加一个 where 从句，从而不用使用 db->where() 这个函数：
      $this->db->select('title, content, date');  允许你在SQL查询中写 SELECT 字段 部分:
      (  select_min  select_avg  select_sum )同样
      $this->db->select_max(); 为你的查询编写一个 "SELECT MAX(field)"。你可以选择性的给出第二个参数，以便重命名结果字段名。
      $this->db->select_max('age');
      $query = $this->db->get('members');// Produces: SELECT MAX(age) as age FROM members
      $this->db->select_max('age', 'member_age');
      $query = $this->db->get('members'); // Produces: SELECT MAX(age) as member_age FROM members
      $this->db->from();
      允许你编写查询中的FROM部分:
      $this->db->select('title, content, date');
      $this->db->from('mytable');
      $query = $this->db->get();
      // 生成: SELECT title, content, date FROM mytable
      说明: 正如前面所说，查询中的FROM部分可以在 $this->db->get() 函数中指定，所以你可以根据自己的喜好来选择使用哪个方法。
      $this->db->join();
      允许你编写查询中的JOIN部分:
      $this->db->select('*');
      $this->db->from('blogs');
      $this->db->join('comments', 'comments.id = blogs.id');
      $query = $this->db->get();
      // 生成:
      // SELECT * FROM blogs
      // JOIN comments ON comments.id = blogs.id
      如果你想要在查询中使用多个连接，可以多次调用本函数。
      如果你需要指定 JOIN 的类型，你可以通过本函数的第三个参数来指定。可选项包括：left, right, outer, inner, left outer, 以及 right outer.
      $this->db->join('comments', 'comments.id = blogs.id', 'left');
      // 生成: LEFT JOIN comments ON comments.id = blogs.id
      $this->db->where();
      本函数允许你使用四种方法中的一种来设置 WHERE 子句:
      说明: 传递给本函数的所有值都会被自动转义，以便生成安全的查询。
      简单的 key/value 方法:
      $this->db->where('name', $name);
      // 生成: WHERE name = 'Joe'
      请注意等号已经为你添加。
      如果你多次调用本函数，那么这些条件会被 AND 连接起来:
      $this->db->where('name', $name);
      $this->db->where('title', $title);
      $this->db->where('status', $status);

      // WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
      自定义 key/value 方法:
      你可以在第一个参数中包含一个运算符，以便控制比较:

      $this->db->where('name !=', $name);
      $this->db->where('id <', $id);

      // 生成: WHERE name != 'Joe' AND id < 45

      关联数组方法:
      $array = array('name' => $name, 'title' => $title, 'status' => $status);

      $this->db->where($array);

      // 生成: WHERE name = 'Joe' AND title = 'boss' AND status = 'active'
      使用这个方法时你也可以包含运算符:

      $array = array('name !=' => $name, 'id <' => $id, 'date >' => $date);

      $this->db->where($array);

      自定义字符串:
      你可以手动的编写子句:

      $where = "name='Joe' AND status='boss' OR status='active'";

      $this->db->where($where);

      $this->db->where() 接受可选的第三个参数。如果你将它设置为 FALSE, CodeIgniter 将不会为你那些包含反勾号的字段名或表名提供保护。

      $this->db->where('MATCH (field) AGAINST ("value")', NULL, FALSE);


      $this->db->or_where();
      本函数与上面的那个几乎完全相同，唯一的区别是本函数生成的子句是用 OR 来连接的:

      $this->db->where('name !=', $name);
      $this->db->or_where('id >', $id);

      // 生成: WHERE name != 'Joe' OR id > 50

      说明: or_where() 以前被叫作 orwhere(), 后者已经过时，现已从代码中移除 orwhere()。

      $this->db->where_in();
      生成一段 WHERE field IN ('item', 'item') 查询语句，如果合适的话，用 AND 连接起来。

      $names = array('Frank', 'Todd', 'James');
      $this->db->where_in('username', $names);
      // 生成: WHERE username IN ('Frank', 'Todd', 'James')

      $this->db->or_where_in();
      生成一段 WHERE field IN ('item', 'item') 查询语句，如果合适的话，用 OR 连接起来。

      $names = array('Frank', 'Todd', 'James');
      $this->db->or_where_in('username', $names);
      // 生成: OR username IN ('Frank', 'Todd', 'James')

      $this->db->where_not_in();
      生成一段 WHERE field NOT IN ('item', 'item') 查询语句，如果合适的话，用 AND 连接起来。

      $names = array('Frank', 'Todd', 'James');
      $this->db->where_not_in('username', $names);
      // 生成: WHERE username NOT IN ('Frank', 'Todd', 'James')

      $this->db->or_where_not_in();
      生成一段 WHERE field NOT IN ('item', 'item') 查询语句，如果合适的话，用 OR 连接起来。

      $names = array('Frank', 'Todd', 'James');
      $this->db->or_where_not_in('username', $names);
      // 生成: OR username NOT IN ('Frank', 'Todd', 'James')


      $this->db->order_by("title", "desc");

      // 生成: ORDER BY title DESC
      你也可以在第一个参数中传递你自己的字符串:

      $this->db->order_by('title desc, name asc');

      // 生成: ORDER BY title DESC, name ASC
      或者，多次调用本函数就可以排序多个字段。

      $this->db->order_by("title", "desc");
      $this->db->order_by("name", "asc");

      // 生成: ORDER BY title DESC, name ASC

     */
    private $_sql;

    /*
     * 判断 是直接读数组还是 重写 sql 语句
     */

    function _where($arr) {
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

    /* 得到 sql 的 order
     * 字符串值 或 数组方式
     * 数组 字段 = 值
     */

    function _order($arr) {
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

    /* 得到 sql 的 order
     * 字符串值  单个 或 2个
     * 结果2  limit $offset,$limit
     * 结果1  limit $limit
     */

    function _limit($limit = '', $offset = '') {
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

    /* 得到 sql 语句值
     * $str  条件  数组与字符串都可以.绝定生成sql 语句.还是直接传入类
     * $filed  字段  字符串
     * $order  排序  字符串与数组(字段 = 值)
     * $limit  可以 10,10 与可以单独 10  不能传入数组
     * $offset 可以合并至$limit参数中.
     */

    function _getWhere($table, $str = '', $filed = '*', $order = '', $limit = '', $offset = '') {

        $where = $this->_where($str);
        $ord = $this->_order($order);
        $lim = $this->_limit($limit, $offset);
        if ($this->_sql) {
            $sql = "SELECT {$filed} FROM {$table} {$where} {$ord} {$lim}";
            $query = $this->db->query($sql);
        } else {
            $this->db->select($filed); //字段
            $query = $this->db->get($table);
        }
        //echo $this->db->last_query();die;
        $this->_sql = null;
        $rowArr = $query->result_array();
        $query->free_result();
        return $rowArr;
    }

    /* 统计总数 */

    function _getNums($table, $str = '', $filed = '*') {
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
     */

    function _getPage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $links = '?', $pageSize = '10') {
        $p = (int) $p;
        $pageSize = (int) $pageSize;
        $nums = $this->_getNums($table, $w_str, $filed);
        $page = ceil($nums / $pageSize);
        $p = $p <= $page ? $p : $page;
        $beginNo = ($p - 1) * $pageSize;
        $beginNo = $beginNo >= 0 ? $beginNo : '0';
        $rs = $this->_getWhere($table, $w_str, $filed, $w_order, $pageSize, $beginNo);
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

    function _getAjaxPage($table, $w_str = '', $filed = '*', $w_order = '', $p = '1', $fcun, $para = '', $pageSize = '10') {

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
        $str_page = $this->ajaxMultLink($p, $page, $fcun, $para, $pageSize);

        $data['nums'] = $nums;
        $data['rs'] = $rs;
        $data['page'] = $page;
        $data['str_page'] = $str_page;
        return $data;
    }

    /* 分页函数
     * table       表名
     * str         条件
     */

    function getaleveluser($table, $str, $pageSize = 10, $pageNo = 1) {
        $result = array();
        $pageSize = $pageSize ? $pageSize : 10;
        $pageNo = $pageNo ? $pageNo : 1;
        $countnums = "SELECT count(*) as nums FROM " . $table . " WHERE " . $str . "";
        $nums = $this->db->query($countnums);
        $nums = $nums->result_array();
        $result['totalRowNums'] = $nums[0]['nums'];
        $beginNo = ($pageNo - 1) * $pageSize;
        $sql = "SELECT * FROM " . $table . " WHERE " . $str . " order by id desc limit " . $beginNo . "," . $pageSize . "";
        $query = $this->db->query($sql);
        $rowArr = $query->result_array();
        //echo $this->db->last_query();die;
        $result['data'] = $rowArr;
        return $result;
    }

    /* 删除数据
     * table      表名
     * str        条件
     */

    function delTable($table, $str) {
        $result = '';
        if (is_array($str)) {
            $result = $this->db->delete($table, $str);
        } else {
            $sql = "DELETE FROM " . $table . " where " . $str . "";
            $result = $this->db->query($sql);
        }
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /* 读取一条记录
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     */

    function getOneTableInfo($table, $arr, $filed = '*', $order = '') {
        $result = array();
        $this->db->select($filed); //字段
        if ($order)
            $this->db->order_by($order);
        $this->db->limit('1'); //字段
        $queryResult = $this->db->get_where($table, $arr);
        /* $rowArr=$queryResult->row(); */
        $rowArr = $queryResult->result_array();
        if (!empty($rowArr)) {
            $result = $rowArr[0];
        }

        //echo $this->db->last_query();die; //输出sql 
        return $result;
    }

    /* 多条
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     * offset		
     * limit		limti $offset,$limit
     */

    function getTableAllInfo($table, $arr, $filed = '*', $limit = '10', $offset = '0') {
        $result = array();
        $this->db->select($filed); //字段
        //当 arr  为空出错时
        //$query = $this->db->get('mytable', 10, 20); 
        $queryResult = $this->db->get_where($table, $arr, $limit, $offset);
        $rowArr = $queryResult->result_array();
        if (!empty($rowArr)) {
            $result = $rowArr;
        }
        return $result;
    }

    /* 读取表所有数据
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     */

    public function getTableAllValue($table, $filed = '*') {
        $result = array();
        $this->db->select($filed); //字段
        $queryResult = $this->db->get($table);
        $rowArr = $queryResult->result_array();
        if (!empty($rowArr)) {
            $result = $rowArr;
        }
        return $result;
    }

    /* 表所有数据 以ID做关键字
     * table      表名
     * arr        where 条件 数组 ( 字段 = 值 )
     */

    function getTableInfo($table, $arr = '', $filed = '*') {
        $result = array();
        $this->db->select($filed); //字段
        if ($arr) {
            $queryResult = $this->db->get_where($table, $arr);
        } else {
            $queryResult = $this->db->get_where($table);
        }
        $rowArr = $queryResult->result_array();
        if (!empty($rowArr)) {
            foreach ($rowArr as $k => $v) {
                $result[$v['id']] = $v;
            }
        }
        return $result;
    }

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

    function getUpdateTable($table, $str, $arr) {
        if ($str && $arr && $table) {
            if (is_array($str)) {
                foreach ($str as $k => $v) {
                    $this->db->where($k, $v);
                }
                $result = $this->db->update($table, $arr);
            } else {
                $result = $this->db->update($table, $arr, $str);
            }
			//echo $result;
            //echo $this->db->last_query();die; //输出sql 
            return $result;
        } else {
            return false;
        }
    }
   //修改数据,返回受影响的行数--2016-1-11
    function getUpdateTable_1111($table, $str, $arr) {
        if ($str && $arr && $table) {
            if (is_array($str)) {
                foreach ($str as $k => $v) {
                    $this->db->where($k, $v);
                }
                $this->db->update($table, $arr);
            } else {
                $this->db->update($table, $arr, $str);
            }
			
			//↑以上的更新操作是执行成功的 然后我想判断是否更新成功
			if ($this->db->affected_rows() > 0){
			   $result=$this->db->affected_rows();
			}else {
			   $result=0;
			}
			//echo $result;
            //echo $this->db->last_query();die; //输出sql 
            return $result;
        } else {
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

    function getAddTable($table, $arr) {
    	//$this->db->cache_delete_all();//清除所有缓存
        if (is_array($arr) && $arr && $table) {
            $rs = $this->db->insert($table, $arr);
            $row = $this->db->insert_id();
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
     */

	function getSqlValue($sql) {
		
		$query = $this->db->query($sql);
		//echo $this->db->last_query();die;
		
		$rowArr = $query->result_array();
		
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
     */

    function getOneSqlValue($sql) {
    	//$this->db->cache_on();//开启缓存
		$query = $this->db->query($sql);
		//echo $sql;die;
		//echo $this->db->last_query();die; //输出sql 
        $rowArr = $query->result_array();
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

    function multLink($currentPage, $totalRecords, $url, $pageSize = 10) {
        $lang_prev = ' &laquo; ';
        $lang_next = ' &raquo; ';
        if ($totalRecords <= $pageSize)
            return '';
        $mult = '';
        $totalPages = ceil($totalRecords / $pageSize);
        $mult .= '<div class="pages">';
        $currentPage < 1 && $currentPage = 1;
        if ($currentPage > 1) {
            $mult .= '<a href="' . $url . 'p=' . ($currentPage - 1) . '">' . $lang_prev . '</a>';
        } else {
            $mult .= '<b>' . $lang_prev . '</b>';
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
            $mult .= '<b>' . $lang_next . '</b>';
        }
        //$mult .= '<span class="fl">记录<strong style="color:red;">' . $totalRecords . '</strong>条&nbsp;&nbsp;共<strong style="color:red;">' . $totalPages . '</strong>页</div>';
       // $mult .= '</span>';之前错误的
		$mult .= '</div>';
        return $mult;
    }

    /*   分页函数
     *   currentPage    当前页
     *   totalRecords   总页数
     *   url            跳转链接
     *   pageSize       一页显示
     */

    function frontpage($currentPage, $totalRecords, $url, $pageSize = 10) {
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

    function ajaxMultLink($currentPage, $totalRecords, $func, $para, $pageSize = 10) {
        $lang_prev = '上一页';
        $lang_next = '下一页';
        $para = $para ? ',' . $para : '';
        if ($totalRecords <= $pageSize)
            return '';
        $mult = '';
        $totalPages = ceil($totalRecords / $pageSize);
        $mult .= '<div class="pages"><div class="nextprev">';
        $currentPage < 1 && $currentPage = 1;
        if ($currentPage > 1) {
            $mult .= '<a href="javascript:;" onclick="' . $func . '(' . ($currentPage - 1) . $para . ');">' . $lang_prev . '</a>';
        } else {
            $mult .= '<b class="nextprev">' . $lang_prev . '</b>';
        }
        if ($totalPages < 13) {
            for ($counter = 1; $counter <= $totalPages; $counter++) {
                if ($counter == $currentPage) {
                    $mult .= '<b class="current">' . $counter . '</b>';
                } else {
                    $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . $para . ')">' . $counter . '</a>';
                }
            }
        } elseif ($totalPages > 11) {
            if ($currentPage < 7) {
                for ($counter = 1; $counter < 10; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . $para . ')">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="javascript:;" onclick="' . $func . '(' . ($totalPages - 1) . $para . ')">' . ($totalPages - 1) . '</a><a href="javascript:;" onclick="' . $func . '(' . $totalPages . $para . ')">' . $totalPages . '</a>';
            } elseif ($totalPages - 6 > $currentPage && $currentPage > 6) {
                $mult .= '<a href="javascript:;" onclick="' . $func . '(1' . $para . ')">1</a><a href="javascript:;" onclick="' . $func . '(2' . $para . ')">2</a><span>&#8230;</span>';
                for ($counter = $currentPage - 3; $counter <= $currentPage + 3; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . $para . ')">' . $counter . '</a>';
                    }
                }
                $mult .= '<span>&#8230;</span><a href="javascript:;" onclick="' . $func . '(' . ($totalPages - 1) . $para . ')">' . ($totalPages - 1) . '</a><a href="javascript:;" onclick="' . $func . '(' . $totalPages . $para . ')">' . $totalPages . '</a>';
            } else {
                $mult .= '<a href="javascript:;" onclick="' . $func . '(1' . $para . ')">1</a><a href="javascript:;" onclick="' . $func . '(2' . $para . ')">2</a><span>&#8230;</span>';
                for ($counter = $totalPages - 8; $counter <= $totalPages; $counter++) {
                    if ($counter == $currentPage) {
                        $mult .= '<b class="current">' . $counter . '</b>';
                    } else {
                        $mult .= '<a href="javascript:;" onclick="' . $func . '(' . $counter . $para . ')">' . $counter . '</a>';
                    }
                }
            }
        }
        if ($currentPage < $counter - 1) {
            $mult .= '<a href="javascript:;" onclick="' . $func . '(' . ($currentPage + 1) . $para . ')" class="nextprev">' . $lang_next . '</a>';
        } else {
            $mult .= '<b class="nextprev">' . $lang_next . '</b>';
        }
        $mult .= '<span class="fl">共<strong style="color:red;">' . $totalRecords . '</strong>条记录&nbsp;&nbsp;  每页<strong style="color:red;">' . $pageSize . '/' . $totalPages . '</strong>记录</span>';

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
    function pp($page, $totalRecords, $pageSize) {
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

    function is_Page($page) {
        return !empty($page) && preg_match('/^([0-9]+)$/', $page);
    }

    // 下拉选项功能 

    function html_options($table, $data, $t = -1, $seleId = 0) {
        $cateList = $this->db->get_where($table, $data);
        $cateList = $cateList->result();
        $t ++;
        $nbsps = str_repeat('&nbsp;', 3 * $t);
        if ($cateList) {
            foreach ($cateList as $k => $v) {
                if ($seleId) {
                    if ($v->id == $seleId) {
                        $this->cateOption .= '<option value="' . $v->id . '" selected="selected">' . $nbsps . $v->name . '</option>';
                    } else {
                        $this->cateOption .= '<option value="' . $v->id . '">' . $nbsps . $v->name . '</option>';
                    }
                } else {
                    $this->cateOption .= '<option value="' . $v->id . '">' . $nbsps . $v->name . '</option>';
                }
                $data = array(
                    'sub' => $v->id,
                );
                $this->html_options($table, $data, $t, $seleId);
            }
        }
    }

    public function getSubCateInfo($filed = '*', $sub = '0', $ifshow = '') {
        $rs = '';
        $str = ''; //where
        $str = "WHERE sub='" . $sub . "'";
        if ($ifshow) {
            $str = " AND ifopen='1'";
        }
        $rs = $this->m_sql->getSqlValue("SELECT {$filed} FROM lk_goods_cate {$str} ORDER BY sort DESC,id DESC");
        return $rs;
    }

    //取得分类
    public function getCateAllValue($filed = '*') {

        $row = $this->getSubCateInfo($filed);
        if ($row) {
            $rs = $this->getCateRecursive($row, $filed);
        }
        return $rs;
    }

    //递归 取得分类
    public function getCateRecursive($arr, $filed = '*') {
        if (is_array($arr) && $arr) {
            foreach ($arr as $k => $v) {
                if (isset($v['id'])) {
                    $str = array();
                    $str = $this->getSubCateInfo($filed, $v['id']);
                    if ($str) {
                        $str = $this->getCateRecursive($str, $filed);
                    }
                    $arr[$k]['row'] = $str;
                }
            }
        }
        return $arr;
    }

}
