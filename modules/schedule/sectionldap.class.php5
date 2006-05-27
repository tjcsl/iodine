<?php
/**
*
*/

/**
*
*/
class SectionLDAP implements Section {

	private $info;
	private $teacher_dn = NULL;

	public function __construct($data) {
		global $I2_LDAP;

		d(print_r($data,1),1);

		$this->info['sectionid'] = $data['tjhsstSectionId'];
		$this->info['quarters'] = $data['quarterNumber'];
		$this->info['room'] = $data['roomNumber'];
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
		
			$this->info['students'] = array();
			foreach($data['enrolledStudent'] as $student_dn) {
				$this->info['students'][] = new User($I2_LDAP->search_base($student_dn, 'iodineUidNumber')->fetch_single_value());
				usort($this->info['students'], array('User', 'name_cmp'));
			}
		}*/
	}

	public function __get($var) {
	   if ($var == 'students') {
			return $this->get_students();
		}
		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		throw new I2Exception('Invalid attribute passed to SectionLDAP::__get(): '.$var);
	}

	public function get_students() {
		global $I2_LDAP;
		if (!isSet($this->info['students'])) {
				  $this->info['students'] = array();
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

		$res = $I2_LDAP->search('ou=schedule,dc=tjhsst,dc=edu','(&(objectClass=tjhsstClass)(sponsorDn='.$this->teacher_dn.'))', array('classPeriod','roomNumber','cn','tjhsstSectionId','quarterNumber'));
		$res->sort(array('classPeriod'));
		
		$ret = array();
		foreach($res as $row) {
			$ret[] = new SectionLDAP($row);
		}

		return $ret;
	}
}
?>
