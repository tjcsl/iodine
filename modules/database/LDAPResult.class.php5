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

	/**
	* Row IDENTIFIER of the current row
	*/
	private $current_row = NULL;

	private $current_row_number = 0;

	/**
	* The number of the last row fetched from LDAP
	*/
	private $last_row_fetched = -1;
	
	private $current_dn;

	/**
	* The number of rows in the LDAP result
	*/
	private $num_rows;

	/**
	* Row DATA of previously fetched rows
	*/
	private $rows;

	/**
	* Previously fetched DNs
	*/
	private $dns;

	public function __construct($ldap,$result,$type) {
		if ($ldap === NULL) {
			$this->num_rows = 0;
			$this->type = LDAP::LDAP_SEARCH;
			return;
		}
		$this->ldap = $ldap;
		$this->ldap_result = $result;
		$this->query_type = $type;
		$this->dns = [];
		$this->rows = [];
		//FIXME: fix whatever else is broken that requires this +1
		if ($type == LDAP::LDAP_SEARCH) {
			$rows = ldap_count_entries($ldap,$result);
			$this->num_rows = $rows+1;
			d("LDAP Search Result with $rows rows constructed",8);
		}
	}

	public function __destruct() {
		/*if ($this->ldap_result) {
			ldap_free_result($this->ldap_result);
		}*/
	}

	public static function get_null() {
		return new LDAPResult(NULL,NULL,NULL);
	}

	public function sort($sortattrs) {
		if ($this->last_row_fetched > -1) {
			throw new I2Exception('An LDAPResult with fetched rows cannot be sorted!');
		}
		foreach ($sortattrs as $attr) {
			ldap_sort($this->ldap,$this->ldap_result,$attr);
		}
	}
	
	public function fetch_array($type=Result::BOTH) {
	
		if (!$this->query_type=LDAP::LDAP_SEARCH) {
			throw new I2Exception('A resultset array cannot be fetched from a non-SEARCH LDAP query!');
		}
	
		if ($this->current_row === NULL) {
			$this->current_row = $this->get_first_row();
			$this->current_dn = $this->get_current_dn();
		} else {
			$this->current_row = $this->get_next_row($this->current_row);
			$this->current_dn = $this->get_current_dn();
		}

		if (!$this->current_row) {
			return FALSE;
		}

		$this->last_row_fetched = $this->last_row_fetched+1;

		/*
		** Free the LDAP result object as soon as possible
		*/
		if ($this->ldap_result && $this->last_row_fetched >= $this->num_rows-1) {
			ldap_free_result($this->ldap_result);
			$this->ldap_result = FALSE;
		}

		$data = $this->extract_data($this->current_row,$type);

		$this->rows[] = $data;
		$this->dns[] = $this->current_dn;

		return $data;

	}

	private function get_current_dn() {
		if (!$this->current_row || $this->num_rows == 0) {
			return FALSE;
		}
		return ldap_get_dn($this->ldap,$this->current_row);
	}

	private function get_first_row() {
		if ($this->num_rows == 0) {
			return FALSE;
		}
		return ldap_first_entry($this->ldap,$this->ldap_result);
	}

	private function get_next_row($row) {
		return ldap_next_entry($this->ldap,$row);
	}

	/**
	* @fixme This method is broken and needs to be rewritten
	*/
	private function extract_data($row,$type) {

		$rawres = ldap_get_attributes($this->ldap,$row);

		$res = [];
		$i = 0;
		
		foreach ($rawres as $key=>$value) {
			//TODO: think hard about this.
			//d($key . '=>' . $value);
			if ($key=='count') {
				continue;
			}
			
			if (is_int($key)) {
				continue;
			}

			if ($key=='dn') {
				$res['dn'] = array($value);
				continue;
			}

			if (is_array($value)) {
			
				if ($value['count'] == 1) {
					$value = $value[0];
				} else {
					unset($value['count']);
				}
				/*$subarray = [];
				for ($i=0;$i<$value['count'];$i++) {
					$subarray[] = $value[$i];
				}
				$value = $subarray;*/
			}

			if($type == Result::NUM || $type == Result::BOTH) {
				$res[$i++] = $value;
			}
			if($type == Result::ASSOC || $type == Result::BOTH) {
				$res[$key] = $value;
			}
		}

		return $res;
		
	}
	
	public function fetch_all_arrays($type=Result::BOTH) {
		$retarr = [];

		for ($a = 0; $a < $this->num_rows-1; $a++) {
			if ($type == Result::NUM || $type == Result::BOTH) {
				$retarr[$a] = $this->fetch_row($a,$type);
			}
			if ($type == Result::ASSOC || $type == Result::BOTH) {
				$retarr[$this->get_dn($a,$type)] = $this->fetch_row($a,$type);
			}
		}
		
		return $retarr;
	}

	/**
	* Fetch up until the passed row (for cache filling)
	*/
	private function fetch_to($rownum, $type=Result::BOTH) {
		if ($rownum > $this->num_rows-1) {
			throw new I2Exception("Row number $rownum requested of an LDAP Result containing only {$this->num_rows} entries!");
		}
		$fetched = $this->last_row_fetched;
		/*
		** Fetch until our cache is sufficiently filled
		*/
		while ($fetched < $rownum) {
			if($this->fetch_array($type) === FALSE) {
				return FALSE;
			}
			$fetched++;
		}
	}

	private function get_dn($rownum,$type=Result::BOTH) {
		$this->fetch_to($rownum,$type);
		return $this->dns[$rownum];
	}
	
	public function get_insert_id() {
		if (!$this->query_type == LDAP::LDAP_ADD) {
			throw new I2Exception('Attempted to get the insert ID of a non-ADD LDAP query!');
		}
		return $this->current_dn;
	}

	public function get_affected_rows() {
		throw new I2Exception('Call to unimplemented method get_affected_rows() in LDAPResult!');
	}

	public function get_num_fetched() {
		return $this->current_row_number;
	}

	public function fetch_row($rownum,$type=Result::BOTH) {
		if($this->fetch_to($rownum, $type) === FALSE) {
			return FALSE;
		}
		/*
		** You CANNOT switch the $type!
		** If you do, cached data is wrong and will still be given to you
		*/
		return $this->rows[$rownum];
	}

	public function more_rows() {
		return $this->get_num_fetched() <  $this->num_rows();
	}

	public function num_rows() {
		return $this->num_rows;
	}

	public function num_cols() {
		/*
		** This doesn't mean anything in LDAP, because our data aren't rectangular..
		*/
		throw new I2Exception('Attempted to get the number of columns in an LDAP result!');
	}

	public function fetch_single_value() {
		$row = $this->fetch_array();
		if (!$row) {
			return FALSE;
		}
		if (isset($row[0])) {
			return $row[0];
		}
		if (isset($row['dn'])) {
			return $row['dn'];
		}
		$keys = array_keys($row);
		if (isset($keys[0])) {
			return $row[$keys[0]];
		}
		return FALSE;
	}

	public function fetch_col($colname) {
		$ret = [];
		while ($arr = $this->fetch_array(Result::BOTH)) {
			if (isset($arr[$colname])) {
				$ret[] = $arr[$colname];
			}
		}
		return $ret;
	}

	public function fetch_all_single_values() {
		$ret = [];
		while ($row = $this->fetch_array(Result::NUM)) {
			$ret[] = $row[0];
		}
		return $ret;
	}

	public function rewind() {
		$this->current_row_number = 0;
	}

	public function current() {
		if (!$this->current_row) {
			return $this->fetch_array();
		}
		return $this->fetch_row($this->current_row_number);
	}

	public function key() {
		return $this->current_row_number;
	}

	public function valid() {
		return $this->current() !== FALSE;
	}

	public function next() {
		if (!$this->current_row) {
			return $this->fetch_array();
		}
		$data = $this->fetch_row($this->current_row_number);
		$this->current_row_number++;
		return $data;
	}

	public function fetch_binary_value($colname) {
		if(!$this->current_row) {
			$this->current_row = $this->get_first_row();
		}
		return ldap_get_values_len($this->ldap, $this->current_row, $colname);
	}
}

?>
