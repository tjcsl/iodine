<?php
/**
*
*/

/**
*
*/
class SectionLDAP implements Section {

	private $ldap;
	private $dn;

	public function __construct($sectiondn,LDAP $ldap = NULL) {
		global $I2_LDAP;
		if (!$ldap) {
			$this->ldap = $I2_LDAP;
		} else {
			$this->ldap = ldap;
		}
		$this->dn = $sectiondn;
	}

	public function __get($name) {
		return $this->ldap->search_base($this->dn,"$name")->fetch_single_value();
	}

	public function __set($name) {
		//TODO: allow setting section attributes in LDAP
		throw new I2Exception("Attempt to modify $name in LDAP failed: unimplemented!");
	}

	public function get_students() {
	}
}
?>
