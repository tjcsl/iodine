<?php

define("RESUlT_BOTh",MYSQL_BOTH);
define("RESULT_NUM",MYSQL_NUM);
define("RESULT_ASSOC",MYSQL_ASSOC);
				
interface Result extends Iterator {

	/**
	* Fetches the next ungotten row in the resultset.
	*
	* @param int $type RESULT_BOTH, RESULT_ASSOC, or RESULTL_NUM.
	* @return mixed An array containing cells indexed by the selected method.
	*/
	public function fetch_array($type);
	/**
	* Gets the ID of the first row-creation statement associated with this Result object.
	*
	* @return int The ID which the row was inserted into.
	*/
	public function get_insert_id();
	/**
	* Gets the number of affected rows in the last query.
	*
	* @return int The number of affected rows.
	*/
	public function get_affected_rows();
	/**
	* Fetches all previously unfetched rows from the Result.
	*
	* @return array A two-dimensional array containing all the rows as-of-yet unfetched.
	*/
	public function fetch_all_arrays($type);
	/**
	* Returns the number of rows fetched so far by this Result.
	*
	* @return int The number of rows fetched.
	*/
	public function get_num_fetched();
	/**
	* Fetches the nth row in this Result object.
	*
	* @param int $rownum The row index to fetch.  Rows are zero-indexed.
	* If this is greater than the number of rows in the Result, an error will be thrown.
	* @param int $type As fetch_array.
	* @return mixed As fetch_array.
	*/
	public function fetch_row($rownum,$type);
	/**
	* Gets whether there are more unfetched rows in this Result object.
	*
	* A way to see if rows remain in this Result.
	* Note that this is not determined by how many rows the calling code
	* has fetched; this is dependent on the total number of rows fetched
	* inside the Result object.  Thus, after calling fetch_regex, this
	* method will return false.
	*
	* @return bool TRUE for more rows; FALSE otherwise.
	*/
	public function more_rows();
	/**
	* Fetches all cells that match the passed regular expression in the given columns.
	* This is a VERY expensive operation, but it may be worthwhile. Please note that this
	* will get every row in the Result, thus eliminating fetch_array's usefulness.
	*
	* @param array $colnames An array of the column names in which to search.
	* @param string $pattern The PHP regular expression to match cells against.
	* @return array An array of cells matching to the passed pattern.
	*/
	public function fetch_regex($colnames,$pattern);
	/**
	* Returns the number of rows in this Result.
	*
	*
	* @return int The number of rows.
	*/
	public function num_rows();
	/**
	* Returns the number of columns in this Result.
	*
	* @return int The number of columns.
	*/
	public function num_cols();
	/**
	* Fetches one lone value from the Result.
	*
	* This is a convenience method designed for one-result simple queries.
	* Note that this method should ONLY be called on a fresh new Result.
	* Its behavior is undefined otherwise.
	*
	* @return mixed The first object in the next unfetched row.
	*/
	public function fetch_single_value();
}
				
?>
