<?php
/**
* Just contains the definition for the Section class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Scheduling
* @filesource
*/

/**
* The class that represents a single Section (like a period of a course)
* @package core
* @subpackage Scheduling
*/
class Section {
	private $info_arr = array();
	private $mysectionid = NULL;

	public function __construct($sectionid) {
		global $I2_SQL;
		$this->mysectionid = $sectionid;
		$this->info_arr = $I2_SQL->query('SELECT * FROM section_course_map WHERE sectionid = %d;',$sectionid)->fetch_array(RESULT::ASSOC);

		if(!$this->info_arr || count($this->info_arr) < 2) {
			throw new I2Exception('Tried to collect info on nonexistant Section: sectionid='.$sectionid);
		}
	}

	public function __get($name) {
		global $I2_SQL;
		
		switch($name) {
			case 'sectionid':	return $this->mysectionid;
			case 'name':		return $I2_SQL->query('SELECT classname FROM course_description WHERE courseid = %d;',$this->info_arr['courseid'])->fetch_single_value();
		}

		if(isset($this->info_arr[$name])) {
			return $this->info_arr[$name];
		}

		throw new I2Exception("Tried to retrieve invalid attribute $name for section {$this->mysectionid}");
	}
	
	/**
	* @return array An array of Users that are in this section.
	*/
	public function get_students() {
		global $I2_SQL;
		$ret = array();

		foreach($I2_SQL->query('SELECT uid FROM userinfo LEFT JOIN student_section_map USING (studentid) WHERE student_section_map.sectionid = %d;', $this->mysectionid) as $row) {
			$ret[] = new User($row[0]);
		}

		return $ret;
	}
}
?>
