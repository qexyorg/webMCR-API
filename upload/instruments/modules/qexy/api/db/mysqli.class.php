<?php
/**
 * API instruments for WebMCR
 *
 * MySQLi class for database
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */

if (!defined('QEXY_API')){ exit("Hacking Attempt!"); }

class api_db{

	private $row = false;
	public $num = 0;

	public function __construct(){
		$this->db = getDB();
	}

	/**
	 * Public Method MQ(@param)
	 *
	 * @param string (Syntax SQL)
	 *
	 * @return resource or false(boolean)
	 *
	 */
	public function query($query, $num=0){

		$_SESSION['count_mq']++;
		$this->num = $num;

		$this->row[$this->num] = $this->db->query($query);
		
		return $this->row[$this->num];
	}


	/**
	 * Public Method get_array(@param)
	 *
	 * @param resource
	 *
	 * @return array or false(boolean)
	 *
	 */
	public function get_array($query){
		return mysqli_fetch_array($this->row[$this->num]->getResult());
	}


	/**
	 * Public Method get_row(@param)
	 *
	 * @param resource
	 *
	 * @return array or false(boolean)
	 *
	 */
	public function get_row($query){
		return mysqli_fetch_assoc($this->row[$this->num]->getResult());//mysqli_fetch_assoc($query);
	}


	/**
	 * Public Method MNR(@param)
	 *
	 * @param resource
	 *
	 * @return integer or false(boolean)
	 *
	 */
	public function num_rows($query=false){
		return $this->row[$this->num]->rowCount();
	}


	/**
	 * Public Method get_affected_rows()
	 *
	 * @return integer
	 *
	 */
	public function get_affected_rows(){
		return $this->row[$this->num]->rowCount();
	}


	/**
	 * Public Method safesql(@param)
	 *
	 * @param string
	 *
	 * @return string or false(boolean)
	 *
	 */
	public function safesql($query){

		return mysqli_real_escape_string($this->db->getLink(), $query);
	}

	public function insert_id(){
		return mysqli_insert_id($this->db->getLink());
	}


	/**
	 * Public Method HSC(@param)
	 *
	 * @param string
	 *
	 * @return string
	 *
	 */
	public function HSC($query){
		return htmlspecialchars($query);
	}
}

/**
 * API instruments for WebMCR
 *
 * MySQLi class for database
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */
?>
