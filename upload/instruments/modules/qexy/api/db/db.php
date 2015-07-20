<?php
/**
 * API instruments for WebMCR
 *
 * Database process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */

if(!defined('QEXY_API')){ exit("Hacking Attempt!"); }

// Set database driver
if(isset($config['db_driver'])){
	if($config['db_driver']=='mysqli'){
		$driver = 'mysqli';
	}elseif($config['db_driver']=='mysql'){
		$driver = 'mysql';
	}else{
		exit("Sorry, but this database driver not supported");
	}
}else{
	$driver = 'mysql.old';
}

$_SESSION['count_mq'] = 0;

require_once(MCR_ROOT.'instruments/modules/qexy/api/db/'.$driver.'.class.php');

$api_db = new api_db();

/**
 * API instruments for WebMCR
 *
 * Database process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */
?>