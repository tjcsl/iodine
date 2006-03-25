<?php
/**
* Just contains the definition for the Schedule class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Scheduling
* @filesource
*/

/**
* The class that represents a student's schedule.
* @package core
* @subpackage Scheduling
*/
class ScheduleSQL implements Iterator {
	private $class_arr = array();

	public function __construct(User $user) {
		global $I2_SQL;

		$sids = array();
		foreach($I2_SQL->query('SELECT sectionid FROM student_section_map WHERE studentid = %d;',$user->studentid) as $row) {
			$sids[] = $row[0];
		}
		$this->class_arr = SectionSQL::generate($sids);

		if(count($this->class_arr) < 1) {
			throw new I2Exception("User `{$user->uid}` does have have schedule information.");
		}

		usort($this->class_arr, array($this,'usort_compare'));
		reset($this->class_arr);
	}

	private static function usort_compare(Section $s1, Section $s2) {
		if($s1->period > $s2->period) {
			return 1;
		}
		if($s1->period < $s2->period) {
			return -1;
		}

		// periods are equal, so test the terms
		// term is like, fall term or spring term, by the way
		if($s1->term > $s2->term) {
			return 1;
		}
		if($s1->term < $s2->term) {
			return -1;
		}
		return 0;
	}

	/**
	* Required for the Iterator interface.
	*/
	public function rewind() {
		reset($this->class_arr);
	}

	/**
	* Required for the Iterator interface.
	*/
	public function current() {
		return current($this->class_arr);
	}

	/**
	* Required for the Iterator interface.
	*/
	public function key() {
		return key($this->class_arr);
	}

	/**
	* Required for the Iterator interface.
	*/
	public function next() {
		return next($this->class_arr);
	}

	/**
	* Required for the Iterator interface.
	*/
	public function valid() {
		return $this->current() !== FALSE;
	}
}
?>
