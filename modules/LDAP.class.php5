<?php

class LDAP {

	private $conn = null;
	private $dnbase = "dc=tjhsst,dc=edu";
	private final $student_dnbase="ou=users,$dnbase";
	private final $teacher_dnbase="ou=users,$dnbase";
	
	function __autoconstruct() {
		$this->conn = ldap_connect(i2config_get('server','localhost','ldap'));
		ldap_bind($this->conn,i2config_get('user','iodine','ldap'),i2config_get('password','iodine','ldap')));
	}

	function __destruct() {
		ldap_close($this->conn);
	}

#	private funtion catenate_dnbases($bases) {
#		$ret = array_shift($bases);
#		foreach ($bases as $base) {
#			$ret += ",$base";
#		}
#		return $ret;
#	}

	private function query_studentid($sid) {
		return "(tjhsstStudentId=$sid)";
	}

	private function query_teacherid($tid) {
		return "(employeeId=$tid)";
	}

	private function query_classid($cid) {
		return "()";
	}

	private function query_period($pd) {
	}

	private function query_year($yr) {
	}

	private function info_student() {
		return "";
	}

	private function info_teacher() {
		return "()";
	}

	private function search_studn

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

	function get_classes_by_name($token,$classname) {
		$res = ;
	}

	function get_students_by_name_exact($token,$lname,$fname=false) {
		$query = "(sn)";
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
		"";
	}

	function get_students_by_grade($token, $grade, $year = false) {
	}

	function get_teachers_by_year($token, $year = false) {
	}

	function get_classes_by_student($token, $studentid, $year = false, $period = false) {
	}
	
	function get_student_info($token, $studentid) {
		return ldap_get_entries($this->conn,ldap_search($this->conn,$this->teacher_dnbase,$this->query_studentid($studentid)));
	}

	function get_class_info($token, $classid) {
	}

	function get_teacher_info($token, $teacherid) {
	}

}

?>
