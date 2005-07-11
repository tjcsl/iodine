<?php
/**
* Just contains the definition for the class {@link User}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage User
* @filesource
*/

/**
* The user information module for Iodine.
* @package core
* @subpackage User
* @see UserInfo
* @see Schedule
*/
class User {

	private $curinfo;
	private $curuid;
	
	/**
	* The User class constructor.
	* 
	* @access public
	*/
	function __construct() {
		global $_SESSION;
		if (isSet($_SESSION['i2_uid'])) {
			$this->curuid = $_SESSION['i2_uid'];
		}
	}	

	function get_info($uid) {
		global $I2_ERR;
		
		return new UserInfo($uid);
	}

	function get_current_user() {
		return $this->curuid;
	}

	function get_current_user_info() {
		if (isSet($_SESSION['i2_uid'])) {
			$this->curuid = $_SESSION['i2_uid'];
		}
		if (!$this->curinfo) {
		 	$this->curinfo = $this->get_info($this->curuid);
		}
		return $this->curinfo;
	}

	/**
	* Returns an array of class sectionIDs. Use the Schedule class to get more info about those classes.
	*/
	function get_schedule() {
		if (!$this->curinfo) {
		 	$this->curinfo = $this->get_info($this->curuid);
		}
		return $this->curinfo->get_schedule();
	}
}

?>
