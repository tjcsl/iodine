<?php
/**
* Just contains the definition for the {@link Section} {@link SectionLDAP}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package core
* @subpackage Scheduling
* @filesource
*/

/**
* A class that represents a single Section that is stored in LDAP.
*
* For data available from Section objects, see the {@link __get} method.
*
* @package core
* @subpackage Scheduling
*/
class SectionLDAP implements Section {

	private $info;
	private $teacher_dn = NULL;

	public function __construct($data) {
		global $I2_LDAP;

		$this->info['sectionid'] = $data['tjhsstSectionId'];
		$this->info['classid'] = $data['tjhsstClassId'];
		$this->info['quarters'] = $data['quarterNumber'];
		$this->info['room'] = isset($data['roomNumber']) ? $data['roomNumber'] : "";
		$this->info['name'] = $data['cn'];
		$this->info['period'] = $data['classPeriod'];

		if(isset($data['sponsorDn'])) {
			$this->info['teacher'] = new User($I2_LDAP->search_base($data['sponsorDn'], 'iodineUidNumber')->fetch_single_value());
			$this->teacher_dn = $data['sponsorDn'];
		}

		/*if(isset($data['enrolledStudent'])) {
			if(!is_array($data['enrolledStudent'])) {
				$data['enrolledStudent'] = array($data['enrolledStudent']);
			}
		
			$this->info['students'] = [];
			foreach($data['enrolledStudent'] as $student_dn) {
				$this->info['students'][] = new User($I2_LDAP->search_base($student_dn, 'iodineUidNumber')->fetch_single_value());
				usort($this->info['students'], array('User', 'name_cmp'));
			}
		}*/
	}

	/**
	* The php magical __get method
	*
	* Accessing $section->thing calls this method. Things you can legitimately
	* access this way are:
	* <ul>
	*  <li>sectionid</li>
	*  <li>classid</li>
	*  <li>quarters -- may be array or single value</li>
	*  <li>room</li>
	*  <li>name</li>
	*  <li>period -- may be array or single value</li>
	*  <li>term -- a string of quarters joined by ", "</li>
	*  <li>periods -- a string of periods joined by ", "</li>
	*  <li>students</li>
	* </ul>
	*/
	public function __get($var) {
		if ($var == 'students') {
			return $this->get_students();
		}
		if ($var == 'term') {
			if(is_array($this->info['quarters']))
				return @implode(', ', $this->info['quarters']);
			else
				return $this->info['quarters'];
		}
		if ($var == 'periods') {
			if(is_array($this->info['period']))
				return @implode(', ', $this->info['period']);
			else
				return $this->info['period'];
		}
		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		throw new I2Exception('Invalid attribute passed to SectionLDAP::__get(): '.$var);
	}

	public function get_students() {
		global $I2_LDAP;
		if (!isSet($this->info['students'])) {
				  $this->info['students'] = [];
				  $res = $I2_LDAP->search(LDAP::get_user_dn(),'enrolledClass='.LDAP::get_schedule_dn($this->info['sectionid']),array('iodineUid'));
				  while ($row = $res->fetch_array(Result::ASSOC)) {
							 $uid = $row['iodineUid'];
							 $this->info['students'][] = new User($uid);
				  }
				  usort($this->info['students'], array('User', 'name_cmp'));
		}
		return $this->info['students'];
	}

	/**
	* @returns Array An array of SectionLDAP objects which represent the other classes taught by the same teacher as this one
	*/
	public function other_classes() {
		global $I2_LDAP;
		if($this->teacher_dn === NULL) {
			throw new I2Exception('Tried to get the other classes from a Section that does not have teacher data! Sectionid: '.$this->sectionid);
		}

		$res = $I2_LDAP->search('ou=schedule,dc=tjhsst,dc=edu','(&(objectClass=tjhsstClass)(sponsorDn='.$this->teacher_dn.'))', array('classPeriod','roomNumber','cn','tjhsstSectionId','quarterNumber','tjhsstClassId'));
		$res->sort(array('classPeriod'));
		
		$ret = [];
		foreach($res as $row) {
			$ret[] = new SectionLDAP($row);
		}

		return $ret;
	}
}
?>
