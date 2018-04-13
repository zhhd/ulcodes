<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/12
 * Time: 9:46
 */

namespace ulcodes\Core\Drive\Db\Mysql;

use PDO;

interface MysqlInterface
{
    /**
     * 打开数据库连接
     *
     * @return void
     */
    public function connect();

    /**
     * 查询一行数据
     *
     * @param string     $query     查询语句
     * @param null|array $params    参数
     * @param int        $fetchMode 查询模式
     * @return array|null
     */
    public function row($query, $params = null, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 查询多行数据
     *
     * @param string     $query     查询语句
     * @param null|array $params    参数
     * @param int        $fetchMode 查询模式
     * @return array|null
     */
    public function select($query, $params = null, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 更新数据
     *
     * @param string     $query  查询语句
     * @param null|array $params 参数
     * @return int
     */
    public function update($query, $params = null);


    /**
     * 删除数据
     *
     * @param string     $query  查询语句
     * @param null|array $params 参数
     * @return int
     */
    public function delete($query, $params = null);

    /**
     * 插入数据
     *
     * @param string     $query    查询语句
     * @param null|array $params   参数
     * @param bool       $returnId 是否返回最后一次插入的id
     * @return int
     */
    public function insert($query, $params = null, $returnId = true);

    /**
     * 设置和获取最后一次执行SQL
     *
     * @param string|null $query  查询语句
     * @param array|null  $params 参数
     * @return string
     */
    public function lastSql($query = null, $params = null);

    /**
     * 最后一次插入的id,没有返回null
     *
     * @return int|null
     */
    public function lastInsertId();

    /**
     * 开启事务
     *
     * @return void
     */
    public function beginTrans();

    /**
     * 提交事务
     *
     * @return void
     */
    public function commitTrans();

    /**
     * 回滚事务
     *
     * @return void
     */
    public function rollBack();
}