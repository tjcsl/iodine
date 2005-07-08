<?php
/**
* Just contains the definition for the class {@link UserInfo}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage User
*/

/**
* The user information class for Iodine.
* @package core
* @subpackage User
* @see User
*/
class UserInfo {
	
       private $uid;
       private $data;
       
       function __construct($token,$uid) {
       	global $I2_SQL, $I2_LOG;
       	
       	$this->uid = $uid;
       	
       	//Not necessary b/c MySQL will check the access rights.
       	/*if (!$this->check_token_rights($token,"info/".$uid,'r')) {
       		$I2_ERR->nonfatal_error("Unable to get user information for user $uid : invalid token!");
       		return;
       	}*/
       	
       	//Select the user with uid=$uid
	$res = $I2_SQL->query($token, 'SELECT * FROM users WHERE uid=%d;', $uid)->fetch_array();
//      $res = $I2_SQL->select($token,'users',false,'uid=%s',array($uid))->fetch_array();

       	if (!$res) {
       		$I2_LOG->log_debug("UserInfo requested for nonexistant user $uid!");
       		return;
       	}
       	$this->data = $res;
       }

       function check_token($token,$field) {
       	global $I2_ERR;
       	if (!check_token_rights($token,'info/students-'.$this->uid.'-'.$field,'r')) {
       		$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the $field info field of user ".$this->uid);
       		return false;
       	}
       	return true;
       }

       function get_first_name($token) {
       	if (!$this->check_token($token,'fname')) return null;
       	return $this->data['fname'];
       }

       function get_middle_name($token) {
       	if (!$this->check_token($token,'mname')) return null;
       	return $this->data['mname'];
       }

       function get_last_name($token) {
       	if (!$this->check_token($token,'lname')) return null;
       	return $this->data['lname'];
       }

       function get_name($token) {
       	if (!$this->check_token($token,'name')) return null;
       	return $this->get_first_name().' '.$this->get_middle_name().' '.$this->get_last_name();
       }

       function get_uid($token) {
       	return $this->uid;
       }

       function get_schedule($token) {
       	global $I2_SQL;
       	if (!$this->check_token($token,'schedule')) return null;
       	return new Schedule($this->token,$this->uid);
       }

       function get_startpage($token) {
       	if (!$this->check_token($token,'startpage')) return null;
       	return $this->data['startpage'];
       }

       function get_bdate($token) {
       	if (!$this->check_token($token,'bdate')) return null;
       	return $this->data['bdate'];
       }

       function get_phone_home($token) {
       	if (!$this->check_token($token,'phone_home')) return null;
       	return $this->data['phone_home'];
       }

       function get_phone_cell($token) {
       	if (!$this->check_token($token,'phone_cell')) return null;
       	return $this->data['phone_cell'];
       }

       function get_phone_other($token) {
       	if (!$this->check_token($token,'phone_other')) return null;
       	return $this->data['phone_other'];
       }

       function get_address_primary_street($token) {
       	if (!$this->check_token($token,'address_primary_street')) return null;
       	return $this->data['address_primary_street'];
       }

       function get_address_secondary_street($token) {
       	if (!$this->check_token($token,'address_secondary_street')) return null;
       	return $this->data['address_secondary_street'];
       }

       function get_address_tertiary_street($token) {
       	if (!$this->check_token($token,'address_tertiary_street')) return null;
       	return $this->data['address_tertiary_street'];
       }

       function get_address_primary_city($token) {
       	if (!$this->check_token($token,'address_primary_city')) return null;
       	return $this->data['address_primary_city'];
       }

       function get_address_secondary_city($token) {
       	if (!$this->check_token($token,'address_secondary_city')) return null;
       	return $this->data['address_secondary_city'];
       }

       function get_address_tertiary_city($token) {
       	if (!$this->check_token($token,'address_tertiary_city')) return null;
       	return $this->data['address_tertiary_city'];
       }

       function get_address_primary_state($token) {
       	if (!$this->check_token($token,'address_primary_state')) return null;
       	return $this->data['address_primary_state'];
       }

       function get_address_secondary_state($token) {
       	if (!$this->check_token($token,'address_secondary_state')) return null;
       	return $this->data['address_secondary_state'];
       }

       function get_address_tertiary_state($token) {
       	if (!$this->check_token($token,'address_tertiary_state')) return null;
       	return $this->data['address_tertiary_state'];
       }

       function get_address_primary_zip($token) {
       	if (!$this->check_token($token,'address_primary_zip')) return null;
       	return $this->data['address_primary_zip'];
       }

       function get_address_secondary_zip($token) {
       	if (!$this->check_token($token,'address_secondary_zip')) return null;
       	return $this->data['address_secondary_zip'];
       }

       function get_address_tertiary_zip($token) {
       	if (!$this->check_token($token,'address_tertiary_zip')) return null;
       	return $this->data['address_tertiary_zip'];
       }

       function get_sn($token,$servicename) {
       	if (!$this->check_token($token,'sn#')) return null; // TODO: Deal w/ #
       	return $this->data['sn#'];
       }

       function get_sex($token) {
       	if (!$this->check_token($token,'sex')) return null;
       	return $this->data['sex'];
       }

       function get_grade($token) {
       	if (!$this->check_token($token,'grade')) return null;
       	return $this->data['grade'];
       }

       function get_locker($token) {
       	if (!$this->check_token($token,'locker')) return null;
       	return $this->data['locker'];
       }

       function get_webpage($token) {
       	if (!$this->check_token($token,'webpage')) return null;
       	return $this->data['webpage'];
       }

       function get_counselor($token) {
       	if (!$this->check_token($token,'counselor')) return null;
       	return $this->data['counselor'];
       }

       function get_email($token,$emailname) {
       	if (!$this->check_token($token,'email#')) return null; // TODO: Deal w/ #
       	return $this->data['email#'];
       }

       function get_picture($token,$year) {
       	if (!$this->check_token($token,'picture'.($year - 9))) return null;
       	return $this->data['picture'.($year - 9)];
       }

}

?>
