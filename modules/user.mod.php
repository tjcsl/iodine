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
			global $I2_SQL;
			//TODO: get the current user (name? studentid?) and put it in $this->curuser. Let's assume StudentID, it is more useful
			//I hope we will be using session variables
			//$this->cursuser = $_SESSION['StudentID'];
			
			/*TODO: pick table name, pass actual auth token, pick selector, format results
			**These are the tables that hold the relevant information as of now. StudentInfo for addresses, name, stuff like that
			**and StudentMiscInfo for preferences. The prefs table name must change as well as the fields, but StudentInfo is fine.
			**$this->info = $I2_SQL->select($token,'StudentInfo',,'StudentID=%d',array($this->curuser));
			**$this->prefs = $I2_SQL->select($token,'StudentMiscInfo',,'StudentID=%d',array($this->curuser));
			*/
			//note from braujac:  This is looking like it'll be LDAP at this point, not SQL.  We can have private LDAP access methods
			// in this class, and call them here.
			

			//TODO: How do I got schedule?
		}
		
		/**
		* Gets a tidbit of info about the user.
		*
		* @param string $token An access token with read rights to the passed field.
		* @param string $field The field to get the value of.
		*/
		function get_info($token, $field) {
			if (!check_token_rights($token,'info/'.$field,'r')) {
				$I2_ERR->call_error("Bad authentication token to get $field!");
				return null;
			}
			if (isSet($this->info[$field])) {
				return $this->info[$field];
			} else {
				$I2_LOG->log_debug("Access of undefined field $field in User module.");	
			}
			return null;
		}

		/**
		* Gets a user preference.
		*
		* @param string $field The preference name whose value you want.
		* @param string $token An access token with read rights to the given preference.
		*/
		function get_pref($token, $field) {
			if (!check_token_rights($token,'pref/'.$field,'r')) {
				$I2_ERR->call_error("Bad authentication token to get preference $field!");
				return null;
			}
			if (isSet($this->prefs[$field])) {
				return $this->prefs[$field];
			} else {
				$I2_LOG->log_debug("Access of undefined preference $field in User module.");
			}
			return null;
		}

		/**
		* Returns an array of class sectionIDs. Use the Schedule class to get more info about those classes.
		*/
		function get_sched() {
			return $this->schedule;
		}


		/**
		* Sets a tidbit of user info.
		*
		* @param string $token An authorization token to check privledges.
		* @param string $name The name of the info bit to set.
		* @param mixed $value The value to set the field to.
		*/
		function set_info($token, $name, $value) {
			if (!check_token_rights($token,'info/'.$name,'w')) {
				$I2_ERR->call_error("Bad authentication token to set $name!"); 
			}
			$this->info[$name] = $value;
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
			if (!check_token_rights($token,'pref/'.$name,'w')) {
				$I2_ERR->call_error("Bad authentication token to set preference $name!"); 
			}
			$this->prefs[$name] = $value;
			//FIXME: update the database to make the change persist.
		}

	}

?>
