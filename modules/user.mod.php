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
		
		/**
		* The User class constructor.
		* 
		* @access public
		*/
		function __construct() {
				
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

	}

?>
