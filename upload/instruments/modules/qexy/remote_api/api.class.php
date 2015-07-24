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
	public $mcfg = array();
	public $db = false;
	public $user = false;

	public function __construct(){

		require_once(DIR_ROOT.'config.php');

		$this->mcfg = array(
			'config' => $config,
			'bd_names' => $bd_names,
			'bd_users' => $bd_users,
			'bd_money' => $site_ways,
			'site_ways' => $site_ways
		);

		// Set database driver
		$base = (@$config['db_driver']=='mysqli') ? 'mysqli' : 'mysql';

		// Loading DB class (mysqli)
		require_once(API_DIR_REMOTE.'db/'.$base.".class.php");

		$this->db = new db($config);

		// Loading DB class (mysqli)
		require_once(API_DIR_REMOTE.'user.class.php');

		$this->user = new user($this);
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
	 * BBquote(@param) - Recursive function for bb codes
	 *
	 * @param - String
	 *
	 * @return callback function
	 *
	*/
	private function BBquote($text){
		$reg = '#\[quote]((?:[^[]|\[(?!/?quote])|(?R))+)\[/quote]#isu';
		if (is_array($text)){$text = '<blockquote>'.$text[1].'</blockquote>';}
		return preg_replace_callback($reg, 'self::BBquote', $text);
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
		$text = nl2br($text);
		$patern = array(
			'/\[b\](.*)\[\/b\]/Usi',
			'/\[i\](.*)\[\/i\]/Usi',
			'/\[s\](.*)\[\/s\]/Usi',
			'/\[u\](.*)\[\/u\]/Usi',
			'/\[left\](.*)\[\/left\]/Usi',
			'/\[center\](.*)\[\/center\]/Usi',
			'/\[right\](.*)\[\/right\]/Usi',
			'/\[code\](.*)\[\/code\]/Usi',
		);
		$replace = array(
			'<b>$1</b>',
			'<i>$1</i>',
			'<s>$1</s>',
			'<u>$1</u>',
			'<p align="left">$1</p>',
			'<p align="center">$1</p>',
			'<p align="right">$1</p>',
			'<code>$1</code>',
		);
		$text = preg_replace($patern, $replace, $text);
		$text = preg_replace("/\[url=(?:&#039;|&quot;|\'|\")((((ht|f)tps?|mailto):(?:\/\/)?)(?:[^<\s\'\"]+))(?:&#039;|&quot;|\'|\")\](.*)\[\/url\]/Usi", "<a href=\"$1\">$5</a>", $text);
		$text = preg_replace("/\[img\](((ht|f)tps?:(?:\/\/)?)(?:[^<\s\'\"]+))\[\/img\]/Usi", "<img src=\"$1\">", $text);
		$text = preg_replace("/\[color=(?:&#039;|&quot;|\'|\")(\#[a-z0-9]{6})(?:&#039;|&quot;|\'|\")\](.*)\[\/color\]/Usi", "<font color=\"$1\">$2</font>", $text);
		$text = preg_replace("/\[size=(?:&#039;|&quot;|\'|\")([1-6]{1})(?:&#039;|&quot;|\'|\")\](.*)\[\/size\]/Usi", "<font size=\"$1\">$2</font>", $text);
		//<iframe width="560" height="315" src="https://www.youtube.com/embed/m8oMm_q1bpk" frameborder="0" allowfullscreen></iframe>
		$text = preg_replace("/\[youtube\](http|https)\:\/\/www\.youtube.com\/watch\?v=([\w-]+)\[\/youtube\]/Usi", "<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/$2\" frameborder=\"0\" allowfullscreen></iframe>", $text);
		$smile_list = array(
			'[:)]',
			'[:(]',
			'[;)]',
			'[:bear:]',
			'[:good:]',
			'[:wall:]',
			'[:D]',
			'[:shy:]',
			'[:secret:]',
			'[:dance:]',
			'[:rock:]',
			'[:sos:]',
			'[:girl:]',
			'[:facepalm:]',
		);
		$smile_replace = array(
			'<img src="'.BASE_URL.'qx_upload/api/smiles/1.gif" alt=":)" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/2.gif" alt=":(" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/3.gif" alt=";)" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/4.gif" alt=":bear:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/5.gif" alt=":good:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/6.gif" alt=":wall:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/7.gif" alt=":D" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/8.gif" alt=":shy:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/9.gif" alt=":secret:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/10.gif" alt=":dance:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/11.gif" alt=":rock:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/12.gif" alt=":sos:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/13.gif" alt=":girl:" />',
			'<img src="'.BASE_URL.'qx_upload/api/smiles/14.gif" alt=":facepalm:" />',
		);
		$text = str_replace($smile_list, $smile_replace, $text);
		$text = $this->BBquote($text);
		return $text;
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