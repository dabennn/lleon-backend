<?php
class Database{
	private static $_instance;
	private static $_connectSource;
	private $_dbConfig = array(
		'host'=>'127.0.0.1',
		'user'=>'root',
		'password'=>'1212',
		'database'=>'lleon'
	);
	private function __construct(){
		
	}
	private function __clone(){
		
	}
	public static function getInstance(){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function connect(){
		if(!self::$_connectSource){
			self::$_connectSource = mysql_connect($this->_dbConfig['host'],$this->_dbConfig['user'],$this->_dbConfig['password']);
		
			if(!self::$_connectSource){
				throw new Exception('mysql connect error'.mysql_error());//数据库链接失败时抛出异常
			}
			
			mysql_select_db($this->_dbConfig['database'],self::$_connectSource);
			mysql_query('SET NAMES UTF8',self::$_connectSource);
		}
		return self::$_connectSource;
	}
	public function insert($sql){
		return mysql_query($sql,self::$_connectSource);
	}
	public function delete($sql){
		return mysql_query($sql,self::$_connectSource);
	}
	public function update($sql){
		return mysql_query($sql,self::$_connectSource);
	}
	public function close(){
		return mysql_close(self::$_connectSource);
	}
}

?>