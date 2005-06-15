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

		function get_info($token,$uid) {
			global $I2_ERR;
			//This isn't necessary because UserInfo checks access rights.
			/*if (!check_token_rights($token,"info/".$this->uid)) {
				$I2_ERR->nonfatal_error("Could not get user information");
			}*/
			
			return new UserInfo($token,$uid);
		}

		function get_current_user() {
			return $this->curuid;
		}

		function get_current_user_info($token) {
			if (!$this->curinfo) {
			 	$this->curinfo = $this->get_info($token,$this->curuid);
			}
			return $this->curinfo;
		}

		/**
		* Returns an array of class sectionIDs. Use the Schedule class to get more info about those classes.
		*/
		function get_schedule($token) {
			if (!$this->curinfo) {
			 	$this->curinfo = $this->get_info($token,$this->curuid);
			}
			return $this->curinfo->get_schedule($token);
		}

		function get_desired_boxes($token) {
			global $I2_SQL;
			$res = $I2_SQL->select($token,'users','boxes','uid=%d',$this->curuid);
			$arr = $res->fetch_array();
			return explode(',',$arr['boxes']);
		}

		function get_users_with_birthday($token, $date) {
			global $I2_SQL;
			/* date in format YYYY-MM-DD
			 * extract the month/day and year components for databse query
			 * and age determination.
			 */
			$day = substr($date,-5);
			$thisyear = substr($date,4);
			$res = $I2_SQL->select($token,'users',array('fname','lname','bdate','grade'),'bdate LIKE "\%-%s"',array($day),array(array('','grade'),array('','lname')));
			$ret = array();
			while ($row = $res->fetch_array()) {
				$byear = substr($row['grade'],4);
				$ret[] = array($row['fname'].' '.$row['lname'],$thisyear - $byear);
			}
			return $ret;
		}

	}

?>
