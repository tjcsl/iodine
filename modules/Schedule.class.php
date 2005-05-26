<?php

 class Schedule {
 	
	private $uid;
	private $token;
	private $data;
	private $classes = false;
	
	function __autoconstruct($token,$uid) {
		global $I2_SQL;
		
		$this->uid = $uid;
		$this->token = $token;

		$this->data = $I2_SQL->select($token,"schedules",false,"uid=%d",$uid)->fetch_array();
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
