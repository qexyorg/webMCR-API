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

		$this->obj = @new mysqli($config['db_host'], $config['db_login'], $config['db_passw'], $config['db_name'], $config['db_port']);

		if(mysqli_connect_errno($this->obj)){ return; }

		if(!$this->obj->set_charset("utf8")){ return; }
	}

	public function query($string){
		$this->count_queries += 1;

		$this->result = @$this->obj->query($string);

		return $this->result;
	}

	public function get_affected_rows(){
		return $this->obj->affected_rows;
	}

	public function get_array($query=false){
		return $this->result->fetch_array();
	}

	public function get_row($query=false){
		return $this->result->fetch_assoc();
	}

	public function free(){
		return $this->result->free();
	}

	public function num_rows($query=false){
		return $this->result->num_rows;
	}

	public function insert_id(){
		return $this->obj->insert_id;
	}

	public function safesql($string){
		return $this->obj->real_escape_string($string);
	}

	public function HSC($string=''){
		return htmlspecialchars($string);
	}

	public function error(){
		return $this->obj->error;
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