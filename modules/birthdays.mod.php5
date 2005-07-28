<?php
/**
* Just contains the definition for the class {@link Birthdays}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @version 1.0
* @since 1.0
* @package modules
* @subpackage Birthday
* @filesource
*/

/**
* A module that displays users with birthdays close to the current date.
* @todo Cache the results for a day, since these don't need to calculated every
 * time.
* @package modules
* @subpackage Birthday
*/

class Birthdays implements Module {

	private $namearr;

	function init_box() {
		global $I2_SQL;
		$this->namearr = $I2_SQL->query("SELECT CONCAT(`fname`, ' ', `lname`) as `name`, `grade`, `bdate` FROM user, userinfo WHERE user.uid=userinfo.uid AND `bdate` LIKE %s", "%-" . date("m-d"))->fetch_all_arrays(MYSQL_NUM);
		foreach($this->namearr as $key => $value)
			$this->namearr[$key][2] = ((int)date("Y")) - ((int)substr($this->namearr[$key][2], 0, 4));
		return "Today's Birthdays";
	}
	
	function display_box($disp) {
		$disp->disp('birthdays_box.tpl',array('birthdays' => $this->namearr));
	}
	
	function init_pane() {
		return FALSE;
	}
	
	function display_pane($disp) {
		return;
	}

	function get_name() {
		return "Birthdays";
	}
}
?>
