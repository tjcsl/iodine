<?php
	/**
	* The user information module for Iodine.
	* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
	* @copyright 2004 The Intranet 2 Development Team
	* @version 1.0
	* @since 1.0
	* @package user
	*/
	
	class User {

		private $info = array();
		private $prefs = array();
		private $schedule;
		private $curuser;
		
		/**
		* The User class constructor.
		* 
		* @access public
		*/
		function __construct() {
			//TODO: get the current user and put it in $this->curuser.
		}
		
		/**
		* Gets a tidbit of info about the user.
		*
		* @param string $field The field to get the value of.
		*/
		function get_info($field) {
			if (isSet($info[$field])) {
				return $info[$field];
			} else {
				$I2_LOG->log_debug("Access of undefined field $field in User module.");	
			}
			return null;
		}

		/**
		* Gets a user preference.
		*
		* @param string $field The preference name whose value you want.
		*/
		function get_pref($field) {
			if (isSet($prefs[$field])) {
			} else {
				$I2_LOG->log_debug("Access of undefined preference $field in User module.");
			}
			return null;
		}

		/**
		* Returns an array of class sectionIDs. Use the Schedule class to get more info about those classes.
		*/
		function get_sched() {
			return $schedule;
		}


		/**
		* Sets a tidbit of user info.
		*
		* @param string $token An authorization token to check privledges.
		* @param string $name The name of the info bit to set.
		* @param mixed $value The value to set the field to.
		*/
		function set_info($token, $name, $value) {
			//FIXME: check $token.
			// if (!check_token_rights($token,$name,'w')) {$I2_ERR->call_error("Bad authentication token to set $name!"); }
			$info[$name] = $value;
			//FIXME: update the database to make the change persist.
		}

		/**
		* Sets a user preference.
		*
		* @param string $token An authorization token to check privledges.
		* @param string $name The name of the preference to set.
		* @param mixed $value The value to set the preference to.
		*/
		function set_pref($token, $name, $value) {
			//FIXME:  As set_info.
		}

	}

?>
