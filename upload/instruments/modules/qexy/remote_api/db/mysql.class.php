<?php
/**
 * Remote API instruments for WebMCR
 *
 * Database process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */

if(!defined('QEXY_API')){ exit('Hacking Attempt!'); }

class db{
	public $obj = false;

	public $result = false;

	public $count_queries = 0;

	public function __construct($config){

		$this->obj = @mysql_connect($config['db_host'].':'.$config['db_port'], $config['db_login'], $config['db_passw']);

		if(!@mysql_select_db($config['db_name'], $this->obj)){ return; }

		@mysql_set_charset("UTF8", $this->obj);
	}

	public function query($string){
		$this->count_queries += 1;

		return @mysql_query($string, $this->obj);
	}

	public function get_affected_rows(){
		return mysql_affected_rows();
	}

	public function get_array($query=false){
		return mysql_fetch_array($query);
	}

	public function get_row($query=false){
		return mysql_fetch_assoc($query);
	}

	public function free($query=false){
		return mysql_free_result($query);
	}

	public function num_rows($query=false){
		return mysql_num_rows($query);
	}

	public function insert_id(){
		return mysql_insert_id();
	}

	public function safesql($string){
		return mysql_real_escape_string($string);
	}

	public function HSC($string){
		return htmlspecialchars($string);
	}

	public function error(){
		return mysql_error();
	}
}

/**
 * Remote API instruments for WebMCR
 *
 * Database process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */
?>