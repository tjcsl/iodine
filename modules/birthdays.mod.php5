<?php
/**
* Just contains the definition for the class {@link Birthdays}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
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

	private $birthdays;

	private $template_args = array();

	function init_box() {
		global $I2_ROOT;

		$this->birthdays = $this->get_birthdays(time());
		
		return "Today's Birthdays";
	}
	
	function display_box($disp) {
		$disp->disp('birthdays_box.tpl', array('birthdays' => $this->birthdays));
	}
	
	function init_pane() {
		global $I2_ARGS;
		
		if (isset($_POST['date'])) {
			redirect('birthdays/' . str_replace('.', '/', $_POST['date']));
		}

		if (count($I2_ARGS) == 4) {
			list($month, $day, $year) = array_slice($I2_ARGS, 1);
		} else {
			$today = getdate();
			$month = $today['mon'];
			$day = $today['mday'];
			$year = $today['year'];
		}

		$birthdays = array();
		for($i = -3; $i <= 3; $i+=1) {
			$time = mktime(0, 0, 0, $month, $day+$i, $year);
			$birthday = array();
			$birthday['date'] = $time;
			$birthday['people'] = $this->get_birthdays($time);
			$birthdays[] = $birthday;
		}
		
		$this->template_args['date'] = mktime(0, 0, 0, $month, $day, $year);
		$this->template_args['today'] = mktime(0, 0, 0);
		$this->template_args['birthdays'] = $birthdays;

		return "Birthdays";
	}
	
	function display_pane($disp) {
		$disp->disp('birthdays_pane.tpl', $this->template_args);
	}

	private function get_birthdays($timestamp) {
		global $I2_SQL;
		
		$date = getdate($timestamp);
		$month = $date['mon'];
		$day = $date['mday'];
		$year = $date['year'];
		
		$birthdays = $I2_SQL->query("SELECT CONCAT(`fname`, ' ', `lname`) as `name`, `grade`, `bdate`, user.uid FROM userinfo JOIN user USING (uid) WHERE `bdate` LIKE %s ORDER BY bdate, grade DESC, lname, fname", "%-%$month-%$day")->fetch_all_arrays(Result::ASSOC);
		
		foreach($birthdays as &$birthday) {
			$birthday['age'] = $year - ((int)substr($birthday['bdate'], 0, 4));
		}
		
		return $birthdays;
	}

	function get_name() {
		return "Birthdays";
	}

	function is_intrabox() {
		return true;
	}
}
?>
