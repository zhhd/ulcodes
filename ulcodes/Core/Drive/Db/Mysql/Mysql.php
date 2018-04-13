<?php
/**
 * Created by PhpStorm.
 * User: zhd
 * Date: 2018/4/11
 * Time: 16:38
 */

namespace ulcodes\Core\Drive\Db\Mysql;

use PDO;
use ulcodes\Core\Drive\Helper\Helper;

class Mysql implements MysqlInterface
{
    private $_last_sql;

    /**
     * PDO实例
     * @var PDO
     */
    public $pdo;

    /**
     * 数据库配置
     * @var array
     */
    private $_settings;


    function __construct($host, $port, $user, $password, $db_name, $charset = 'utf8')
    {
        $this->_settings = array(
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $db_name,
            'charset'  => $charset,
        );
        $this->connect();
    }

    /**
     * 打开连接
     */
    public function connect()
    {
        $dsn       = 'mysql:dbname=' . $this->_settings["dbname"] . ';host=' .
            $this->_settings["host"] . ';port=' . $this->_settings['port'];
        $this->pdo = new PDO($dsn, $this->_settings["user"], $this->_settings["password"],
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . (!empty($this->_settings['charset']) ?
                        $this->_settings['charset'] : 'utf8'),
            ));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * 关闭连接
     */
    public function closeConnection()
    {
        $this->pdo = null;
    }

    /**
     * 查询单条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return array|bool 没有数据返回false
     */
    public function row($query, $params = null, $fetchMode = PDO::FETCH_ASSOC)
    {
        $this->lastSql($query, $params);
        $sQuery = $this->pdo->prepare($query);
        $sQuery->execute($params);

        return $sQuery->fetch($fetchMode);
    }

    /**
     * 查询多条数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @param int          $fetchMode
     * @return array|bool 没有数据返回false
     */
    public function select($query, $params = null, $fetchMode = PDO::FETCH_ASSOC)
    {
        $this->lastSql($query, $params);
        $sQuery = $this->pdo->prepare($query);
        $sQuery->execute($params);

        return $sQuery->fetchAll($fetchMode);
    }

    /**
     * 更新数据
     * @param string       $query  sql语句
     * @param array | null $params 参数
     * @return int 影响条数
     */
    public function update($query, $params = null)
    {
        $this->lastSql($query, $params);
        $sQuery = $this->pdo->prepare($query);
        $sQuery->execute($params);

        return $sQuery->rowCount();
    }

    /**
     * 删除数据
     *
     * @param string     $query  查询语句
     * @param null|array $params 参数
     * @return int
     */
    public function delete($query, $params = null)
    {
        $this->lastSql($query, $params);
        $sQuery = $this->pdo->prepare($query);
        $sQuery->execute($params);
        return $sQuery->rowCount();
    }


    /**
     * 插入数据
     *
     * @param string     $query    查询语句
     * @param null|array $params   参数
     * @param bool       $returnId 是否返回最后一次插入的id
     * @return int
     */
    public function insert($query, $params = null, $returnId = true)
    {
        $this->lastSql($query, $params);
        $sQuery = $this->pdo->prepare($query);
        $sQuery->execute($params);
        $rowCount = $sQuery->rowCount();
        if ($rowCount > 0 && $returnId) {
            return $this->lastInsertId();
        } else {
            return $rowCount;
        }
    }

    /**
     * 设置和获取最后一次执行SQL
     *
     * @param string|null $query  查询语句
     * @param array|null  $params 参数
     * @return string
     */
    public function lastSql($query = null, $params = null)
    {
        if ($query !== null) {
            $this->_last_sql = sprintf('[SQL]::%s[Prams]::%s', $query, Helper::json($params));
        }
        return $this->_last_sql;
    }


    /**
     * 返回最后一次插入的id
     * @return int
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 开启事务
     */
    public function beginTrans()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 提交事务
     */
    public function commitTrans()
    {
        $this->pdo->commit();
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

}