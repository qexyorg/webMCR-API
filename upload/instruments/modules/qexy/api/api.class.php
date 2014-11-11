<?php
// Check webmcr constant
if(!defined('MCR')){ exit("Hacking Attempt!"); }

// Set default constant with API version
define('QEXY_API', '1.0');

// Set default API directory
define('API_DIR', MCR_ROOT.'instruments/modules/qexy/api/');

// Load db driver
require_once(API_DIR."db/db.php");

// Load filtered user functions
require_once(API_DIR."user.class.php");

class api{
	public $style = STYLE_URL;
	public $db = false;
	public $def_style = '';
	public $url = '';
	public $user = false;
	public $cfg = array();

	public function __construct($db=false, $user=false){
		$this->def_style = STYLE_URL.'Default/modules/qexy/api/';
		define("API_STYLE", STYLE_URL.'Default/modules/qexy/api/');
		
		$this->db = $db;
		
		$this->user = $user;
	}

	/**
	 * info_set() - Set info session
	 *
	 * @return void
	 *
	*/
	public function info_set(){
		if(isset($_SESSION['api_info'])){ define('API_INFO', $this->info()); }else{ define('API_INFO', ''); }
	}

	/**
	 * info_unset() - Unset info session
	 *
	 * @return void
	 *
	*/
	public function info_unset(){
		if(isset($_SESSION['api_info'])){unset($_SESSION['api_info']); unset($_SESSION['api_info_t']);}
	}

	/**
	 * notify(@param $text, @param $url, @param $title, @param $type) - Set notify and redirect
	 *
	 * @param $text - Message
	 * @param $url - YOUR_URL
	 * @param $title - Alert title
	 * @param $type - 1: Green | 2: Blue | 3: Red | 4: Yellow
	 *
	 * @return void
	 *
	*/
	public function notify($message='', $page='', $title='', $type=0){
		if(!empty($message) || !empty($title)){
			$_SESSION['api_ntf_t'] = $title;
			$_SESSION['api_ntf_m'] = $message;
			$_SESSION['api_ntf_type'] = $type;
		}

		header("Location: ".BASE_URL.$this->url.$page);
		exit;
	}

	/**
	 * BBquote(@param) - Recursive function for bb codes
	 *
	 * @param - String
	 *
	 * @return callback function
	 *
	*/
	private function BBquote($text)
	{
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
	public function bb_decode($text)
	{
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

		$text = $this->BBquote($text);

		return $text;
	}

	/**
	 * bb_panel(@param $value, @param @name, @param $panel_id) - Get BB panel
	 *
	 * @param $value - String
	 *
	 * @param $name - String
	 *
	 * @param $panel_id - String
	 *
	 * @return String
	 *
	*/
	public function bb_panel($value='', $name='comments', $panel_id='bbc'){
		ob_start();

		include_once(API_STYLE.'bb-panel.html');

		return ob_get_clean();
	}

	/**
	 * get_notify() - Get notify block
	 *
	 * @return String
	 *
	*/
	public function get_notify(){
		ob_start();
		if(isset($_SESSION['api_ntf_t'])){

			$type = intval($_SESSION['api_ntf_type']);

			switch($type){
				case 1: $type_text = "success";		break;
				case 2: $type_text = "info";		break;
				case 3: $type_text = "error";		break;
				case 4: $type_text = "warning";		break;

				default: $type_text = "";			break;
			}

			$data = array(
				"API_NOTIFY_TITLE" => htmlspecialchars($_SESSION['api_ntf_t']),
				"API_NOTIFY_MESSAGE" => htmlspecialchars($_SESSION['api_ntf_m']),
				"API_NOTIFY_TYPE" => $type_text
			);

			echo $this->sp(API_STYLE."notify.html", $data, true);
		}

		if(isset($_SESSION['api_ntf_t'])){ unset($_SESSION['api_ntf_t']); }
		if(isset($_SESSION['api_ntf_type'])){ unset($_SESSION['api_ntf_type']); }
		if(isset($_SESSION['api_ntf_m'])){ unset($_SESSION['api_ntf_m']); }

		return ob_get_clean();
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
	public function gen($length=10, $param = false) {
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
	 * info() - Set info params
	 *
	 * @return String
	 *
	*/
	private function info(){
		ob_start();

		if(empty($_SESSION['api_info'])){ return ob_get_clean(); }
		
		switch($_SESSION['api_info_t']){
			case 1: $type = 'alert-success'; break;
			case 2: $type = 'alert-info'; break;
			case 3: $type = 'alert-error'; break;

			default: $type = ''; break;
		}

		$text = htmlspecialchars($_SESSION['api_info']);

		include_once(API_STYLE.'info.html');
		return ob_get_clean();
	}

	/**
	 * csrf_check(@param) - Check csrf hacking
	 *
	 * @param - csrf variable
	 *
	 * @return boolean
	 *
	*/
	public function csrf_check($var='api_f'){
		if(!isset($_SESSION[$var]) || !isset($_POST[$var])){ return false; }

		if($_SESSION[$var]!=$_POST[$var]){ unset($_SESSION[$var]); return false; }

		unset($_SESSION[$var]);

		return true;
	}

	/**
	 * csrf_set(@param) - Set csrf variable
	 *
	 * @param - csrf variable
	 *
	 * @return String
	 *
	*/
	public function csrf_set($var){
		$_SESSION[$var] = md5(randString(30));
		return $_SESSION[$var];
	}

	/**
	 * pagination(@param) - Pagination method
	 *
	 * @param - Num result on the page
	 * @param - Default page (YOUR_PAGE)
	 * @param - SQL String
	 *
	 * @return - String
	 *
	*/
	public function pagination($res=10, $page='', $sql='', $theme=''){
		ob_start();

		if($this->db===false){ return ob_get_clean(); }

		if(isset($_GET['pid'])){$pid = intval($_GET['pid']);}else{$pid = 1;}
		$start	= $pid * $res - $res; if($page===0 || $sql===0){ return $start; }
		$query	= $this->db->query($sql);
		$ar		= $this->db->get_array($query);
		$max	= intval(ceil($ar[0] / $res));
		if($pid<=0 || $pid>$max){ return ob_get_clean(); }
		if($max>1)
		{
			$FirstPge='<li><a href="'.BASE_URL.$this->url.$page.'1"><<</a></li>';
			if($pid-2>0){$Prev2Pge	='<li><a href="'.BASE_URL.$this->url.$page.($pid-2).'">'.($pid-2).'</a></li>';}else{$Prev2Pge ='';}
			if($pid-1>0){$PrevPge	='<li><a href="'.BASE_URL.$this->url.$page.($pid-1).'">'.($pid-1).'</a></li>';}else{$PrevPge ='';}
			$SelectPge = '<li><a href="'.BASE_URL.$this->url.$page.$pid.'"><b>'.$pid.'</b></a></li>';
			if($pid+1<=$max){$NextPge	='<li><a href="'.BASE_URL.$this->url.$page.($pid+1).'">'.($pid+1).'</a></li>';}else{$NextPge ='';}
			if($pid+2<=$max){$Next2Pge	='<li><a href="'.BASE_URL.$this->url.$page.($pid+2).'">'.($pid+2).'</a></li>';}else{$Next2Pge ='';}
			$LastPge='<li><a href="'.BASE_URL.$this->url.$page.$max.'">>></a></li>';
			$path = (empty($theme)) ? API_STYLE."pagination.html" : $theme;
			include($path);
		}

		return ob_get_clean();
	}

	/**
	 * getMcrConfig() - Get webmcr config
	 *
	 * @return Array
	 *
	*/
	public function getMcrConfig(){
		global $config, $bd_names, $bd_users, $bd_money, $site_ways;

		$config_ar = array(
			"config" => $config,
			"bd_names" => $bd_names,
			"bd_users" => $bd_users,
			"bd_money" => $bd_money,
			"site_ways" => $site_ways
		);

		return $config_ar;
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

		$result = file_put_contents(MCR_ROOT.$file, $txt);

		if (is_bool($result) and $result == false){return false;}

		return true;
	}

	/**
	 * sp(@param $page, @param $data, @param $api_style) - Get static page
	 *
	 * @param $page path to file
	 *
	 * @param $data Array variables for loaded file
	 *
	 * @param $api_style Boolean
	 *
	 * @return String
	 *
	*/
	public function sp($page, $data=false, $api_style=false){
		ob_start();

		if($api_style){
			include($page);
		}else{
			include($this->style.$page);
		}

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
	 * bc(@param) - Set breadcrumbs
	 *
	 * @param Array
	 *
	 * @return String
	 *
	*/
	public function bc($array=array()){
		ob_start();

		if(empty($array)){ return ob_get_clean(); }

		$count = count($array)-1;

		$i = 0;

		$string = '';

		foreach($array as $title => $url){

			if($count==$i){
				$string .= '<li class="active">'.$title.'</li>';
			}else{
				$string .= '<li><a href="'.$url.'">'.$title.'</a> <span class="divider">»</span></li>';
			}

			$i++;
		}

		$data = array("LIST" => $string);

		include(API_STYLE."breadcrumbs.html");

		return ob_get_clean();
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
}

// Create new API object
$api = new api($api_db, $api_user);

// Load API styles
$content_js .= '<link href="'.API_STYLE.'css/style.css" rel="stylesheet">';
$content_js .= '<script src="'.API_STYLE.'js/content.js"></script>';

?>
