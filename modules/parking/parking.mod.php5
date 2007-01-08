<?php
/**
* Just contains the definition for the class {@link Parking}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Parking
* @filesource
*/

/**
* The module that keeps the security office happy.
* @package modules
* @subpackage Parking
*/
class Parking implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Arguments for the template
	*/
	private $template_args = array();

	/**
	* Declaring some global variables
	*/
	private $message;

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1) {
			$I2_ARGS[1] = 'home';
		}

		$method = $I2_ARGS[1];
		if (method_exists($this, $method)) {
			$this->$method();
			$this->template_args['method'] = $method;
			return 'Parking Applications: ' . ucwords(strtr($method, '_', ' '));
		}
		else {
			redirect('parking');
		}
	}

	/**
	* The intro page.
	*
	* Says some text.
	*/
	function home() {
		global $I2_USER, $I2_SQL;

		$settings = $I2_SQL->query('SELECT * FROM parking_settings')->fetch_array();

		if(! ($I2_USER->is_group_member('grade_10') || $I2_USER->is_group_member('grade_11') || $I2_USER->is_group_member('admin_parking'))) {
			redirect('');
		}

		$this->template_args['is_admin'] = $I2_USER->is_group_member('admin_parking');
		$this->template_args['deadline'] = date('F jS, Y', strtotime($settings['deadline']));
		$this->template_args['startdate'] = date('F jS, Y', strtotime($settings['startdate']));

		if(! isset($_REQUEST['go_to_form'])) {
			// just the intro page
			$this->template = 'parking_pane.tpl';
			return;
		}

		if(! (strtotime($settings['startdate']) < time() || $I2_USER->is_group_member('admin_parking'))) {
			$this->template = 'parking_not_time_yet.tpl';
			return;
		}
		if(! (time() < strtotime($settings['deadline']) || $I2_USER->is_group_member('admin_parking'))) {
			$this->template = 'parking_past_deadline.tpl';
			return;
		}

		// do the application processing
		if(isset($_REQUEST['parking_apply_form'])) {

			if($_REQUEST['parking_apply_form'] == 'apply') {
				if(isset($_REQUEST['mship'])) {
					$mship = 1;
				}
				else {
					$mship = 0;
				}

				if($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value() == 0) {
					$I2_SQL->query('INSERT INTO parking_apps SET uid=%d, mentorship=%d, other_driver=%s, name=%s, skips=%d, grade=%d, timestamp=NOW()', $I2_USER->uid, $mship, $_REQUEST['otherdriver'], $I2_USER->name_comma, count(EighthSchedule::get_absences($I2_USER->uid)), $I2_USER->grade);
				}
				else {
					$I2_SQL->query('UPDATE parking_apps SET mentorship=%d, other_driver=%s, skips=%d WHERE uid=%d', $mship, $_REQUEST['otherdriver'], count(EighthSchedule::get_absences($I2_USER->uid)), $I2_USER->uid);
				}

				$I2_SQL->query('DELETE FROM parking_cars WHERE uid=%d', $I2_USER->uid);
				foreach ($_REQUEST['car'] as $car) {
					if ($car['plate'] != "") {
						$I2_SQL->query('INSERT INTO parking_cars SET uid=%d, plate=%s, make=%s, model=%s, color=%s, year=%d', $I2_USER->uid, $car['plate'], $car['make'], $car['model'], $car['color'], $car['year']);
					}
				}
			}
			else if($_REQUEST['parking_apply_form'] == 'withdraw') {
				$I2_SQL->query('DELETE FROM parking_apps WHERE uid=%d', $I2_USER->uid);
				$I2_SQL->query('DELETE FROM parking_cars WHERE uid=%d', $I2_USER->uid);
			}
		}

		$this->template_args['name'] = $I2_USER->name_comma;
		$this->template_args['grade'] = $I2_USER->grade;
		$this->template_args['skips'] = count(EighthSchedule::get_absences($I2_USER->uid));

		$this->template_args['cars'] = array();

		if($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value() != 0) {

			$app = $I2_SQL->query('SELECT timestamp, mentorship, other_driver FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_array();
			$this->template_args['submitdate'] = date('F jS, Y',strtotime($app['timestamp']));
			$this->template_args['mship'] = $app['mentorship'];
			$this->template_args['otherdriver'] = $app['other_driver'];

			$res = $I2_SQL->query('SELECT plate, make, model, color, year FROM parking_cars WHERE uid=%d', $I2_USER->uid);
			while($car = $res->fetch_array()) {
				$this->template_args['cars'][] = $car;
			}
		}
		else {
			/* so smarty doesn't whine about undefined indexes */
			$this->template_args['submitdate'] = $this->template_args['mship'] = $this->template_args['otherdriver'] = "";
		}

		/* so smarty doesn't whine about undefined indexes */
		while(count($this->template_args['cars']) < 4) {
			$this->template_args['cars'][] = array('plate' => '', 'make' => '', 'model' => '', 'color' => '', 'year' => '');
		}

		$this->template = 'parking_apply.tpl';

	}

	/**
	* Review/administrate submitted applications.
	*
	* Grabs applications, sorts, assigns spots, whatever else the security office people need.
	*/
	function admin() {
		global $I2_USER, $I2_SQL;

		if(! $I2_USER->is_group_member('admin_parking')) {
			redirect('parking');
		}

		$sortmap = array(	'none' 			=> array('1', 'none (leave last on sorting if used)'),
					'nameasc' 		=> array('name', 'name, A-Z'),
					'namedesc' 		=> array('name DESC', 'name, Z-A'),
					'gradeasc' 		=> array('grade', 'grade, rising juniors first'),
					'gradedesc' 		=> array('grade DESC', 'grade, rising seniors first'),
					'skipsasc' 		=> array('skips', '8th period skips, least to most'),
					'skipsdesc' 		=> array('skips DESC', '8th period skips, most to least'),
					'mentorshipasc' 	=> array('mentorship', 'mentorship, not first'),
					'mentorshipdesc' 	=> array('mentorship DESC', 'mentorship, yes first'),
					'jointdesc' 		=> array('other_driver DESC', 'joint applications, first'),
					'assignedasc' 		=> array('assigned', 'assigned spot, ascending'),
					'assigneddesc' 		=> array('assigned DESC', 'assigned spot, descending'),
					'timeasc' 		=> array('timestamp', 'time submitted, earliest first'),
					'timedesc' 		=> array('timestamp DESC', 'time submtted, latest first'),
					'random' 		=> array('RAND()', 'randomize within last category')
				);
		$sortarr = array();
		if(isset($_REQUEST['parking_admin_form'])) {
			if($_REQUEST['parking_admin_form'] == 'changedeadline') {
				$I2_SQL->query('UPDATE parking_settings SET deadline=%s', $_REQUEST['deadline']);
			}
			if($_REQUEST['parking_admin_form'] == 'changestartdate') {
				$I2_SQL->query('UPDATE parking_settings SET startdate=%s', $_REQUEST['startdate']);
			}
			if($_REQUEST['parking_admin_form'] == 'sort') {
				$this->template_args['sort_selected'] = array();
				for($n = 1; $n <= 5; $n++) {
					$sort = $_REQUEST["sort$n"];
					$sortarr[] = $sortmap[$sort][0];
					$this->template_args['sort_selected'][$n] = $sort;

					// yes, i know this is not good form. for now, i don't care.
					$I2_SQL->query('UPDATE parking_settings SET sort'.$n.' = %s', $sort);
				}
			}
			if($_REQUEST['parking_admin_form'] == 'person_assign') {
				if($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE assigned=%d', $_REQUEST['spot'])->fetch_single_value() > 0 && ! isset($_REQUEST['override'])) {
					$info = $I2_SQL->query('SELECT uid, name FROM parking_apps WHERE assigned=%d', $_REQUEST['spot'])->fetch_array();
					$this->template_args['ask_override'] = TRUE;
					$this->template_args['override_id'] = $_REQUEST['person_id'];
					$this->template_args['override_name'] = $I2_SQL->query('SELECT name FROM parking_apps WHERE uid=%d', $_REQUEST['person_id'])->fetch_single_value();
					$this->template_args['override_spot'] = $_REQUEST['spot'];
					$this->template_args['override_othername'] = $info['name'];
					$this->template_args['override_otherid'] = $info['uid'];
				}
				else {
					$I2_SQL->query('UPDATE parking_apps SET assigned=%d WHERE uid=%d', $_REQUEST['spot'], $_REQUEST['person_id']);
				}
			}
		}

		if(count($sortarr) == 0) {
			$sorts = $I2_SQL->query('SELECT sort1, sort2, sort3, sort4, sort5 FROM parking_settings')->fetch_array();
			$this->template_args['sort_selected'] = array(	1 => $sorts['sort1'],
									2 => $sorts['sort2'],
									3 => $sorts['sort3'],
									4 => $sorts['sort4'],
									5 => $sorts['sort5']
								);
			foreach($this->template_args['sort_selected'] as $key => $val) {
				d($key . ": " . $val);
			}
			$sortarr = array(	$sortmap[$sorts['sort1']][0],
						$sortmap[$sorts['sort2']][0],
						$sortmap[$sorts['sort3']][0],
						$sortmap[$sorts['sort4']][0],
						$sortmap[$sorts['sort5']][0]
					);
		}

		$settings = $I2_SQL->query('SELECT * FROM parking_settings')->fetch_array();
		$this->template_args['deadline'] = $settings['deadline'];
		$this->template_args['startdate'] = $settings['startdate'];

		// update ALL the eighth period absences, so sorting works on current data...
		$uids = $I2_SQL->query('SELECT uid FROM parking_apps')->fetch_col('uid');
		foreach ($uids as $uid) {
			if($uid == NULL) {
				continue; // don't need to worry about special_name rows
			}
			$I2_SQL->query('UPDATE parking_apps SET skips=%d WHERE uid=%d', count(EighthSchedule::get_absences($uid)), $uid);
		}

		$this->template_args['people'] = array();
		$res = $I2_SQL->query('SELECT uid, name, special_name, mentorship, other_driver, assigned, grade, skips FROM parking_apps ORDER BY '.join(", ", $sortarr));
		while ($record = $res->fetch_array()) {
			$person = array();

			if($record['special_name'] != "") {
				$person['isTeacher'] = TRUE;
				$person['name'] = $record['special_name'];
			}
			else {
				$user = new User($record['uid']);
				if($user->objectclass == 'tjhsstTeacher') {
					$person['isTeacher'] = TRUE;
				}
				else {
					$person['isTeacher'] = FALSE;
				}
				$person['name'] = $record['name'];
			}

			$person['assigned'] = $record['assigned'];
			$person['id'] = $record['uid'];
			$person['grade'] = $record['grade'];
			d('mentorship: ' . $record['mentorship']);
			if($record['mentorship']) {
				$person['mentor'] = 'Y';
			}
			else {
				$person['mentor'] = 'N';
			}
			$person['skips'] = $record['skips'];
			$person['otherdriver'] = $record['other_driver'];
			$person['numcars'] = 0;
			$person['cars'] = array();
			$car_res = $I2_SQL->query('SELECT plate, make, model, color, year FROM parking_cars WHERE uid=%d', $record['uid']);
			$n = 0;
			while ($car = $car_res->fetch_array()) {
				$person['numcars']++;
				$car['index'] = $n++;
				$person['cars'][] = $car;
			}

			$this->template_args['people'][] = $person;
		}

		$this->template_args['options'] = $sortmap;

		$this->template = 'parking_admin.tpl';
	}

	function print_apps() {
		global $I2_USER, $I2_SQL;

		if(!$I2_USER->is_group_member('admin_parking')) {
			redirect('parking');
		}

		$sortmap = array(	'none' 			=> array('1', 'none (leave last on sorting if used)'),
					'nameasc' 		=> array('name', 'name, A-Z'),
					'namedesc' 		=> array('name DESC', 'name, Z-A'),
					'gradeasc' 		=> array('grade', 'grade, rising juniors first'),
					'gradedesc' 		=> array('grade DESC', 'grade, rising seniors first'),
					'skipsasc' 		=> array('skips', '8th period skips, least to most'),
					'skipsdesc' 		=> array('skips DESC', '8th period skips, most to least'),
					'mentorshipasc' 	=> array('mentorship', 'mentorship, not first'),
					'mentorshipdesc' 	=> array('mentorship DESC', 'mentorship, yes first'),
					'jointdesc' 		=> array('other_driver DESC', 'joint applications, first'),
					'assignedasc' 		=> array('assigned', 'assigned spot, ascending'),
					'assigneddesc' 		=> array('assigned DESC', 'assigned spot, descending'),
					'timeasc' 		=> array('timestamp', 'time submitted, earliest first'),
					'timedesc' 		=> array('timestamp DESC', 'time submtted, latest first'),
					'random' 		=> array('RAND()', 'randomize within last category')
				);
		$sortarr = array();
		if(count($sortarr) == 0) {
			$sorts = $I2_SQL->query('SELECT sort1, sort2, sort3, sort4, sort5 FROM parking_settings')->fetch_array();
			$sortarr = array(	$sortmap[$sorts['sort1']][0],
						$sortmap[$sorts['sort2']][0],
						$sortmap[$sorts['sort3']][0],
						$sortmap[$sorts['sort4']][0],
						$sortmap[$sorts['sort5']][0]
					);
		}

		// update ALL the eighth period absences, so sorting works on current data...
		$uids = $I2_SQL->query('SELECT uid FROM parking_apps')->fetch_col('uid');
		foreach ($uids as $uid) {
			if($uid == NULL) {
				continue; // don't need to worry about special_name rows
			}
			$I2_SQL->query('UPDATE parking_apps SET skips=%d WHERE uid=%d', count(EighthSchedule::get_absences($uid)), $uid);
		}

		$people = array();
		$res = $I2_SQL->query('SELECT uid, name, special_name, mentorship, other_driver, assigned, grade, skips FROM parking_apps ORDER BY '.join(", ", $sortarr));
		while ($record = $res->fetch_array()) {
			$person = array();

			if($record['special_name'] != "") {
				$person['isTeacher'] = TRUE;
				$person['name'] = $record['special_name'];
			}
			else {
				$user = new User($record['uid']);
				if($user->objectclass == 'tjhsstTeacher') {
					$person['isTeacher'] = TRUE;
				}
				else {
					$person['isTeacher'] = FALSE;
				}
				$person['name'] = $record['name'];
			}

			$person['assigned'] = $record['assigned'];
			$person['id'] = $record['uid'];
			$person['grade'] = $record['grade'];
			if($record['mentorship']) {
				$person['mentor'] = 'Y';
			}
			else {
				$person['mentor'] = 'N';
			}
			$person['skips'] = $record['skips'];
			$person['otherdriver'] = $record['other_driver'];
			$person['numcars'] = 0;
			$person['cars'] = array();
			$car_res = $I2_SQL->query('SELECT plate, make, model, color, year FROM parking_cars WHERE uid=%d', $record['uid']);
			$n = 0;
			while ($car = $car_res->fetch_array()) {
				$person['numcars']++;
				$car['index'] = $n++;
				$person['cars'][] = $car;
			}

			$people[] = $person;
		}
		Printing::print_parking($people, 'pdf');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER;
		$display->disp($this->template, $this->template_args);
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
		return "Parking Applications";
	}
}

?>
