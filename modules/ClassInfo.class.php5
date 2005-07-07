<?php
/**
* Just contains the definition for the {@link ClassInfo} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage User
*/

/**
* Represents a class (the kind that is taught, not the OO kind). I kinda think this should be called 'class' or 'course' or something.
* @package core
* @subpackage User
*/
class ClassInfo {
	
	private $cid;
	private $data;

	function __construct($token,$cid) {
		global $I2_SQL;
		global $I2_ERR;
		
		$this->cid = $cid;
		
		//Not necessary b/c MySQL will check the access rights.
		/*if (!check_token_rights($token,"info/".$uid,'r')) {
			$I2_ERR->nonfatal_error("Unable to get user information for user $uid : invalid token!");
			return;
		}*/
		
		//Select the user with uid=$uid
		$res = $I2_SQL->select($token,'classes',false,'cid=%s',$cid)->fetch_array();

		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class $cid!");
			return;
		}
		$this->data = $res;
	}

	function check_token($token,$field) {
		if (!check_token_rights($token,'info/classes-'.$this->cid.'-'.$field,'r')) {
			$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the $field info field of class ".$this->cid);
			return false;
		}
		return true;
	}

	function get_teachers($token) {
		if(!check_token($token,'teachers')) return null;
		return explode(":", $this->data['teachers']);
	}

	function get_period($token) {
		if(!check_token($token,'period')) return null;
		return $this->data['period'];
	}

	function get_length($token) {
		if(!check_token($token,'length')) return null;
		return $this->data['length'];
	}

	function get_time($token) {
		if(!check_token($token,'time')) return null;
		return $this->data['time'];
	}

	function get_name($token) {
		global $I2_ERR, $I2_SQL;
		if(!check_token($token,'name')) return null;
		if (!check_token_rights($token,'info/classdescriptions-'.$this->data['descriptionid'].'-name','r')) {
			$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the name info field of class description ".$this->data['descriptionid']);
			return null;
		}
		$res = $I2_SQL->select($token,'classdescriptions','name','did=%d',$this->data['descriptionid'])->fetch_array();
		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class description {$this->data['descriptionid']}!");
			return null;
		}
		return $res['name'];
	}

	function get_description($token) {
		global $I2_ERR, $I2_SQL;
		if(!check_token($token,'description')) return null;
		if (!check_token_rights($token,'info/classdescriptions-'.$this->data['descriptionid'].'-description','r')) {
			$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the description info field of class description ".$this->data['descriptionid']);
			return null;
		}
		$res = $I2_SQL->select($token,'classdescriptions','description','did=%d',$this->data['descriptionid'])->fetch_array();
		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class description {$this->['descriptionid']}!");
			return null;
		}
		return $res['description'];
	}

}

?>
