<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/12
 * Time: 10:29
 */

namespace ulcodes\Extend\Mysql;

class Mysql extends MysqlHandel implements MysqlInterface
{
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
    public function where($where, $op = 'AND')
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
        $this->where([$field => $value], 'AND');
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
        $this->where([$field => $value], 'AND');
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
    public function having($having, $op = 'AND')
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
     * @param string       $mode    链接模式
     * @return $this
     *
     * @example join("clazz",["a.id = b.aid","a.i = b.i"],"b"); // INNER JOIN `clazz` as b ON a.id = b.aid AND a.i = b.i
     *          join("clazz","a.id = b.aid","b"); // INNER JOIN `clazz` as b ON a.id = b.aid
     *
     */
    public function join($table, $on, $bytable = null, $mode = 'INNER JOIN')
    {
        $this->_join[] = [$table, $on, $bytable, $mode];
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
        $this->_join[] = [$table, $on, $bytable, 'LEFT JOIN'];
        return $this;
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
        $this->_join[] = [$table, $on, $bytable, 'RIGHT JOIN'];
        return $this;
    }

    /**
     * SET <field1> = <value1>,<field2> = <value2>,...
     *
     * @param string|array $updateSet
     * @return $this
     *
     * @example set(['name'=>'张三','sex'=>1])   // SET `name`='张三',`sex`=1
     *          set("name='张三',set=1")         // SET name='张三',sex=1
     */
    public function updateSet($updateSet)
    {
        $this->_update_set[] = $updateSet;
        return $this;
    }

    /**
     * 插入列
     * INSERT INTO <TABLE> (<FILED>) VALUES (<VALUES>)
     *
     * @param string|array $field
     * @return $this
     *
     * @example insertFiled('name,sex')         // INSERT INTO <TABLE> ('name,sex') VALUES (<VALUES>)
     *          insertFiled(['name','sex'])     // INSERT INTO <TABLE> ('`name`,`sex`') VALUES (<VALUES>)
     */
    public function insertFiled($field)
    {
        $this->_insert_field[] = $field;
        return $this;
    }

    /**
     * 插入值
     * INSERT INTO <TABLE> (<FILED>) VALUES (<VALUES>)
     *
     * @param string|array $values
     * @param bool         $one
     * @return $this
     *
     * @example insertValues(['张三','1'], true)      // INSERT INTO <TABLE> (<FILED>) VALUES ('张三','1')
     *          insertValues("'张三','1'", true)      // INSERT INTO <TABLE> (<FILED>) VALUES ('张三','1')
     *          insertValues([['张三','1'],['李四','2']], false)      // INSERT INTO <TABLE> (<FILED>) VALUES
     *          ('张三','1'),('李四','2')
     */
    public function insertValues($values, $one = true)
    {
        $this->_insert_values[] = [$values, $one];
        return $this;
    }
}