<?php
/**
* Just contains the definition for the {@link Result} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package core
* @subpackage MySQL
* @filesource
* @todo Fix the broken-ness. Much of this class is broken. (Stebbins?)
*/

/**
* A class representing the results of a {@link MySQL} query.
* @package core
* @subpackage MySQL
*/
class Result {
	
	/*
	** An associative array of arrays, such that:
	** $results[$mysql_result_object] = array($qtype,$numrows,$row1,$row2,...)
	** $qtype is a constant from the MySQL module;
	** $numrows is the number of rows fetched from the result.
	*/
	private $results = array();
	
	/*
	** Cached information resolving column names to numbers.
	*/
	private $schema = array();
	
	/**
	* Joins a resultset object onto the right of this one.
	*
	* @param mixed $result The result object to right-join onto this.
	* @param mixed $qtype The query type this result represents, from
	* the MySQL module.
	*/

	function join_right($result,$qtype) {
		$this->results[$result] = array($result,$qtype,0);
	}

	/**
	* The constructor for a Result object.
	*
	* @param mixed $mysql_result A MySQL resultset object 
	* for this to associate with.
	* @param mixed $query_type See join_right.
	*/
	function __construct($mysql_result,$query_type) {
		global $I2_LOG;
		if (!$mysql_result) {
			d('Null SQL result constructed.');
			return;
		}
		$this->join_right($mysql_result,$query_type);
	}

	
	/**
	* Fetches the next ungotten row in the resultset.
	* Note that which row this is may change after joining
	*
	* @param int $type MYSQL_BOTH, MYSQL_ASSOC, or MYSQL_NUM.
	* @return mixed An array containing cells indexed by the selected method.
	*/
	function fetch_array($type = MYSQL_BOTH) {
		foreach ($this->results as $res=>$arr) {
			$row = mysql_fetch_array($arr[0],$type);
			if ($row) {
				$arr[] = $row;
				$arr[2]++;
				return $row;
			}
		}
		return false;
	}

	/**
	* Gets the ID of the first INSERT statement associated with this Result object.
	*
	* @return int The ID which the row was inserted into.
	*/
	function get_insert_id() {
		foreach ($results as $res=>$arr) {
			if ($arr[1] != MySQL::INSERT) {
				continue;
			}
			$id = mysql_insert_id($arr[0]);
			if ($id) {
				return $id;
			}
		}
		//TODO: roll over and die; this resultset has no queries for which insert_id is relevant.
	}

	/**
	* Gets the number of affected rows in the last query.
	*
	* @return int The number of affected rows.
	*/
	function get_affected_rows() {
		foreach ($results as $res=>$arr) {
			if ($arr[1] != MySQL::UPDATE && $arr[1] != MySQL::DELETE) {
				continue;
			}
			$affected = mysql_affected_rows($arr[0]);
			if ($affected) {
				return $affected;
			}
		}
		//TODO: die; this Result has no queries with affected_rows.
	}

	/**
	* Returns an array of the number of rows fetched so far by this Result.
	*
	* @return array The number of rows fetched for each resultset in this Result.
	*/
	function get_num_fetched() {
		$fetched = array();
		foreach ($this->results as $null=>$arr) {
			$fetched[] = $arr[2];
		}
		return $fetched;
	}

	/**
	* Returns a cumulative sum of the number of rows fetched by this Result.
	*
	* @return int The total number of rows fetched.
	*/
	function get_num_fetched_total() {
		$sum = 0;
		foreach ($this->get_num_fetched() as $fetched) {
			$sum += $fetched;
		}
		return $sum;
	}

	/**
	* Fetches the nth row in this Result object.
	*
	* @param int $rownum The row index to fetch.  Rows are zero-indexed.
	* If this is greater than the number of rows in the Result, an error will be thrown.
	* @param int $type As fetch_array.
	* @return mixed As fetch_array.
	*/
	function fetch_row($rownum, $type = MYSQL_BOTH) {
		foreach ($results as $res=>$arr) {
			if ($arr[1] != MySQL::SELECT) {
				continue;
			}
			$rownum -= mysql_num_rows($res);
			if ($rownum < 0) {
				while ($rownum > $arr[2]) {
					$row = mysql_fetch_array($arr[0],$type);
					$arr[] = $row;
					$arr[2]++;
				}
				return $arr[count($arr)-1];
			}
		}
		//TODO: crash and burn; no such row.
	}
	
	/**
	* Gets whether there are more unfetched rows in this Result object.
	* Note that this is not determined by how many rows the calling code
	* has fetched; this is dependent on the total number of rows fetched
	* inside the Result object.  Thus, after calling fetch_regex, this
	* method will return false.
	*
	* @return boolean True for more rows; false otherwise.
	*/
	function more_rows() {
		foreach ($this->results as $res=>$arr) {
			if ($arr[1] == MySQL::SELECT && mysql_num_rows($arr[0]) > $arr[2]) {
				return true;
			}
		}
		return false;
	}

	/**
	* Fetches an entire column by name.  Note that this operation is time-consuming,
	* and thus discouraged.  It is not safe to call fetch_array after this operation.
	*
	* @param string $colname The name of the column to be fetched.
	* @return array An array of cells from the selected column.
	*/
	function fetch_col($colname) {
		$ret = array();
		/*
		**  First, get all the cached rows.
		*/
		$numfetched = $this->get_num_fetched_total();
		for ($a = 0; $a < $numfetched; $a++) {
			$row = $this->fetch_row($a);
			if (isSet($row[$colname])) {
				$ret[] = $row[$colname];
			}
			
		}

		/*
		** Then, push through the rest of the resultset.
		*/
		while ($this->more_rows()) {
			$row = $this->fetch_array();
			if (isSet($row[$colname])) {
				$ret[] = $row[$colname];
			}
		}

		return $ret;
	}

	/**
	* Fetches all cells that match the passed regular expression in the given columns.
	* This is a VERY expensive operation, but it may be worthwhile. Please note that this
	* will get every row in the Result, thus eliminating fetch_array's usefulness.
	*
	* @param array $colnames An array of the column names in which to search.
	* @param string $pattern The PHP regular expression to match cells against.
	* @return array An array of cells matching to the passed pattern.
	*/
	function fetch_regex($colnames, $pattern) {
		//TODO: implement
		foreach ($results as $res=>$arr) {
		}
	}
	

	/**
	* Fetches all previously unfetched rows from the Result.
	*
	* @return array A two-dimensional array containing all the rows as-of-yet unfetched.
	*/
	function fetch_all_arrays($type = MYSQL_BOTH) {
		$sum = array();
		while ($arr = $this->fetch_array($type)) {
			$sum[] = $arr;
		}
		return $sum;
	}
}
?>
