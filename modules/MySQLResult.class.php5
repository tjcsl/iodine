<?php
/**
* Just contains the definition for the {@link MySQLResult} class.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2004 The Intranet 2 Development Team
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
class MySQLResult implements Result {
	
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
	private $current_row_number = 0;
	
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
			$this->currect_row = 0;
			/* Haha, it's brilliant!
			** We just have to make sure to implement the same methods as MySQL results do.
			** Doing this will make things more likely to work in the event of disaster.
			*/
		$this->mysql_result = $this;

			return;
		}
		$this->mysql_result = $mysql_result;
		$this->query_type = $query_type;
	}

	public static function nil() {
		return new MySQLResult(null,null);	
	}
	
	function fetch_array($type = MYSQL_BOTH) {
		$row = mysql_fetch_array($this->mysql_result, $type);
		if ($row) {
			$this->current_row_number++;
			return ($this->current_row = $row);
		}
		return ($this->current_row = FALSE);
	}

	
	function get_insert_id() {
		if ($this->query_type == MySQL::INSERT) {
			$id = mysql_insert_id($this->mysql_result);
			if ($id) {
				return $id;
			}
		}
		return 0;
	}

	
	function get_affected_rows() {
		if ($this->query_type == MySQL::INSERT || $this->query_type == MySQL::UPDATE || $this->query_type == MySQL::DELETE) {
			$affected = mysql_affected_rows($this->mysql_result);
			if ($affected) {
				return $affected;
			}
		}
		d("get_affected_rows called in invalid context",8);
		return -1;
	}

	function get_num_fetched() {
		return $this->current_row_number;
	}

	
	function fetch_row($rownum, $type = MYSQL_BOTH) {
		if($this->query_type = MySQL::SELECT && $rownum < mysql_num_rows($this->mysql_result)) {
			mysql_data_seek($this->mysql_result, $rownum);
			$this->fetch_array($type);
			mysql_data_seek($this->mysql_result, $this->current_row_number);
		}
		return FALSE;
	}
	
	
	function more_rows() {
		if ($this->query_type == MySQL::SELECT && mysql_num_rows($this->mysql_result) > $this->current_row_number) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	* @todo Implement fetch_regex
	*/	
	function fetch_regex($colnames, $pattern) {
	}
	

	
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
		if(mysql_num_rows($this->mysql_result) > 0) {
			mysql_data_seek($this->mysql_result, 0);
		}
		$this->current_row_number = 0;
		$this->current_row = FALSE;
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
	
	
	public function num_rows() {
		return mysql_num_rows($this->mysql_result);
	}

	
	public function num_cols() {
		return mysql_num_fields($this->mysql_result);
	}

	public function fetch_single_value() {
		$this->rewind();
		$array = $this->fetch_array(MYSQL_NUM);
		return $array[0];
	}

	public function fetch_col($colname) {
		$this->rewind();
		$ret = array();
		while ($arr = $this->fetch_array(MYSQL_ASSOC)) {
			$ret[] = $arr[$colname];
		}
		return $ret;
	}

	public function fetch_all_single_values() {
		$this->rewind();
		$ret = array();
		while ($arr = $this->fetch_array(MYSQL_NUM)) {
			$ret[] = $arr[0];
		}
		return $ret;
	}


}
?>
