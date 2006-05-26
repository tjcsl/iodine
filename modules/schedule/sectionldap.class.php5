<?php
/**
*
*/

/**
*
*/
class SectionLDAP implements Section {

	private $info;

	public function __construct($data) {
		global $I2_LDAP;
	
		$this->info['sectionid'] = $data['tjhsstSectionId'];
		$this->info['quarters'] = $data['quarterNumber'];
		$this->info['room'] = $data['roomNumber'];
		$this->info['name'] = $data['cn'];
		$this->info['period'] = $data['classPeriod'];

		if(isset($data['sponsorDn'])) {
			$this->info['teacher'] = new User($I2_LDAP->search_base($data['sponsorDn'], 'iodineUidNumber')->fetch_single_value());
		}

		if(isset($data['enrolledStudent'])) {
			if(!is_array($data['enrolledStudent'])) {
				$data['enrolledStudent'] = array($data['enrolledStudent']);
			}
		
			$this->info['students'] = array();
			foreach($data['enrolledStudent'] as $student_dn) {
				$this->info['students'][] = new User($I2_LDAP->search_base($student_dn, 'iodineUidNumber')->fetch_single_value());
			}
		}
	}

	public function __get($var) {
		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		throw new I2Exception('Invalid attribute passed to SectionLDAP::__get(): '.$var);
	}

	public function get_students() {
		return $this->info['students'];
	}
}
?>
