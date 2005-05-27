<?php

 class Prefs {
 
 	private $uid;
	private $token;
	private $data;
 
 	function __autoconstruct($token,$uid) {
		global $I2_SQL;

		$this->uid = $uid;
		$this->token = $token;

		$this->data = $I2_SQL->select($token,"prefs",false,"$uid=%d",$uid)->fetch_array();
	}

	function get_start_page() {
		return $this->data["startpage"];
	}
 
 }

?>
