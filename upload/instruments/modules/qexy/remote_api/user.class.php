<?php
/**
 * Remote API instruments for WebMCR
 *
 * User class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */

if(!defined('QEXY_API')){ exit('Hacking Attempt!'); }

class user{
	private $api = false;
	private $db = false;

	// User values
	public $id = 0;
	public $login = '';
	public $female = 2;
	public $email = '';
	public $password = '';
	public $tmp = '';
	public $ip = '127.0.0.1';
	public $comments = 0;
	public $gameplay_last = '0000-00-00 00:00:00';
	public $create_time = '0000-00-00 00:00:00';
	public $active_last = '0000-00-00 00:00:00';
	public $default_skin = 1;
	public $is_auth = false;

	// Group values
	public $group = 0;									// Group id
	public $group_name = 'Гость';						// Group name
	public $lvl = 0;									// Access lvl
	public $group_system = 1;							// Is system group

	// Group permissions
	public $permissoins = false;
	public $permissoins_ar = array();

	// User money
	public $balance = 0;

	public function __construct($api){

		$this->api = $api;
		$this->db = $api->db;
		$this->mcfg = $api->mcfg;

		$auth_ar = $this->is_auth();

		if($auth_ar===false){ $this->set_unauth(); return false; }

		$bd_names = $this->mcfg['bd_names'];
		$bd_users = $this->mcfg['bd_users'];
		$bd_money = $this->mcfg['bd_money'];

		$is_iconomy_table = (is_bool($bd_names['iconomy'])) ? "" : "LEFT JOIN `{$bd_names['iconomy']}` AS `i` ON `i`.`{$bd_money['login']}`=`u`.`{$bd_users['login']}`";
		$is_iconomy_rows = (is_bool($bd_names['iconomy'])) ? "" : ", `i`.`{$bd_money['money']}`";

		$value = $this->db->safesql($auth_ar[0]);

		if($auth_ar[1]=='tmp'){
			$where = "`u`.`{$bd_users['tmp']}`='$value'";
		}elseif($auth_ar[1]=='id'){
			$where = "`u`.`{$bd_users['id']}`='$value'";
		}else{
			$where = "`u`.`{$bd_users['login']}`='$value'";
		}

		$query = $this->db->query("SELECT `u`.`{$bd_users['id']}`, `u`.`{$bd_users['login']}`, `u`.`{$bd_users['password']}`,
											`u`.`{$bd_users['ip']}`, `u`.`{$bd_users['email']}`, `u`.`{$bd_users['female']}`,
											`u`.`{$bd_users['group']}`, `u`.`{$bd_users['ctime']}`, `u`.`{$bd_users['tmp']}`,
											`u`.comments_num, `u`.gameplay_last, `u`.active_last, `u`.default_skin,
											`g`.`name` AS `group_name`, `g`.`lvl`, `g`.`system`, `g`.`lvl`, `g`.`change_skin`, `g`.`change_pass`,
											`g`.`change_login`, `g`.`change_cloak`, `g`.`add_news`, `g`.`add_comm`,
											`g`.`adm_comm`, `g`.`max_fsize`, `g`.`max_ratio`$is_iconomy_rows
									FROM `{$bd_names['users']}` AS `u`
									LEFT JOIN `{$bd_names['groups']}` AS `g`
										ON `g`.id=`u`.`{$bd_users['group']}`
									$is_iconomy_table
									WHERE $where");

		if(!$query || $this->db->num_rows($query)<=0){ $this->set_unauth(); return false; }

		$ar = $this->db->get_row($query);

		$this->id				= intval($ar[$bd_users['id']]);
		$this->login			= $this->db->HSC($ar[$bd_users['login']]);
		$this->female			= intval($ar[$bd_users['female']]);
		$this->email			= $this->db->HSC($ar[$bd_users['email']]);
		$this->password			= $this->db->HSC($ar[$bd_users['password']]);
		$this->tmp				= $ar[$bd_users['tmp']];
		$this->comments			= intval($ar['comments_num']);
		$this->gameplay_last	= strtotime($ar['gameplay_last']);
		$this->create_time		= strtotime($ar[$bd_users['ctime']]);
		$this->active_last		= strtotime($ar['active_last']);
		$this->default_skin		= intval($ar['default_skin']);

		$this->group			= intval($ar[$bd_users['group']]);
		$this->group_name		= $this->db->HSC($ar['group_name']);
		$this->lvl				= intval($ar['lvl']);
		$this->group_system		= (intval($ar['system'])===1) ? true : false;

		$this->permissoins_ar	= array(
										'change_skin' => (intval($ar['change_skin'])===1) ? true : false,
										'change_pass' => (intval($ar['change_pass'])===1) ? true : false,
										'change_login' => (intval($ar['change_login'])===1) ? true : false,
										'change_cloak' => (intval($ar['change_cloak'])===1) ? true : false,
										'add_news' => (intval($ar['add_news'])===1) ? true : false,
										'add_comm' => (intval($ar['add_comm'])===1) ? true : false,
										'adm_comm' => (intval($ar['adm_comm'])===1) ? true : false,
										'max_fsize' => intval($ar['max_fsize']),
										'max_ratio' => intval($ar['max_ratio'])
									);

		$this->permissoins		= json_encode($this->permissoins_ar);

		$this->balance			= (is_bool($bd_names['iconomy'])) ? 0 : floatval($ar[$bd_money['money']]);

		$this->is_auth			= true;
	}

	private function is_auth(){
		if(isset($_SESSION['user_id'])){
			return array($_SESSION['user_id'], 'id');

		}elseif(isset($_SESSION['user_name'])){
			return array($_SESSION['user_name'], 'name');

		}elseif(isset($_COOKIE['PRTCookie1'])){
			return array($_COOKIE['PRTCookie1'], 'tmp');

		}else{
			return false;
		}
	}

	private function set_unauth(){
		setcookie("PRTCookie1", "", time()-3600, '/');
		if(isset($_SESSION['user_id'])){ unset($_SESSION['user_id']); }
		if(isset($_SESSION['user_name'])){ unset($_SESSION['user_name']); }
		if(isset($_SESSION['ip'])){ unset($_SESSION['ip']); }
		return true;
	}
}

/**
 * Remote API instruments for WebMCR
 *
 * User class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.3.0
 *
 */
?>