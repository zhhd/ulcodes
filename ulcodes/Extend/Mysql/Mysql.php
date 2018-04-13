<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/12
 * Time: 10:29
 */

namespace ulcodes\Extend\Mysql;

class Mysql implements MysqlInterface
{
    /**
     * 列名
     *
     * @var array
     */
    private $_field = [];
    /**
     * 表名
     *
     * @var array
     */
    private $_name;
    /**
     * 别名
     *
     * @var
     */
    private $_byname;
    /**
     * 条件
     *
     * @var array
     */
    private $_where = [];
    /**
     * 参数
     *
     * @var array
     */
    private $_params = [];
    /**
     * OFFSET
     *
     * @var int
     */
    private $_offset = 0;
    /**
     * limit
     *
     * @var int
     */
    private $_limit = 0;
    /**
     * GROUP BY
     *
     * @var array
     */
    private $_group = [];

    /**
     * ORDER BY
     *
     * @var array
     */
    private $_order = [];

    /**
     * HAVING
     *
     * @var array
     */
    private $_having = [];

    /**
     * INNER JOIN
     *
     * @var array
     */
    private $_join = [];

    /**
     * WHERE和HAVING条件处理
     *
     * @param $data
     * @return string
     */
    private function conditionHandle($data)
    {
        $_data = '';
        foreach ($data as $op => $item) {
            foreach ($item as $op_item) {
                if (is_array($op_item)) {
                    $_data_tmp = '';
                    foreach ($op_item as $field => $value) {
                        $_field = uniqid(':');
                        if (is_array($value)) {
                            $_value_tmp = '';
                            foreach ($value as $val) {
                                $_field                 = uniqid(':');
                                $_value_tmp             .= sprintf(',%s', $_field);
                                $this->_params[$_field] = $val;
                            }
                            $_value_tmp = ltrim($_value_tmp, ',');
                            $_data_tmp  .= sprintf(' %s `%s` IN (%s) ', $op, $field, $_value_tmp);
                        } else {
                            $_data_tmp              .= sprintf(' %s `%s`= %s ', $op, $field, $_field);
                            $this->_params[$_field] = $value;
                        }
                    }
                    $op_item = ltrim(ltrim($_data_tmp), $op);
                }
                if (empty($_data)) {
                    $_data_tmp = sprintf(' (%s) ', $op_item);
                } else {
                    $_data_tmp = sprintf(' %s (%s) ', $op, $op_item);
                }
                $_data .= $_data_tmp;
            }
        }
        return $_data;
    }

    /**
     * 获取列名
     *
     * @return string
     */
    public function getField()
    {
        $_field = '';
        if (empty($this->_field))
            return null;

        foreach ($this->_field as $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $_field .= sprintf(',`%s`', $value);
                }
            } else {
                $_field .= sprintf(',%s', $item);
            }
        }

        return ltrim($_field, ',');
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->_byname)) {
            return sprintf('`%s`', $this->_name);
        } else {
            return sprintf('`%s` AS `%s`', $this->_name, $this->_byname);
        }
    }

    /**
     * 获取条件
     *
     * @return string
     */
    public function getWhere()
    {
        return $this->conditionHandle($this->_where);
    }

    /**
     * 获取参数
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 获取group
     *
     * @return string
     */
    public function getGroup()
    {
        $_item = '';
        foreach ($this->_group as $item) {
            if (is_array($item)) {
                foreach ($item as $val) {
                    $_item .= sprintf(',`%s`', $val);
                }

            } else {
                $_item .= sprintf(',%s', $_item);
            }
        }
        return ltrim($_item, ',');
    }

    /**
     * 获取order
     *
     * @return string
     */
    public function getOrder()
    {
        $_item = '';
        foreach ($this->_order as $item) {
            $order = $item[0];
            $asc   = $item[1] ? 'ASC' : 'DESC';
            if (is_array($order)) {
                foreach ($order as $value) {
                    $_item .= sprintf(',`s%` s%', $value, $asc);
                }
            } else {
                $_item .= sprintf(',s%', $order);
            }
        }
        return ltrim($_item, ',');
    }

    /**
     * 获取having
     *
     * @return string
     */
    public function getHaving()
    {
        return $this->conditionHandle($this->_having);
    }

    /**
     * 获取JOIN
     *
     * @return string
     */
    public function getJoin()
    {
        $join = '';
        foreach ($this->_join as $item) {
            $_item  = '';
            $name   = $item [0];
            $on     = $item [1];
            $byname = empty($item[2]) ? $name : $item[2];
            if (is_array($on)) {
                foreach ($on as $o) {
                    $_item .= sprintf('AND s%', $o);
                }
            } else {
                $_item = $on;
            }
            $join .= sprintf('INNER JOIN `%s` AS `%s` ON %s', $name, $byname, ltrim($_item, 'AND'));
        }
        return $join;
    }

    /**
     * 查询列名
     *
     * @param string|array $field
     * @return $this
     *
     * @example title("name,sex");     //  name,sex
     *          title(["name","sex"]); // `name`,`sex`
     */
    public function field($field)
    {
        $this->_field [] = $field;
        return $this;
    }

    /**
     * 表名
     *
     * @param string $name   表名
     * @param string $byname 别名
     * @return $this
     */
    public function tableName($name, $byname = null)
    {
        $this->_name   = $name;
        $this->_byname = $byname;
        return $this;
    }

    /**
     * 别名
     *
     * @param string $name
     * @return $this
     */
    public function tableByname($name)
    {
        $this->_byname = $name;
        return $this;
    }

    /**
     * 条件
     *
     * 注意：如果条件以字符形式传入，需进行sql注入处理
     * @param mixed  $where
     * @param string $op
     * @return $this
     *
     * @example where(['name'=>'张三','sex'=>1]); // AND (`name`='张三' and `sex`=1)
     *          where("name='张三' and sex=1");   //  AND (name='张三' and sex=1)
     *          where(['name'=>['张三','李四'],'sex'=>1],'OR');   //  OR (`name` in ('张三','李四') and sex=1)
     */
    public function where($where, $op = WHERE_AND)
    {
        if (!isset($this->_where[$op]))
            $this->_where[$op] = [];

        $this->_where[$op][] = $where;
        return $this;
    }

    /**
     * 添加and条件
     *
     * @param string $field
     * @param mixed  $value
     * @return $this
     *
     * @see MysqlInterface::where()
     */
    public function whereAnd($field, $value)
    {
        $this->where([$field => $value], WHERE_AND);
        return $this;
    }

    /**
     * 添加or条件
     *
     * @param string $field
     * @param mixed  $value
     * @return $this
     *
     * @see MysqlInterface::where()
     */
    public function whereOr($field, $value)
    {
        $this->where([$field => $value], WHERE_OR);
        return $this;
    }

    /**
     * 设置offset
     *
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * 设置limit
     *
     * @param int $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * 分页
     *
     * @param int $page    页码
     * @param int $pageNum 数量
     * @return $this
     */
    public function page($page, $pageNum)
    {
        $this->offset(($page - 1) * $pageNum);
        $this->limit($pageNum);
        return $this;
    }

    /**
     * 分组
     *
     * @param string|array $group
     * @return $this
     *
     * @example group("name,sex");      // GROUP BY name,sex
     *          group(["name","sex"]);  // GROUP BY `name`,`sex`
     */
    public function group($group)
    {
        $this->_group[] = $group;
        return $this;
    }

    /**
     * 排序
     *
     * @param string|array $order
     * @param bool         $asc 正序排序
     * @return $this
     *
     * @example order("sex DESC,age ASC");      // ORDER BY sex DESC,age ASC
     *          order(["sex","age"],true);  // ORDER BY `sex` ASC,`age` ASC
     */
    public function order($order, $asc = true)
    {
        $this->_order[] = [$order, $asc];
        return $this;
    }

    /**
     * 聚合条件
     *
     * @param string|array $having
     * @param string       $op
     * @return $this
     *
     * @see MysqlInterface::where() 说明
     */
    public function having($having, $op = WHERE_AND)
    {
        if (!isset($this->_having[$op]))
            $this->_having[$op] = [];

        $this->_having[$op][] = $having;
        return $this;
    }

    /**
     * INNER JOIN <table> ON
     *
     * @param string       $table   表名
     * @param string|array $on
     * @param string|null  $bytable 别名
     * @return $this
     *
     * @example join("clazz",["a.id = b.aid","a.i = b.i"],"b"); // INNER JOIN `clazz` as b ON a.id = b.aid and a.i = b.i
     *          join("clazz","a.id = b.aid","b"); // INNER JOIN `clazz` as b ON a.id = b.aid
     *
     */
    public function join($table, $on, $bytable = null)
    {
        $this->_join[] = [$table, $on, $bytable];
        return $this;
    }

    /**
     * LEFT JOIN <table> ON
     *
     * @param string       $table   表名
     * @param string|array $on
     * @param string|null  $bytable 别名
     * @return $this
     *
     * @see MysqlInterface::join();
     */
    public function leftJoin($table, $on, $bytable = null)
    {
        // TODO: Implement leftJoin() method.
    }

    /**
     * RIGHT JOIN <table> ON
     *
     * @param string       $table   表名
     * @param string|array $on
     * @param string|null  $bytable 别名
     * @return $this
     *
     * @see MysqlInterface::join();
     */
    public function rightJoin($table, $on, $bytable = null)
    {
        // TODO: Implement rightJoin() method.
    }

    /**
     * SET <field1> = <value1>,<field2> = <value2>,...
     *
     * @param string|array $set
     * @return $this
     *
     * @example set(['name'=>'张三','sex'=>1])   // SET `name`='张三',`sex`=1
     *          set("name='张三',set=1")         // SET name='张三',sex=1
     */
    public function set($set)
    {
        // TODO: Implement set() method.
    }

}