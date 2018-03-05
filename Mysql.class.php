<?php
/**
mysql数据库底操作类
 */

class Mysql{
    protected $link; //数据库连接
    protected $host; //连接主机
    protected $user; //连接用户
    protected $password; //连接密码
    protected $charset; //设定字符集
    protected $db; //数据库名
    protected $sql; //上一条执行的sql语句

    public function __construct($db,$host="localhost", $user="root", $password="",$charset="utf8"){
       $this->connect($host,$user,$password);
       $this->setCharset($charset);
       $this->selectDB($db,$this->link);
    }

    /**
     * mysql数据库的连接
     * @param $host 连接主机名称
     * @param $user 连接用户
     * @param $password 连接密码
     */
    protected function connect($host,$user,$password){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->link = @mysql_connect($this->host,$this->user,$this->password);
    }

    /**
     * 设定数据库字符集
     * @param $charset 字符集
     */
    protected function setCharset($charset){
        $this->charset = $charset;
        $sql = "set names {$this->charset}";
        $this->query($sql);
    }

    /**
     * 选择数据库
     * @param $db 需要操作数据库名
     * @param $link 数据库连接
     */
    protected function selectDB($db,$link){
        $this->db = $db;
        mysql_select_db($this->db,$link);
    }

    /**
     * 执行sql语句
     * @param $sql sql语句
     */
    public function query($sql){
        $this->sql = $sql;
        return mysql_query($sql);
    }

    /**
     * 获取上一条sql语句
     */
    public function getLastSql(){
        return $this->sql;
    }

    /**
     * 关闭mysql连接
     */
    public function close(){
        mysql_close($this->link);
    }

}