<?php
 class UserInfo {
 	
	private $uid;
	private $token;
	private $data;
	
	function __construct($token,$uid) {
		global $I2_SQL;
		global $I2_ERR;
		
		$this->uid = $uid;
		$this->token = $token;
		
		//Select the user with uid=$uid
		$res = $I2_SQL->select($token,"users",false,"uid=%s",$uid)->fetch_array();

		if (!$res) {
			$I2_ERR->call_error("UserInfo requested for nonexistant user $uid!");
			return;
		}
		$this->data = $res;
	}

	function get_first_name() {
		return $this->data['fname'];
	}

	function get_middle_name() {
		return $this->data['mname'];
	}

	function get_last_name() {
		return $this->data['lname'];
	}

	function get_name() {
		return $this->get_first_name().$this->get_middle_name().$this->get_last_name();
	}

	function get_uid() {
		return $this->uid;
	}

	function get_schedule() {
		global $I2_SQL;
		return new Schedule($this->token,$this->uid);
	}
	
 }

?>
