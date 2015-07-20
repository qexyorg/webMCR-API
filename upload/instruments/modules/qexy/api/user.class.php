<?php
/**
 * API instruments for WebMCR
 *
 * User class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */

if(!defined('QEXY_API')){ exit("Hacking Attempt!"); }

class api_user{
	public $lvl, $login, $id, $ip, $email, $group, $money, $isOnline, $mcrUser;

	public function __construct($user=false, $money=0){

		$this->lvl		= (empty($user)) ? -1 : intval($user->lvl());

		$this->login	= (empty($user)) ? false : $user->name();

		$this->id		= (empty($user)) ? false : $user->id();

		$this->email	= (empty($user)) ? false : $user->email();

		$this->group	= (empty($user)) ? false : $user->group();
		
		$this->money	= $money;

		$this->isOnline	= (empty($user)) ? false : true;

		$this->mcrUser	= $user;
	}

}

$api_money  = (isset($player_money)) ? $player_money : 0;

$api_user = new api_user($user, $api_money);

/**
 * API instruments for WebMCR
 *
 * User class
 * 
 * @author Qexy.org (admin@qexy.org)
 *
 * @copyright Copyright (c) 2015 Qexy.org
 *
 * @version 1.2.0
 *
 */
?>