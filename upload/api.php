<?php

define('QEXY_API', '1.3.0');
define('DIR_ROOT', dirname(__FILE__).'/');

// Set default API directory
define('API_DIR', DIR_ROOT.'instruments/modules/qexy/api/');

// Set default API remote directory
define('API_DIR_REMOTE', DIR_ROOT.'instruments/modules/qexy/remote_api/');

// Set default API remote directory
define('API_MOD_DIR', DIR_ROOT.'location/api_modules/');

// Loading API class
require_once(API_DIR_REMOTE."api.class.php");

$api = new api();

if(!isset($_REQUEST['apicsrf'])){ $api->result('Incorrect security key') }

$apicfg = md5($api->getIP().$api->cfg['csrfkey']);
if($_REQUEST['apicsrf']!==$apicfg){ $api->result('Incorrect security key'); }

$do = (isset($_GET['do'])) ? $_GET['do'] : false;

if($do!==false && file_exists(API_MOD_DIR.$do.'.php')){
	require_once(API_MOD_DIR.$do.'.php');
}else{
	$api->result('Action is not set!');
}

?>