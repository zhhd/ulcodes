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
     * @return mixed
     */
    public function connect();

    /**
     * 查询一行数据
     *
     * @param      $query
     * @param null $params
     * @param int  $fetchMode
     * @return mixed
     */
    public function row($query, $params = null, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 查询多行数据
     *
     * @param      $query
     * @param null $params
     * @param int  $fetchMode
     * @return mixed
     */
    public function select($query, $params = null, $fetchMode = PDO::FETCH_ASSOC);

    /**
     * 更新数据
     *
     * @param      $query
     * @param null $params
     * @return mixed
     */
    public function update($query, $params = null);


    /**
     * 删除数据
     *
     * @param      $query
     * @param null $params
     * @return mixed
     */
    public function delete($query, $params = null);

    /**
     * 插入数据
     *
     * @param      $query
     * @param null $params
     * @return mixed
     */
    public function insert($query, $params = null);

    /**
     * 获取上一次执行的sql
     *
     * @return mixed
     */
    public function lastSql();

    /**
     * 最后一次插入的id,没有返回null
     *
     * @return mixed
     */
    public function lastInsertId();

    /**
     * 开启事务
     *
     * @return mixed
     */
    public function beginTrans();

    /**
     * 提交事务
     *
     * @return mixed
     */
    public function commitTrans();

    /**
     * 回滚事务
     *
     * @return mixed
     */
    public function rollBack();
}