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
	private $data;
 
 	function __construct($uid) {
		global $I2_SQL;

		$this->uid = $uid;
		
		$this->data = $I2_SQL->query('SELECT * FROM prefs WHERE uid=%d;',$uid)->fetch_array();
	}

 }

?>
