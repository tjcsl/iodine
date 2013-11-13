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
* The module that keeps the calculator nerds happy.
* @package modules
* @subpackage Calc
*/
class Calc extends Module {

	/**
	* Template for the specified action
	*/
	private $template = "calc_pane.tpl";

	/**
	* Declaring some global variables
	*/
	private $message;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		
		global $I2_USER, $I2_SQL;

		if( isset($_REQUEST['calc_form']) ) {
			//form submitted
			if ($_REQUEST['calc_form']=="add")
			{
				if ($_REQUEST['sn']==""||strlen($_REQUEST['sn'])!=10||!is_numeric($_REQUEST['sn']))
				{
					$this->message = "You didn't specify a valid serial number!";
				}
				else if($_REQUEST['id']==""||strlen($_REQUEST['id'])!=14)
				{
					$this->message = "You didn't specify a valid calculator ID!";
				}
				else if($this->calc_exists($_REQUEST['sn']))
				{
					$this->message = "Calculator not added -- this calculator is already in the database!";
				}
				else
				{
					$uid = $I2_USER->uid;
					$I2_SQL->query('INSERT INTO calculators (calcsn, calcid, uid) VALUES (%d,%s,%d)', $_REQUEST['sn'],strtoupper($_REQUEST['id']), $uid);
					$this->message = "Calculator successfully added.";
				}
			}
			else if ($_REQUEST['calc_form']=="delete")
			{
				$I2_SQL->query('DELETE FROM calculators WHERE calcsn=%s', $_REQUEST['sn']);
				$this->message = "Calculator {$_REQUEST['sn']} removed from database.";
			}
		}
		return array('Calculator Registration');
	}

	private function calc_exists($val) {
		global $I2_SQL;
		if ( $I2_SQL->query('SELECT * FROM calculators WHERE calcsn=%d', $val)->fetch_array(Result::ASSOC) != null )
		{
			return true;
		} else {
			return false;
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($disp) {
		global $I2_SQL, $I2_USER;
		$calcs = $I2_SQL->query('SELECT calcsn, calcid FROM calculators WHERE uid=%d', $I2_USER->uid)->fetch_all_arrays(Result::ASSOC);
		$disp->disp($this->template, array( 'message' => $this->message ,
							'calcs' => $calcs));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Calculator Registration";
	}
}

?>
