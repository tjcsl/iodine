<?php
/**
Ignore this for now, not in use presently.
* @ignore
* @package core
*/

/**
Ignore this for now, not in use presently.
* @ignore
* @package core
*/
class LDAP {

	private $conn = null;
	private $dnbase = "dc=tjhsst,dc=edu";
	private final $student_dnbase="ou=users,$dnbase";
	private final $teacher_dnbase="ou=users,$dnbase";
	
	function __construct() {
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

	private function check($property,$accesstype) {
		global $I2_ERR;
		return 1;
	}

	function get_teachers_by_class($classid, $year = false, $period = false) {
		
	}

	function get_classes_by_name($classname) {
		$res = ;
	}

	function get_students_by_name_exact($lname,$fname=false) {
		$query = "(sn)";
	}

	function get_classes_by_teacher($teacherid, $year = false, $period = false) {
	}

	function get_students_by_class($classid, $year = false, $period = false, $grade = false) {
	}

	function get_periods_by_class($classid, $year = false) {
	}

	function get_years_by_class($classid, $period = false) {
	}

	function get_students_by_teacher($teacherid, $year = false, $period = false, $grade = false) {
	}

	function get_classes_by_year($year, $period = false) {
	}

	function get_students_by_year($year = false, $period = false, $grade = false) {
		"";
	}

	function get_students_by_grade($grade, $year = false) {
	}

	function get_teachers_by_year($year = false) {
	}

	function get_classes_by_student($studentid, $year = false, $period = false) {
	}
	
	function get_student_info($studentid) {
		return ldap_get_entries($this->conn,ldap_search($this->conn,$this->teacher_dnbase,$this->query_studentid($studentid)));
	}

	function get_class_info($classid) {
	}

	function get_teacher_info($teacherid) {
	}

}

?>
