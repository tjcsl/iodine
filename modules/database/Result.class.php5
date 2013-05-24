<?php
/**
* Just contains the definition for the {@link Result} class, and the defines for Result:: stuff.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Database
* @filesource
*/

/**
* An interface representing the results of a data query.
* @package core
* @subpackage Database
*/			
interface Result extends Iterator {

	const BOTH = MYSQLI_BOTH;
	const NUM = MYSQLI_NUM;
	const ASSOC = MYSQLI_ASSOC;

	/**
	* Fetches the next ungotten row in the resultset.  This function will return FALSE
	* if fetching the next row is impossible.
	*
	* @param int $type Result::BOTH, Result::ASSOC, or RESULTL_NUM.
	* @return mixed An array containing cells indexed by the selected method.
	*/
	public function fetch_array($type=Result::BOTH);
	/**
	* Gets the ID of the first row-creation statement associated with this Result object.
	*
	* @return mixed The ID which the row was inserted into.
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
	public function fetch_all_arrays($type=Result::BOTH);
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
	public function fetch_row($rownum,$type=Result::BOTH);
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
	
	/**
	* Fetch all values from the given column.
	*
	*
	* @param mixed $colname The name of the column to fetch values for.
	* @return array The values of the passed column.
	*/
	public function fetch_col($colname);

	/**
	* Fetches the entirety of the Result's first column.
	*
	* @return array All the values of the first column.
	*/
	public function fetch_all_single_values();

	/**
	* Fetches binary data from the Result.
	*/
	public function fetch_binary_value($colname);
}
				
?>
