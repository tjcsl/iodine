<?php
/**
* Just contains the definition for the class {@link UserInfo}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage User
* @filesource
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
       
       function __construct(Token $token,$uid) {
       	global $I2_SQL, $I2_LOG;
       	
       	$this->uid = $uid;
       	
       	//Not necessary b/c MySQL will check the access rights.
       	/*if (!$this->check_token_rights($token,"info/".$uid,'r')) {
       		$I2_ERR->nonfatal_error("Unable to get user information for user $uid : invalid token!");
       		return;
       	}*/
       	
       	//Select the user with uid=$uid
	$res = $I2_SQL->query($token, 'SELECT * FROM user WHERE uid=%d;', $uid)->fetch_array();
//      $res = $I2_SQL->select($token,'users',false,'uid=%s',array($uid))->fetch_array();

       	if (!$res) {
       		d("UserInfo requested for nonexistant user $uid!");
       		return;
       	}
       	$this->data = $res;
       }

       function check_token(Token $token,$field) {
       	global $I2_ERR;
       	if (!$token->check_rights('info/students-'.$this->uid.'-'.$field,'r')) {
       		$I2_ERR->nonfatal_error("An invalid access token was used in attempting to access the $field info field of user ".$this->uid);
       		return false;
       	}
       	return true;
       }

       function get_first_name(Token $token) {
       	if (!$this->check_token($token,'fname')) return null;
       	return $this->data['fname'];
       }

       function get_middle_name(Token $token) {
       	if (!$this->check_token($token,'mname')) return null;
       	return $this->data['mname'];
       }

       function get_last_name(Token $token) {
       	if (!$this->check_token($token,'lname')) return null;
       	return $this->data['lname'];
       }

       function get_name(Token $token) {
       	if (!$this->check_token($token,'name')) return null;
       	return $this->get_first_name().' '.$this->get_middle_name().' '.$this->get_last_name();
       }

       function get_uid(Token $token) {
       	return $this->uid;
       }

       function get_schedule(Token $token) {
       	global $I2_SQL;
       	if (!$this->check_token($token,'schedule')) return null;
       	return new Schedule($this->token,$this->uid);
       }

       function get_startpage(Token $token) {
       	if (!$this->check_token($token,'startpage')) return null;
       	return $this->data['startpage'];
       }

       function get_bdate(Token $token) {
       	if (!$this->check_token($token,'bdate')) return null;
       	return $this->data['bdate'];
       }

       function get_phone_home(Token $token) {
       	if (!$this->check_token($token,'phone_home')) return null;
       	return $this->data['phone_home'];
       }

       function get_phone_cell(Token $token) {
       	if (!$this->check_token($token,'phone_cell')) return null;
       	return $this->data['phone_cell'];
       }

       function get_phone_other(Token $token) {
       	if (!$this->check_token($token,'phone_other')) return null;
       	return $this->data['phone_other'];
       }

       function get_address_primary_street(Token $token) {
       	if (!$this->check_token($token,'address_primary_street')) return null;
       	return $this->data['address_primary_street'];
       }

       function get_address_secondary_street(Token $token) {
       	if (!$this->check_token($token,'address_secondary_street')) return null;
       	return $this->data['address_secondary_street'];
       }

       function get_address_tertiary_street(Token $token) {
       	if (!$this->check_token($token,'address_tertiary_street')) return null;
       	return $this->data['address_tertiary_street'];
       }

       function get_address_primary_city(Token $token) {
       	if (!$this->check_token($token,'address_primary_city')) return null;
       	return $this->data['address_primary_city'];
       }

       function get_address_secondary_city(Token $token) {
       	if (!$this->check_token($token,'address_secondary_city')) return null;
       	return $this->data['address_secondary_city'];
       }

       function get_address_tertiary_city(Token $token) {
       	if (!$this->check_token($token,'address_tertiary_city')) return null;
       	return $this->data['address_tertiary_city'];
       }

       function get_address_primary_state(Token $token) {
       	if (!$this->check_token($token,'address_primary_state')) return null;
       	return $this->data['address_primary_state'];
       }

       function get_address_secondary_state(Token $token) {
       	if (!$this->check_token($token,'address_secondary_state')) return null;
       	return $this->data['address_secondary_state'];
       }

       function get_address_tertiary_state(Token $token) {
       	if (!$this->check_token($token,'address_tertiary_state')) return null;
       	return $this->data['address_tertiary_state'];
       }

       function get_address_primary_zip(Token $token) {
       	if (!$this->check_token($token,'address_primary_zip')) return null;
       	return $this->data['address_primary_zip'];
       }

       function get_address_secondary_zip(Token $token) {
       	if (!$this->check_token($token,'address_secondary_zip')) return null;
       	return $this->data['address_secondary_zip'];
       }

       function get_address_tertiary_zip(Token $token) {
       	if (!$this->check_token($token,'address_tertiary_zip')) return null;
       	return $this->data['address_tertiary_zip'];
       }

       function get_sn(Token $token,$servicename) {
       	if (!$this->check_token($token,'sn#')) return null; // TODO: Deal w/ #
       	return $this->data['sn#'];
       }

       function get_sex(Token $token) {
       	if (!$this->check_token($token,'sex')) return null;
       	return $this->data['sex'];
       }

       function get_grade(Token $token) {
       	if (!$this->check_token($token,'grade')) return null;
       	return $this->data['grade'];
       }

       function get_locker(Token $token) {
       	if (!$this->check_token($token,'locker')) return null;
       	return $this->data['locker'];
       }

       function get_webpage(Token $token) {
       	if (!$this->check_token($token,'webpage')) return null;
       	return $this->data['webpage'];
       }

       function get_counselor(Token $token) {
       	if (!$this->check_token($token,'counselor')) return null;
       	return $this->data['counselor'];
       }

       function get_email(Token $token,$emailname) {
       	if (!$this->check_token($token,'email#')) return null; // TODO: Deal w/ #
       	return $this->data['email#'];
       }

       function get_picture(Token $token,$year) {
       	if (!$this->check_token($token,'picture'.($year - 9))) return null;
       	return $this->data['picture'.($year - 9)];
       }

}

?>
