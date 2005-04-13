<?php

class LDAP {

	function __autoconstruct() {
	}

	private function check($token,$property,$accesstype) {
		global $I2_ERR;
		if (!check_token_rights($token,$property,$accesstype)) {
			$I2_ERR->call_error("Invalid token used when attempting to access LDAP property $property [$accesstype]!");
			return 0;
		}
		return 1;
	}

	function get_teachers_by_class($token, $classid, $year = false, $period = false) {
	}

	function get_classes_by_teacher($token, $teacherid, $year = false, $period = false) {
	}

	function get_students_by_class($token, $classid, $year = false, $period = false, $grade = false) {
	}

	function get_periods_by_class($token, $classid, $year = false) {
	}

	function get_years_by_class($token, $classid, $period = false) {
	}

	function get_students_by_teacher($token, $teacherid, $year = false, $period = false, $grade = false) {
	}

	function get_classes_by_year($token, $year, $period = false) {
	}

	function get_students_by_year($token, $year = false, $period = false, $grade = false) {
	}

	function get_students_by_grade($token, $grade, $year = false) {
	}

	function get_teachers_by_year($token, $year = false) {
	}

	function get_classes_by_student($token, $studentid, $year = false, $period = false) {
	}
	
	function get_student_info($token, $studentid) {
	}

	function get_class_info($token, $classid) {
	}

	function get_teacher_info($token, $teacherid) {
	}

}

?>
