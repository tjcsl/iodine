<?php
/**
* Just contains the definition for the class {@link Schedule}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage User
*/

/**
* The class for a student's schedule for Iodine.
* @package core
* @subpackage User
*/
class Schedule {
	
       private $uid;
       private $token;
       private $data;
       private $classes = false;
       
       function __construct($token,$uid) {
       	global $I2_SQL;
       	
       	$this->uid = $uid;
       	$this->token = $token;

	$this->data = $I2_SQL->query($token, 'SELECT * FROM schedules WHERE uid=%d;', $uid)->fetch_array();
//       	$this->data = $I2_SQL->select($token,"schedules",false,"uid=%d",$uid)->fetch_array();
       }

       function get_period($periodnum) {
       	return $this->data["period".$periodnum];
       }

       function get_classes() {
       	if ($classes) {
       		return $classes;
       	}
       	$this->classes = array();
       	for ($a = 1; $a <= 7; $a++) {
       		//Push a blank on to make periods line up with indices.
       		$this->classes[] = false;
       		$this->classes[] = $this->get_period($a);
       	}
       	return $this->classes;
       }

       function get_time($classid) {
       	$classes = $this->get_classes();
       	$time = 0;
       	foreach ($classes as $time=>$class) {
       		if ($class == $classid) {
       			return $time;
       		}
       	}
       	return false;
       }
       
}

?>
