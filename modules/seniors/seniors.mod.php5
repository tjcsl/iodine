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
	private $template_args = [];

	private $college_cache = [];
	private $major_cache = [];

	private $is_admin = false;
	
	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ARGS, $I2_USER;

		$this->is_admin = $I2_USER->is_group_member('admin_all');
		
		$args = [];
		if(count($I2_ARGS) <= 1) {
			return $this->sort();
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

	private function sort() {
		global $I2_SQL, $I2_USER, $I2_ARGS;

		$this->template = 'destinations.tpl';
		$this->template_args['is_admin'] = $this->is_admin;
	
		$sort = 'name';
		$sortdir = 'ASC';
		if (isset($I2_ARGS[2])) {
			switch ($I2_ARGS[2]) {
			case 'name':
				$sort = 'name';
				break;
			case 'college':
				$sort = 'CollegeName';
				break;
			case 'major':
				$sort = 'MajorMap.Major';
				break;
			}
			if (isset($I2_ARGS[3])) {
				$sortdir = 'DESC';
			}
		}
		if (isset($I2_ARGS[2])) {
			$this->template_args['sort'] = $I2_ARGS[2];
		}
		else {
			$this->template_args['sort'] = 'name';
		}
		$this->template_args['sortnormal'] = ($sortdir == 'ASC');

		$this->template_args['seniors'] = [];

		$rows = $I2_SQL->query("SELECT uid, CollegeName, college_certain, MajorMap.Major, major_certain FROM senior_destinations LEFT JOIN CEEBMap USING (CEEB) LEFT JOIN MajorMap ON senior_destinations.Major=MajorMap.MajorID ORDER BY $sort $sortdir")->fetch_all_arrays(Result::ASSOC);
		foreach ($rows as $row) {
			$senior = [];
			$senior['user'] = new $I2_USER($row['uid']);
			#$senior['dest'] = $this->get_college($row['ceeb']);
			$senior['dest'] = $row['CollegeName'];
			$senior['dest_sure'] = $row['college_certain'];
			#$senior['major'] = $this->get_major($row['major']);
			$senior['major'] = $row['Major'];
			$senior['major_sure'] = $row['major_certain'];
			$this->template_args['seniors'][] = $senior;
		}

		$this->template_args['num_submitted'] = $I2_SQL->query('SELECT COUNT(*) FROM senior_destinations')->fetch_single_value();

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
			$row = $res->fetch_array(Result::ASSOC);
			$this->template_args['sel_ceeb'] = $row['ceeb'];
			$this->template_args['dest_sure'] = $row['college_certain'];
			$this->template_args['sel_major'] = $row['major'];
			$this->template_args['major_sure'] = $row['major_certain'];
		}

		$this->template_args['colleges'] = $I2_SQL->query('SELECT * FROM CEEBMap ORDER BY CollegeName')->fetch_all_arrays();
		$this->template_args['majors'] = $I2_SQL->query('SELECT * FROM MajorMap ORDER BY Major')->fetch_all_arrays();

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

	private function admin() {
		global $I2_SQL;

		if (! $this->is_admin) {
			redirect('seniors');
		}
		if (isSet($_POST['add_college'])) {
			$I2_SQL->query('INSERT INTO CEEBMap SET CEEB=%d, CollegeName=%s;', $_POST['ceeb'], $_POST['college']);
			redirect('seniors');
		}
		if (isSet($_POST['add_major'])) {
	 		$I2_SQL->query('INSERT INTO MajorMap SET Major=%s;', $_POST['major']);	
			redirect('seniors');
		}
		
		$this->template = 'admin.tpl';
		return "Add a College or Major";
	}

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
		$args = [];
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
