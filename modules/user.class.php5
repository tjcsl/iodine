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

	function get_info(Token $token,$uid) {
		global $I2_ERR;
		
		return new UserInfo($token,$uid);
	}

	function get_current_user() {
		return $this->curuid;
	}

	function get_current_user_info(Token $token) {
		if (isSet($_SESSION['i2_uid'])) {
			$this->curuid = $_SESSION['i2_uid'];
		}
		if (!$this->curinfo) {
		 	$this->curinfo = $this->get_info($token,$this->curuid);
		}
		return $this->curinfo;
	}

	/**
	* Returns an array of class sectionIDs. Use the Schedule class to get more info about those classes.
	*/
	function get_schedule(Token $token) {
		if (!$this->curinfo) {
		 	$this->curinfo = $this->get_info($token,$this->curuid);
		}
		return $this->curinfo->get_schedule($token);
	}

	function get_desired_boxes(Token $token) {
		global $I2_SQL;
		$res = $I2_SQL->query($token, 'SELECT boxes FROM userinfo WHERE uid=%d;', $this->curuid);
		$arr = $res->fetch_array();
		if($arr['boxes'])
			return explode(',',$arr['boxes']);
		return array();
	}

	function get_users_with_birthday(Token $token, $date) {
		global $I2_SQL;
		/* date in format YYYY-MM-DD
		 * extract the month/day and year components for databse query
		 * and age determination.
		 */
		$day = substr($date,-5);
		$thisyear = substr($date,0,4);
		/* Can't implement this in new query() style because I can't tell wth is does */
		//$res = $I2_SQL->select($token,'users',array('fname','lname','bdate','grade'),"bdate='1988-06-21'",array($day),array(array(false,'grade'),array(false,'lname')));
		$ret = array();
		while ($row = $res->fetch_array()) {
			$byear = substr($row['bdate'],0,4);
			$ret[] = array($row['fname'].' '.$row['lname'],$row['grade'],$thisyear - $byear);
		}
		return $ret;
	}

}

?>
