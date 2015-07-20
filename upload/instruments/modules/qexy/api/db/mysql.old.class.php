<?php
/**
 * API instruments for WebMCR
 *
 * MySQL class for database (old version webmcr)
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
		return BD($query);
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
		return mysql_fetch_array($query);
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
		return mysql_fetch_assoc($query);
	}


	/**
	 * Public Method MNR(@param)
	 *
	 * @param resource
	 *
	 * @return integer or false(boolean)
	 *
	 */
	public function num_rows($query){
		return mysql_num_rows($query);
	}


	/**
	 * Public Method get_affected_rows()
	 *
	 * @return integer
	 *
	 */
	public function get_affected_rows(){
		return mysql_affected_rows();
	}

	public function insert_id(){
		return mysql_insert_id();
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
		return mysql_real_escape_string($query);
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
 * MySQL class for database (old version webmcr)
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */
?>
