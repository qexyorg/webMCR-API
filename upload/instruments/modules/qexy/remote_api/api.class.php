<?php
/**
 * Remote API instruments for WebMCR
 *
 * General process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */

if(!defined('QEXY_API')){ exit('Hacking Attempt!'); }

class api{
	public $mcfg	= array();
	public $db		= false;
	public $user	= false;
	public $cfg		= array();
	public $bb		= false;

	public function __construct(){

		require_once(DIR_ROOT.'config.php');

		$this->mcfg = array(
			'config' => $config,
			'bd_names' => $bd_names,
			'bd_users' => $bd_users,
			'bd_money' => $bd_money,
			'site_ways' => $site_ways
		);

		define('BASE_URL', $config['s_root']);

		// Set database driver
		$base = (@$config['db_driver']=='mysqli') ? 'mysqli' : 'mysql';

		// Loading DB class (mysqli)
		require_once(API_DIR_REMOTE.'db/'.$base.".class.php");

		$this->db = new db($config);

		// Loading DB class (mysqli)
		require_once(API_DIR_REMOTE.'user.class.php');

		$this->user = new user($this);

		// Loading DB class (mysqli)
		require_once(DIR_ROOT.'configs/rapi.cfg.php');

		if($cfg['csrfkey']===false){
			$cfg['csrfkey'] = md5($this->gen(20, true));
			if(!$this->savecfg($cfg, 'configs/rapi.cfg.php')){ $this->result('Not have a permissions for save configs/rapi.cfg.php'); }
		}

		$this->cfg = $cfg;

		// Get "BB-Code-Parser"
		require_once(API_DIR.'bbcode.parse.php');
		$this->bb = new bbcode();
	}

	public function result($msg='', $type=false, $data=array()){

		$array = array(
			"msg" => $msg,
			"type" => $type,
			"data" => $data,
		);

		echo json_encode($array);

		exit();
	}

	/**
	 * bb_panel(@param $for) - Get BB panel
	 *
	 * @param $for - String
	 *
	 * @param $target - String
	 *
	 * @return String
	 *
	*/
	public function bb_panel($for='.bb-textarea', $target='panel-target'){
		ob_start();

		include_once(API_STYLE.'bb-panel.html');

		return ob_get_clean();
	}

	/**
	 * bb_decode(@param) - Change BB-code to HTML
	 *
	 * @param - String
	 *
	 * @return String
	 *
	*/
	public function bb_decode($text){

		return $this->bb->parse($text);
	}

	/**
	 * gan(@param $length, @param $param) - Genirated random string
	 *
	 * @param $length Integer
	 *
	 * @param $param Boolean
	 *
	 * @return String
	 *
	*/
	public function gen($length=10, $param = false){
		$chars	= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
		if($param){ $chars .= '$()#@!'; }
		$string	= "";
		$len	= strlen($chars) - 1;  
		while (strlen($string) < $length){
			$string .= $chars[mt_rand(0,$len)];  
		}
		return $string;
	}

	/**
	 * savecfg(@param $cfg, @param $file, @param $var) - Save config file
	 *
	 * @param $cfg Array config
	 *
	 * @param $file Config file path (Example: instruments/menu.php)
	 *
	 * @param $var Name config variable
	 *
	 * @return Boolean
	 *
	*/
	public function savecfg($cfg, $file, $var="cfg"){
		$txt  = '<?php'.PHP_EOL;
		$txt .= '$'.$var.' = '.var_export($cfg, true).';'.PHP_EOL;
		$txt .= '?>';
		$result = file_put_contents(DIR_ROOT.$file, $txt);
		if (is_bool($result) and $result == false){return false;}
		return true;
	}

	/**
	 * sp(@param $page, @param $data) - Get static page
	 *
	 * @param $page path to file
	 *
	 * @param $data Array variables for loaded file
	 *
	 * @return String
	 *
	*/
	public function sp($page, $data=false){
		ob_start();
		
		include($page);

		return ob_get_clean();
	}

	/**
	 * getIP() - Get real user IP
	 *
	 * @return String
	 *
	*/
	public function getIP(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(!empty($_SERVER['HTTP_X_REAL_IP'])){
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/**
	 * filter_array_integer(@param) - Filter array variables to integer
	 *
	 * @param - Array
	 *
	 * @return - Array
	 *
	*/
	public function filter_array_integer($array){
		if(empty($array)){ return false; }
		
		$new_ar = array();
		foreach($array as $key => $value){
			$new_ar[] = intval($value);
		}
		return $new_ar;
	}

	public function email_load($ssl=true){
		require_once(API_DIR.'mail/PHPMailerAutoload.php');
		$this->email = new PHPMailer;
		//$this->email->SMTPDebug = 3;										// Enable verbose debug output
		$this->email->CharSet = "UTF-8";

		$config = $this->mcfg['config'];
		$this->email->setLanguage('ru', API_DIR.'mail/');
		if($config['smtp']){
			$this->email->isSMTP();											// Set mailer to use SMTP
			$this->email->Host = sqlConfigGet('smtp-host');					// Specify main and backup SMTP servers
			$this->email->SMTPAuth = true;									// Enable SMTP authentication
			$this->email->Username = sqlConfigGet('smtp-user');				// SMTP username
			$this->email->Password = sqlConfigGet('smtp-pass');				// SMTP password
			$this->email->SMTPSecure = ($ssl) ? 'ssl' : 'tls';	// Enable TLS encryption, `ssl` also accepted
			$this->email->Port = sqlConfigGet('smtp-port');					// TCP port to connect to
		}
		$this->email->FromName = sqlConfigGet('email-mail');
		$this->email->From = sqlConfigGet('email-mail');
		$this->email->addReplyTo(sqlConfigGet('email-mail'), sqlConfigGet('email-name'));
		$this->email->isHTML(true);											// Set email format to HTML
	}

	public function email($to, $subject, $message){
		$this->email->addAddress($to);										// Add a recipient
		$this->email->Subject = $subject;
		$this->email->Body    = $message;
		$this->email->AltBody = strip_tags($message);
		return $this->email->send();
	}
}

/**
 * Remote API instruments for WebMCR
 *
 * General process
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */
?>