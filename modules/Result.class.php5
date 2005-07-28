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
*/

/**
* A class representing the results of a {@link MySQL} query.
* @package core
* @subpackage MySQL
*/
class Result implements Iterator {
	
	/*
	** The mysql result for this Result object.
	*/
	private $mysql_result = NULL;

	/*
	** The query type of this Result object.
	*/
	private $query_type = NULL;
	
	/*
	** Cached information resolving column names to numbers.
	*/
	private $schema = array();

	/*
	** The current row.
	*/
	private $current_row = NULL;

	/*
	** The current row number.
	*/
	private $curr_row_number = 0;
	
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
		$this->mysql_result = $mysql_result;
		$this->query_type = $query_type;
	}

	
	/**
	* Fetches the next ungotten row in the resultset.
	*
	* @param int $type MYSQL_BOTH, MYSQL_ASSOC, or MYSQL_NUM.
	* @return mixed An array containing cells indexed by the selected method.
	*/
	function fetch_array($type = MYSQL_BOTH) {
		$row = mysql_fetch_array($this->mysql_result, $type);
		if ($row) {
			$this->current_row_number++;
			return ($this->current_row = $row);
		}
		return ($this->current_row = FALSE);
	}

	/**
	* Gets the ID of the first INSERT statement associated with this Result object.
	*
	* @return int The ID which the row was inserted into.
	*/
	function get_insert_id() {
		if ($this->query_type == MySQL::INSERT) {
			$id = mysql_insert_id($this->mysql_result);
			if ($id) {
				return $id;
			}
		}
		return 0;
	}

	/**
	* Gets the number of affected rows in the last query.
	*
	* @return int The number of affected rows.
	*/
	function get_affected_rows() {
		if ($this->query_type == MySQL::INSERT || $this->query_type == MySQL::UPDATE || $this->query_type == MySQL::DELETE) {
			$affected = mysql_affected_rows($this->mysql_result);
			if ($affected) {
				return $affected;
			}
		}
		return -1;
	}

	/**
	* Returns the number of rows fetched so far by this Result.
	*
	* @return int The number of rows fetched.
	*/
	function get_num_fetched() {
		return $this->current_row_number;
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
		if($this->query_type = MySQL::SELECT && $rownum < mysql_num_rows($this->mysql_result)) {
			mysql_data_seek($this->mysql_result, $rownum);
			$this->fetch_array($type);
			mysql_data_seek($this->mysql_result, $this->current_row_number);
		}
		return FALSE;
	}
	
	/**
	* Gets whether there are more unfetched rows in this Result object.
	* Note that this is not determined by how many rows the calling code
	* has fetched; this is dependent on the total number of rows fetched
	* inside the Result object.  Thus, after calling fetch_regex, this
	* method will return false.
	*
	* @return bool TRUE for more rows; FALSE otherwise.
	*/
	function more_rows() {
		if ($this->query_type == MySQL::SELECT && mysql_num_rows($this->mysql_result) > $this->current_row_number) {
			return TRUE;
		}
		return FALSE;
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

	/**
	* Rewind function for Iterator interface
	*/
	function rewind() {
		mysql_data_seek($this->mysql_result, 0);
		$this->current_row_number = 0;
		$this->current_row = NULL;
	}
	
	/**
	* Current function for Iterator interface
	* @return array The current row
	*/
	function current() {
		if(!$this->current_row) {
			//fetch_array sets current_row, so do not do it here
			$this->fetch_array();
		}
		return $this->current_row;
	}
	
	/**
	* Key function for the Iterator interface
	* @return int The key for the current row (its number)
	*/
	function key() {
		return $this->current_row_number - 1;
	}
	
	/**
	* Next function for Iterator interface
	* @return array The next row
	*/
	function next() {
		return $this->fetch_array();
	}
	
	/**
	* Valid function for Iterator interface
	* @return bool Valid until we reach the end of the result set
	*/
	function valid() {
		return $this->current() !== FALSE;
	}

}
?>
