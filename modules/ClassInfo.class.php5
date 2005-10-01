<?php
/**
* Just contains the definition for the {@link ClassInfo} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage User
* @filesource
*/

/**
* Represents a class (the kind that is taught, not the OO kind). I kinda think this should be called 'class' or 'course' or something.
* @package core
* @subpackage User
*/
class ClassInfo {
	
	private $cid;
	private $data;

	function __construct($cid) {
		global $I2_SQL;
		global $I2_ERR;
		
		$this->cid = $cid;
		
		//Select the user with uid=$uid
		$res = $I2_SQL->query('SELECT * FROM classes WHERE cid=%d;', $cid)->fetch_array();

		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class $cid!");
			return;
		}
		$this->data = $res;
	}

	function get_teachers() {
		return explode(":", $this->data['teachers']);
	}

	function get_period() {
		return $this->data['period'];
	}

	function get_length() {
		return $this->data['length'];
	}

		function get_time() {
		return $this->data['time'];
	}

	function get_name() {
		global $I2_ERR, $I2_SQL;
		$res = $I2_SQL->query('SELECT name FROM classdescriptions WHERE did=%d;', $this->data['descriptionid'])->fetch_array();
		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class description {$this->data['descriptionid']}!");
			return null;
		}
		return $res['name'];
	}

	function get_description() {
		global $I2_ERR, $I2_SQL;
		$res = $I2_SQL->query('SELECT description FROM classdescriptions WHERE did=%d;', $this->data['descriptionid'])->fetch_array();
		if (!$res) {
			$I2_ERR->call_error("ClassInfo requested for nonexistant class description {$this->['descriptionid']}!");
			return null;
		}
		return $res['description'];
	}

}

?>
