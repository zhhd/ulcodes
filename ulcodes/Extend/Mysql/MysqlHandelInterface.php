<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/13
 * Time: 17:55
 */

namespace ulcodes\Extend\Mysql;


interface MysqlHandelInterface
{
    /**
     * 查询单条数据
     *
     * @param array $where 条件
     * @param array $field 列
     * @return array
     */
    public function row($where = [], $field = []);

    /**
     * 查询多条数据
     *
     * @param array $where 条件
     * @param array $field 列
     * @return mixed
     */
    public function select($where = [], $field = []);

    public function update();
}