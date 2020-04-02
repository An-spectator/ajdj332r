<?php
defined('IN_LCMS') or exit('No permission');
class SQLPDO
{
    private $pdo;
    private $psm;
    /**
     * [__construct 连接数据库]
     * @param  [type] $db [数据库信息]
     * @return [type]     [description]
     */
    public function __construct($sqlinfo, $user = "", $pass = "")
    {
        try {
            $this->pdo = new PDO($sqlinfo, $user, $pass);
        } catch (PDOException $e) {
            LCMS::X(500, iconv('gbk', 'utf-8', $e->getMessage()));
        }

    }
    /**
     * [version 获取数据库版本]
     * @return [type] [description]
     */
    public function version()
    {
        return $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    }
    /**
     * [prepare 数据库操作准备]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function prepare($sql)
    {
        $this->psm = $this->pdo->prepare($sql);
        if ($this->psm) {
            $this->psm->execute();
            return $this->psm;
        } else {
            LCMS::X(500, "数据错误");
        }
    }
    /**
     * [fetch 从结果集中获取下一行]
     * @param  [type] $sql         [description]
     * @param  string $result_type [description]
     * @return [type]              [description]
     */
    public function fetch($sql, $result_type = "")
    {
        return $this->prepare($sql)->fetch($result_type);
    }
    /**
     * [fetch_all 返回一个包含结果集中所有行的数组]
     * @param  [type] $sql         [description]
     * @param  string $result_type [description]
     * @return [type]              [description]
     */
    public function fetch_all($sql, $result_type = "")
    {
        return $this->prepare($sql)->fetchAll($result_type);
    }
    /**
     * [fetch_column 从结果集中的下一行返回单独的一列]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function fetch_column($sql)
    {
        return $this->prepare($sql)->fetchColumn();
    }
    /**
     * [get_tables 获取数据库所有表名]
     * @return [type] [description]
     */
    public function get_tables($sql = "SHOW TABLES")
    {
        return $this->fetch_all($sql, PDO::FETCH_COLUMN);
    }
    /**
     * [get_one 查询一条数据]
     * @param  [type] $sql         [description]
     * @param  [type] $result_type [description]
     * @return [type]              [description]
     */
    public function get_one($sql, $result_type = PDO::FETCH_ASSOC)
    {
        return $this->fetch($sql, $result_type);
    }
    /**
     * [get_all 查询多条数据]
     * @param  [type] $sql         [description]
     * @param  [type] $result_type [description]
     * @return [type]              [description]
     */
    public function get_all($sql, $result_type = PDO::FETCH_ASSOC)
    {
        return $this->fetch_all($sql, $result_type);
    }
    /**
     * [update 更新数据]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function update($sql)
    {
        return $this->prepare($sql)->rowCount();
    }
    /**
     * [insert 插入数据]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function insert($sql)
    {
        // $this->prepare($sql)->rowCount();
        // $error = $this->pdo->errorInfo();
        // dump($sql);
        return $this->prepare($sql)->rowCount();
    }
    /**
     * [delete 删除数据]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function delete($sql)
    {
        return $this->prepare($sql)->rowCount();
    }
    /**
     * [query 自由查询sql语句]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function query($sql)
    {

        $this->psm = $this->pdo->query($sql);
        return $this->psm;
    }
    /**
     * [counter 查询数据库条数]
     * @param  [type] $sql [description]
     * @return [type]      [description]
     */
    public function counter($sql)
    {
        return $this->fetch_column($sql);
    }
    /**
     * [insert_id 获取最后一个插入数据的ID]
     * @return [type] [description]
     */
    public function insert_id()
    {
        return $this->pdo->lastInsertId();
    }
    /**
     * [affected_rows 返回受 DELETE、INSERT、 或 UPDATE 语句影响的行数]
     * @return [type] [description]
     */
    public function affected_rows()
    {
        return $this->psm->rowCount();
    }
    /**
     * [error 返回最后一次操作的错误信息]
     * @return [type] [description]
     */
    public function error()
    {
        $error = $this->psm->errorInfo();
        if ($error[0] !== 00000 && $error[1]) {
            return "[{$error[1]}] {$error[2]}";
        }
    }
    /**
     * [errno 返回最后一次操作的错误编号]
     * @return [type] [description]
     */
    public function errno()
    {
        return $this->psm->errorCode();
    }
    /**
     * [close 关闭数据库连接]
     * @return [type] [description]
     */
    public function close()
    {
        $this->pdo = null;
        $this->psm = null;
    }
}
