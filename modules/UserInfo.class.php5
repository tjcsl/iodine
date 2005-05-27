<?php

 class UserInfo {
 	
	private $uid;
	private $data;
	
	function __construct($token,$uid) {
		global $I2_SQL;
		global $I2_ERR;
		
		$this->uid = $uid;
		
		//Not necessary b/c MySQL will check the access rights.
		/*if (!check_token_rights($token,"info/".$uid,'r')) {
			$I2_ERR->nonfatal_error("Unable to get user information for user $uid : invalid token!");
			return;
		}*/
		
		//Select the user with uid=$uid
		$res = $I2_SQL->select($token,"users",false,"uid=%s",$uid)->fetch_array();

		if (!$res) {
			$I2_ERR->call_error("UserInfo requested for nonexistant user $uid!");
			return;
		}
		$this->data = $res;
	}

	function check_token($token,$field) {
		global $I2_ERR;
		if (!check_token_rights($token,'info/'.$this->uid.'-'.$field,'r')) {
			$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the $field info field of user".$this->uid);
			return false;
		}
		return true;
	}

	function get_first_name($token) {
		if (!check_token($token,'fname')) return null;
		return $this->data['fname'];
	}

	function get_middle_name($token) {
		if (!check_token($token,'mname')) return null;
		return $this->data['mname'];
	}

	function get_last_name($token) {
		if (!check_token($token,'lname')) return null;
		return $this->data['lname'];
	}

	function get_name($token) {
		if (!check_token($token,'name')) return null;
		return $this->get_first_name().$this->get_middle_name().$this->get_last_name();
	}

	function get_uid($token) {
		return $this->uid;
	}

	function get_schedule($token) {
		global $I2_SQL;
		if (!check_token($token,'schedule')) return null;
		return new Schedule($this->token,$this->uid);
	}

	function get_startpage($token) {
		if (!check_token($token,'startpage')) return null;
		return $this->data['startpage'];
	}
	
 }

?>
