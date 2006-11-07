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
class Schedule implements Iterator {

	/**
	* An {@link LDAP} object to use for data access.
	*/
	private $ldap;
	/**
	* An array of the sectionIds in which the student is enrolled.
	*/
	private $sections;

	/**
	* The position in the schedule we are currently at
	*/
	private $index = -1;
	
	public function __construct(User $user) {
		global $I2_LDAP;
		$this->ldap = $I2_LDAP;
		$this->sections = array();
		if ($user->is_group_member('grade_staff')) {
			$res = $this->ldap->search(LDAP::get_schedule_dn(),'sponsorDN='.LDAP::get_user_dn($user),array('tjhsstSectionId','quarterNumber','roomNumber','cn','classPeriod','sponsorDN','tjhsstClassId'));
			if ($res) {
				while ($row = $res->fetch_array(Result::ASSOC)) {
					$this->sections[] = $row;
				}
				$this->index = 0;
			}
		} else {
			$res = $this->ldap->search_base(LDAP::get_user_dn($user),array('enrolledclass'))->fetch_single_value();
			if ($res) {
				foreach ($res as $classdn) {
					  $this->sections[] = $this->ldap->search_base($classdn,array('tjhsstSectionId','quarterNumber','roomNumber','cn','classPeriod','sponsorDn','tjhsstClassId'))->fetch_array(Result::ASSOC);
				}
				$this->index = 0;
			}
		}
		usort($this->sections,array($this,'periodsort'));
	}

	private function periodsort($one, $two) {
			  return $one['classPeriod']-$two['classPeriod'];
	}

	public function set_ldap($ldap) {
		$this->ldap = $ldap;
	}
	
	public static function section($sectionid) {
		global $I2_LDAP;
		$res = $I2_LDAP->search(LDAP::get_schedule_dn(),"(&(objectClass=tjhsstClass)(tjhsstSectionId=$sectionid))",array('tjhsstSectionId','cn','sponsorDn','roomNumber','quarterNumber','classPeriod','enrolledStudent','tjhsstClassId'));
		if($res->num_rows() < 1) {
			throw new I2Exception('Invalid Section ID passed to Schedule::section(): '.$sectionid);
		}
		return new SectionLDAP($res->fetch_array(Result::ASSOC));
	}

	/**
	* Returns an array of SectionIDs representing every instance of a particular class (period/teacher nonwithstanding)
	*
	* @param int $classid The tjhsstClassId number.
	* @return array An array of SectionID numbers
	*/
	public static function sections($classid) {
		global $I2_LDAP;
		$res = $I2_LDAP->search(LDAP::get_schedule_dn(),"(&(objectClass=tjhsstClass)(tjhsstClassId=$classid))",array('tjhsstSectionId'));
		if($res->num_rows() < 1) {
			throw new I2Exception('Invalid Class ID passed to Schedule::sections(): '.$classid);
		}
		$ret = array();
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$ret[] = $row['tjhsstSectionId'];
		}
		return $ret;
	}

	/**
	 * Returns an array of SectionIDs representing all class periods in existence
	 *
	 * @return array
	 */
	public static function roster() {
		global $I2_LDAP;
		$res = $I2_LDAP->search(LDAP::get_schedule_dn(),"objectClass=tjhsstClass",array('tjhsstSectionId'));
		$ret = array();
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$ret[] = $row['tjhsstSectionId'];
		}
		return $ret;
	}

	public function fill_schedule(User $user) {
		global $I2_LDAP;
		$res = $this->get_sections($user->uid);
		$this->sections = $res->fetch_all_single_values(Result::NUM);
	}

	public function add_class() {
	}

	public function remove_class($classid) {
	}

	public function add_teacher() {
	}

	public function remove_user($uname) {
		//FIXME: escape stuff
		$res = $this->ldap->search(LDAP::get_user_dn(),"iodineUid=$uname",'dn');
		if ($res->num_rows() > 1) {
			throw new I2Exception("Username '$uname' returned more than one match in remove_user!");
		}
		$dn = $res->fetch_single_value();
		d("Removing user $dn",6);
		$this->ldap->delete($dn);
	}

	public function remove_student($studentid) {
		$res = $this->ldap->search(LDAP::get_user_dn(),"tjhsstStudentId=$studentid",'dn');
		if ($res->num_rows() > 1) {
			throw new I2Exception("StudentID '$studentid' returned more than one match in remove_student!");
		}
		$dn = $res->fetch_single_value();
		d("Removing student $dn",6);
		$this->ldap->delete($dn);
		
	}

	public function get_sections($studentid) {
		$user = new User($studentid);
		return $this->ldap->search_base(LDAP::get_user_dn($user->uid),array('enrolledClass'));
	}

	public function get_class_name($classid) {
		//TODO: input checking
		$ret = $this->ldap->search(LDAP::get_schedule_dn(),"tjhsstClassId=$classid",'cn')->fetch_array(LDAP::ASSOC);
		return $ret['cn'];
	}

	public function get_section_name($sectionid) {
		$res = $this->ldap->search(LDAP::get_schedule_dn(),"tjhsstSectionId=$sectionid",'associatedClass');
		$res = $this->ldap->search_one($res->fetch_single_value(),'objectClass=*','cn');
		$ret = $res->fetch_array(LDAP::ASSOC);
		return $ret['cn'];
	}

	public function next() {
		if($this->index >= count($this->sections)) {
			return FALSE;
		}
		return new SectionLDAP($this->sections[$this->index++]);
	}

	public function prev() {
		if($this->index <= 0) {
			return FALSE;
		}
		return new SectionLDAP($this->sections[--$this->index]);
	}

	public function key() {
		return $this->index;
	}

	public function rewind() {
		$this->index = 0;
	}

	public function current() {
		if($this->index < 0 || $this->index >= count($this->sections)) {
			return FALSE;
		}
		return new SectionLDAP($this->sections[$this->index]);
	}

	public function valid() {
		return $this->current() !== FALSE;
	}
}	
?>
