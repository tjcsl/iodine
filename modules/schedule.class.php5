<?php
/**
* Just contains the definition for the {@link Schedule} class.
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
class Schedule {

	private $ldap;
	
	public function __construct($username) {
		global $I2_LDAP;
		$this->ldap = $I2_LDAP;
	}

	public function set_ldap($ldap) {
		$this->ldap = $ldap;
	}

	public function add_class() {
	}

	public function remove_class($classid) {
	}

	public function add_teacher() {
	}

	public function remove_user($uname) {
		//FIXME: escape stuff
		$res = $this->ldap->search('ou=people',"iodineUid=$uname",'dn');
		if ($res->num_rows() > 1) {
			throw new I2Exception("Username '$uname' returned more than one match in remove_user!");
		}
		$dn = $res->fetch_single_value();
		d("Removing user $dn",6);
		$this->ldap->delete($dn);
	}

	public function remove_student($studentid) {
		$res = $this->ldap->search('ou=people',"tjhsstStudentId=$studentid",'dn');
		if ($res->num_rows() > 1) {
			throw new I2Exception("StudentID '$studentid' returned more than one match in remove_student!");
		}
		$dn = $res->fetch_single_value();
		d("Removing student $dn",6);
		$this->ldap->delete($dn);
		
	}

	public function get_sections($studentid) {
		//TODO: input checking
		return $this->ldap->search('ou=schedule',"(&(objectClass=tjhsstSection)(enrolledStudent=$studentid))");
	}

	public function get_class_name($classid) {
		//TODO: input checking
		$ret = $this->ldap->search('ou=schedule',"tjhsstClassId=$classid",'cn')->fetch_array(LDAP::ASSOC);
		return $ret['cn'];
	}

	public function get_section_name($sectionid) {
		$res = $this->ldap->search('ou=schedule',"tjhsstSectionId=$sectionid",'associatedClass');
		$res = $this->ldap->search_one($res->fetch_single_value(),'objectClass=*','cn');
		$ret = $res->fetch_array(LDAP::ASSOC);
		return $ret['cn'];
	}

}	
?>
