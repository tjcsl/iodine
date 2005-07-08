<?php
/**
* @ignore
* @package modules
Don't know if this is necessary yet, so don't include docs for it.
*/

/**
* @ignore
* @package modules
Don't know if this is necessary yet, so don't include docs for it.
*/
 class Prefs {
 
 	private $uid;
	private $token;
	private $data;
 
 	function __autoconstruct($token,$uid) {
		global $I2_SQL;

		$this->uid = $uid;
		$this->token = $token;
		
		$this->data = $I2_SQL->query($token,'SELECT * FROM prefs WHERE uid=%d;',$uid)->fetch_array();
//		$this->data = $I2_SQL->select($token,"prefs",false,"$uid=%d",$uid)->fetch_array();
	}

 }

?>
