<?php
/**
* Just contains the definition for the {@link LDAPResult} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Database
* @filesource
*/

/**
* An implementation of {@link Result} to represent the result of an LDAP query.
* @package core
* @subpackage Database
*/

class LDAPResult implements Result {
	
	private $ldap = NULL;
	
	private $ldap_result = NULL;

	private $query_type = NULL;

	private $current_row = NULL;

	private $current_row_number = 0;

	public function __construct($ldap,$result,$type) {
		$this->ldap = $ldap;
		$this->ldap_result = $result;
		$this->query_type = $type;
	}

	public function __destruct() {
		ldap_free_result($this->ldap_result);
	}
	
	public function fetch_array($type=Result::BOTH) {
	
		if (!$this->query_type=LDAP::LDAP_SEARCH) {
			throw new I2Exception("A resultset array cannot be fetched from a non-SEARCH LDAP query!");
		}
	
		if ($this->current_row == NULL) {
			$this->current_row = $this->get_first_row();
		} else {
			$this->current_row = $this->get_next_row($this->current_row);
		}

		if (!$this->current_row) {
			return FALSE;
		}

		$this->current_row_number++;

		return $this->extract_data($this->current_row,$type);

	}

	private function get_first_row() {
		return ldap_first_entry($this->ldap,$this->ldap_result);
	}

	private function get_next_row($row) {
		return ldap_next_entry($this->ldap,$row);
	}

	private function extract_data($row,$type) {

		$rawres = ldap_get_attributes($this->ldap,$row);

		$res = array();
		foreach ($rawres as $key=>$value) {
			//TODO: think hard about this.
			d($key . '=>' . $value);
			if ($key=='count') {
				continue;
			}
			
			if (($type==Result::ASSOC && is_int($key)) || ($type==Result::NUM && !is_int($key))) {
				continue;
			}

			if ($key=='dn') {
				$res['dn'] = array($value);
				continue;
			}

			if (is_array($value)) {
			
				if ($value['count'] == 1) {
					$res[$key] = $value[0];
					continue;
				}
				unset($value['count']);
				/*$subarray = array();
				for ($i=0;$i<$value['count'];$i++) {
					$subarray[] = $value[$i];
				}
				$value = $subarray;*/
			}

			$res[$key] = $value;
		}

		return $res;
		
	}
	
	public function fetch_all_arrays($type=Result::BOTH) {
		$retarr = array();

		/*
		** We must loop here to avoid breaking Result's contract.
		** It's probably good to have one entry point into the LDAP server, anyhow...
		*/
		if ($type == RESULT::NUM) {
			while ($row = $this->fetch_array($type)) {
				$retarr[] = $row;
			}
		} else {
			while ($row = $this->fetch_array($type)) {
				$retarr[ldap_get_dn($this->ldap,$this->current_row)] = $row;
			}
		}
	
		return $retarr;
	}
	
	public function get_insert_id() {
		if (!$this->query_type == LDAP::LDAP_ADD) {
			throw new I2Exception("Attempted to get the insert ID of a non-ADD LDAP query!");
		}
		return ldap_get_dn($this->ldap,$this->current_row);
	}

	public function get_affected_rows() {
	}

	public function get_num_fetched() {
		return $this->current_row_number;
	}

	public function fetch_row($rownum,$type=Result::BOTH) {

		if ($rownum == $this->current_row_number) {
			return $this->extract_data($this->current_row,$type);
		}

		$num = 1;
		$row = NULL;

		if (!$this->current_row || $rownum < $this->current_row_number) {
			$row = $this->get_first_row();
		} else if ($this->current_row) {
			$row = $this->current_row;
			$num = $this->current_row_number;
		}

		while ($num < $rownum) {
			$num++;
			$row = $this->get_next_row($row);
		}

		return $this->extract_data($row,$type);	
	}

	public function more_rows() {
		return $this->get_num_fetched() <  $this->num_rows();
	}

	public function num_rows() {
		return ldap_count_entries($this->ldap,$this->ldap_result);
	}

	public function num_cols() {
		/*
		** This doesn't mean anything in LDAP, because our data aren't rectangular..
		*/
		return -1;
	}

	public function fetch_single_value() {
	}

	public function fetch_col($colname) {
	}

	public function fetch_all_single_values() {
	}

	public function rewind() {
		$this->current_row = NULL;
		$this->current_row_number = 0;
	}

	public function current() {
		if (!$this->current_row) {
			return $this->fetch_array();
		}
		//This is wrong.
		return $this->current_row;
	}

	public function key() {
		return $this->current_row_number - 1;
	}

	public function valid() {
		return $this->current() !== FALSE;
	}

	public function next() {
		return $this->fetch_array();
	}

}

?>
