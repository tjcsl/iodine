<?php
/**
* Just contains the definition for the class {@link Seniors}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Seniors
* @filesource
*/

/**
* The module that keep the seniors (and those who want information about their college destinations and majors) happy.
* @package modules
* @subpackage Seniors
*/
class Seniors implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	private $college_cache = array();
	private $major_cache = array();

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;

		$args = array();
		if(count($I2_ARGS) <= 1) {
			return $this->home();
		}
		else {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				return $this->$method();
			}
			$this->template = 'error.tpl';
			$this->template_args = array('method' => $method, 'args' => $I2_ARGS);
		}
		return array('Error', 'Error');
	}

	private function home() {
		global $I2_SQL, $I2_USER;

		$this->template = 'destinations.tpl';

		$this->template_args['seniors'] = array();
		$rows = $I2_SQL->query('SELECT * FROM senior_destinations ORDER BY name')->fetch_all_arrays(Result::ASSOC);
		foreach ($rows as $row) {
			$senior = array();
			$senior['user'] = new $I2_USER($row['uid']);
			$senior['dest'] = $this->get_college($row['ceeb']);
			$senior['dest_sure'] = $row['college_certain'];
			$senior['major'] = $this->get_major($row['major']);
			$senior['major_sure'] = $row['major_certain'];
			$this->template_args['seniors'][] = $senior;
		}

		$this->template_args['num_submitted'] = $I2_SQL->query('SELECT COUNT(*) FROM senior_destinations ORDER BY name')->fetch_single_value();

		if ($I2_USER->grade == 12) {
			$this->template_args['is_senior'] = 1;
			if ($I2_SQL->query('SELECT COUNT(*) FROM senior_destinations WHERE uid=%d', $I2_USER->uid)->fetch_single_value()) {
				$this->template_args['has_submitted'] = 1;
			}
		}

		return "View Senior College Destinations";
	}

	private function submit() {
		global $I2_SQL, $I2_USER;

		if ($I2_USER->grade != 12) {
			redirect('seniors');
		}

		if (isset($_REQUEST['seniors_form'])) {
			if (! is_numeric($_REQUEST['ceeb'])) {
				error('CEEB not numeric!');
			}
			$ceeb = $_REQUEST['ceeb'];
			$dest_sure = (int) isset($_REQUEST['dest_sure']);

			if (! is_numeric($_REQUEST['major'])) {
				error('majorID not numeric!');
			}
			$major = $_REQUEST['major'];
			$major_sure = (int) isset($_REQUEST['major_sure']);
			
			$I2_SQL->query('REPLACE INTO senior_destinations SET uid=%d, name=%s, ceeb=%d, college_certain=%d, major=%d, major_certain=%d', $I2_USER->uid, $I2_USER->name_comma, $ceeb, $dest_sure, $major, $major_sure);
			redirect('seniors');
		}

		$res = $I2_SQL->query('SELECT * FROM senior_destinations WHERE uid=%d', $I2_USER->uid);
		if ($res->num_rows() > 0) {
			$row = $res->fetch_row(Result::ASSOC);
			$this->template_args['sel_ceeb'] = $row['CEEB'];
			$this->template_args['dest_sure'] = $row['college_certain'];
			$this->template_args['sel_major'] = $row['major'];
			$this->template_args['major_sure'] = $row['major_certain'];
		}

		$this->template_args['colleges'] = $I2_SQL->query('SELECT * FROM CEEBMap')->fetch_all_arrays();
		$this->template_args['majors'] = $I2_SQL->query('SELECT * FROM MajorMap')->fetch_all_arrays();

		$this->template = 'submit.tpl';

		return 'Enter Your College Destination';
	}

	private function load_college_cache() {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT * FROM CEEBMap');
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$this->college_cache[$row['CEEB']] = $row['CollegeName'];
		}
	}
	
	private function get_college($ceeb) {
		global $I2_SQL;
		if (! isset($this->college_cache[$ceeb])) {
			$this->college_cache[$ceeb] = $I2_SQL->query('SELECT CollegeName FROM CEEBMap WHERE CEEB=%d', $ceeb)->fetch_single_value();
		}
		return $this->college_cache[$ceeb];
	}

	private function load_major_cache() {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT * FROM MajorMap');
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$this->major_cache[$row['MajorID']] = $row['Major'];
		}
	}

	private function get_major($id) {
		global $I2_SQL;
		if (! isset($this->major_cache[$id])) {
			$this->major_cache[$id] = $I2_SQL->query('SELECT Major FROM MajorMap WHERE MajorID=%d', $id)->fetch_single_value();
		}
		return $this->major_cache[$id];
	}

	//private function 

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return "Senior College Destinations";
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		global $I2_SQL, $I2_USER;
		$args = array();
		$args['num_submitted'] = $I2_SQL->query('SELECT COUNT(*) FROM senior_destinations')->fetch_single_value();
		if ($I2_USER->grade == 12) {
			$args['is_senior'] = 1;
			if ($I2_SQL->query('SELECT COUNT(*) FROM senior_destinations WHERE uid=%d', $I2_USER->uid)->fetch_single_value()) {
				$args['has_submitted'] = 1;
			}
		}
		$display->disp('box.tpl', $args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Calculator Registration";
	}
}

?>
