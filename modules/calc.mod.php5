<?php
/**
* Just contains the definition for the class {@link Calc}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Calc
* @filesource
*/

/**
* The module that keeps the eighth block office happy.
* @package modules
* @subpackage Calc
*/
class Calc implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template = "calc_pane.tpl";

	/**
	* Template arguments for the specified action
	*/
	private $message;
	private $template_args = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if( isset($_REQUEST['calc_form']) ) {
			//form submitted
			foreach($_REQUEST as $key=>$val) {
				if($key == 'delete') {
					$I2_SQL->query('DELETE FROM calculators WHERE calcid=%s', $val);
					$this->message = "Calculator $val removed from database.";
				}
				else if($key == 'add') {
					if( !is_numeric($val) ) {
						$this->message = "Calculator not added -- calculator ID number must be entered as one number!";
					}
					else if($this->calc_exists($val)) {
						$this->message = "Calculator not added -- this calculator is already in the database!";
					}
					else {
						$uid = $I2_USER->uid;
						//d("uid: $uid");
						$I2_SQL->query('INSERT INTO calculators (calcid, uid) VALUES (%d, %d)', $val, $uid);
						$this->message = "Calculator succesfully added.";
					}
				}
			}
		}
		return array('Calculator Registration');
	}

	private function calc_exists($val) {
		global $I2_SQL;
		$temp = flatten($I2_SQL->query('SELECT * FROM calculators WHERE calcid=%d', $val)->fetch_array(MYSQL_ASSOC));
		if ( $temp != null )
		{
			return true;
		}
		else {
			return false;
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER;
		$calcs = $I2_SQL->query('SELECT calcid FROM calculators WHERE uid=%d', $I2_USER->uid)->fetch_all_arrays(MYSQL_ASSOC);
		//$display->disp($this->template, $this->template_args);
		$display->disp($this->template, array( 'message' => $this->message ,
							'calcs' => $calcs));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Calculator Registration";
	}
}

?>
