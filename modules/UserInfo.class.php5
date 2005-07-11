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
       
       function __construct($uid) {
       	global $I2_SQL, $I2_LOG;
       	
       	$this->uid = $uid;
       	
       	//Select the user with uid=$uid
	$res = $I2_SQL->query('SELECT * FROM user WHERE uid=%d;', $uid)->fetch_array();

       	if (!$res) {
       		d("UserInfo requested for nonexistant user $uid!");
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
       	return $this->get_first_name().' '.$this->get_middle_name().' '.$this->get_last_name();
       }

       function get_uid() {
       	return $this->uid;
       }

       function get_schedule() {
       	global $I2_SQL;
       	return new Schedule($this->uid);
       }

       function get_startpage() {
       	return $this->data['startpage'];
       }

       function get_bdate() {
       	return $this->data['bdate'];
       }

       function get_phone_home() {
       	return $this->data['phone_home'];
       }

       function get_phone_cell() {
       	return $this->data['phone_cell'];
       }

       function get_phone_other() {
       	return $this->data['phone_other'];
       }

       function get_address_primary_street() {
       	return $this->data['address_primary_street'];
       }

       function get_address_secondary_street() {
       	return $this->data['address_secondary_street'];
       }

       function get_address_tertiary_street() {
       	return $this->data['address_tertiary_street'];
       }

       function get_address_primary_city() {
       	return $this->data['address_primary_city'];
       }

       function get_address_secondary_city() {
       	return $this->data['address_secondary_city'];
       }

       function get_address_tertiary_city() {
       	return $this->data['address_tertiary_city'];
       }

       function get_address_primary_state() {
       	return $this->data['address_primary_state'];
       }

       function get_address_secondary_state() {
       	return $this->data['address_secondary_state'];
       }

       function get_address_tertiary_state() {
       	return $this->data['address_tertiary_state'];
       }

       function get_address_primary_zip() {
       	return $this->data['address_primary_zip'];
       }

       function get_address_secondary_zip() {
       	return $this->data['address_secondary_zip'];
       }

       function get_address_tertiary_zip() {
       	return $this->data['address_tertiary_zip'];
       }

       function get_sn($servicename) {
       	return $this->data['sn#'];
       }

       function get_sex() {
       	return $this->data['sex'];
       }

       function get_grade() {
       	return $this->data['grade'];
       }

       function get_locker() {
       	return $this->data['locker'];
       }

       function get_webpage() {
       	return $this->data['webpage'];
       }

       function get_counselor() {
       	return $this->data['counselor'];
       }

       function get_email($emailname) {
       	return $this->data['email#'];
       }

       function get_picture($year) {
       	return $this->data['picture'.($year - 9)];
       }

}

?>
