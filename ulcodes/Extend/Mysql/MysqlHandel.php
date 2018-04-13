<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/13
 * Time: 17:36
 */

namespace ulcodes\Extend\Mysql;


class MysqlHandel implements MysqlHandelInterface
{
    /**
     * 列名
     *
     * @var array
     */
    protected $_field = [];
    /**
     * 表名
     *
     * @var array
     */
    protected $_name;
    /**
     * 别名
     *
     * @var
     */
    protected $_byname;
    /**
     * 条件
     *
     * @var array
     */
    protected $_where = [];
    /**
     * 参数
     *
     * @var array
     */
    protected $_params = [];
    /**
     * OFFSET
     *
     * @var int
     */
    protected $_offset = 0;
    /**
     * limit
     *
     * @var int
     */
    protected $_limit = 0;
    /**
     * GROUP BY
     *
     * @var array
     */
    protected $_group = [];
    /**
     * ORDER BY
     *
     * @var array
     */
    protected $_order = [];
    /**
     * HAVING
     *
     * @var array
     */
    protected $_having = [];
    /**
     * INNER JOIN
     *
     * @var array
     */
    protected $_join = [];
    /**
     * UPDATE SET
     *
     * @var array
     */
    protected $_update_set = [];
    /**
     * INSERT INTO 列
     *
     * @var array
     */
    protected $_insert_field = [];
    /**
     * INSERT INTO VALUES
     * @var array
     */
    protected $_insert_values = [];

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

}