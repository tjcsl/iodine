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
	 * WARNING: HACKY. HACKY. HACKY. BAD.
	 * The mysql query to select apps, correctly sorted, since I can't think
	 * of a good way to flexibly do this.
	 * BAD. BAD. BAD. HACKY.
	 */
	private $query = 'SELECT *,
			(skips + other_driver_skips * other_driver_approved) AS totalskips,
			(other_driver_approved * (skips <= 3 AND other_driver_skips * other_driver_approved <= 3)) as s0,
			((skips < 1 AND other_driver_skips * other_driver_approved < 1) * (NOT other_driver_approved)) as s1,
			(skips < 6 AND other_driver_skips * other_driver_approved < 6) as s2,
			(skips < 11 AND other_driver_skips * other_driver_approved < 11) as s3,
			(skips < 12 AND other_driver_skips * other_driver_approved < 12) as s4
			FROM parking_apps WHERE uid IS NOT NULL
			ORDER BY grade DESC, s0 DESC, s1 DESC, s2 DESC, s3 DESC, s4 DESC, RAND();';

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

		redirect('parking/apply');
	}

	function apply() {
		global $I2_USER, $I2_SQL;

		$settings = $I2_SQL->query('SELECT * FROM parking_settings')->fetch_array();

		if(! ($I2_USER->is_group_member('grade_10') || $I2_USER->is_group_member('grade_11') || $I2_USER->is_group_member('admin_parking'))) {
			redirect('');
		}

		$this->template_args['is_admin'] = $I2_USER->is_group_member('admin_parking');
		$this->template_args['deadline'] = date('F jS, Y', strtotime($settings['deadline']));
		$this->template_args['startdate'] = date('F jS, Y', strtotime($settings['startdate']));

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

			$eaddr = "";
			if(isset($_REQUEST['email'])) {
				$eaddr = $_REQUEST['email'];
			}

			$validform = true;
			if(isset($_REQUEST['email'])) {
				if(!ereg("^[^@]{1,64}@[^@]{1,255}$", $eaddr)) {
					$validform = false;
				}
			}

			if($_REQUEST['parking_apply_form'] == 'apply' && $validform) {

				if(isset($_REQUEST['mship'])) {
					$mship = 1;
				}
				else {
					$mship = 0;
				}

				if($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value() == 0) {
					$I2_SQL->query('INSERT INTO parking_apps SET uid=%d, mentorship=%d, name=%s, email=%s, skips=%d, grade=%d, timestamp=NOW()', $I2_USER->uid, $mship, $I2_USER->name_comma, $eaddr, count(EighthSchedule::get_absences($I2_USER->uid)), $I2_USER->grade);
				}
				else {
					$I2_SQL->query('UPDATE parking_apps SET mentorship=%d, skips=%d WHERE uid=%d', $mship, count(EighthSchedule::get_absences($I2_USER->uid)), $I2_USER->uid);
				}

				$I2_SQL->query('DELETE FROM parking_cars WHERE uid=%d', $I2_USER->uid);
				foreach ($_REQUEST['car'] as $car) {
					if ($car['plate'] != "") {
						$I2_SQL->query('INSERT INTO parking_cars SET uid=%d, plate=%s, make=%s, model=%s, year=%d', $I2_USER->uid, $car['plate'], $car['make'], $car['model'], $car['year']);
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

			//$app = $I2_SQL->query('SELECT timestamp, mentorship, other_driver, other_driver_approved FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_array();
			$app = $I2_SQL->query('SELECT email, timestamp, mentorship, other_driver, other_driver_approved FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_array();
			$this->template_args['email'] = $app['email'];
			$this->template_args['submitdate'] = date('F jS, Y',strtotime($app['timestamp']));
			$this->template_args['mship'] = $app['mentorship'];
			if (!empty($app['other_driver']) && is_numeric($app['other_driver'])) {
				$this->template_args['otherdriver'] = new User($app['other_driver']);
				$this->template_args['otherdriver_od'] = $I2_SQL->query('SELECT other_driver FROM parking_apps WHERE uid=%d', $app['other_driver'])->fetch_single_value();
			}

			$res = $I2_SQL->query('SELECT plate, make, model, year FROM parking_cars WHERE uid=%d', $I2_USER->uid);
			while($car = $res->fetch_array()) {
				$this->template_args['cars'][] = $car;
			}
		}
		else {
			/* so smarty doesn't whine about undefined indexes */
			$this->template_args['email'] = $this->template_args['submitdate'] = $this->template_args['mship'] = $this->template_args['otherdriver'] = "";
		}

		$res = $I2_SQL->query('SELECT uid FROM parking_apps WHERE other_driver=%d', $I2_USER->uid);
		$pot_parts = array();
		while ($row = $res->fetch_array()) {
			$pot_parts[] = new User($row['uid']);
		}
		$this->template_args['potential_partners'] = $pot_parts;

		/* so smarty doesn't whine about undefined indexes */
		while(count($this->template_args['cars']) < 4) {
			$this->template_args['cars'][] = array('plate' => '', 'make' => '', 'model' => '', 'year' => '');
		}

		$this->template = 'parking_apply.tpl';

	}

	function partner() {
		global $I2_USER, $I2_SQL, $I2_ARGS;

		$settings = $I2_SQL->query('SELECT * FROM parking_settings')->fetch_array();

		if(! ($I2_USER->is_group_member('grade_10') || $I2_USER->is_group_member('grade_11') || $I2_USER->is_group_member('admin_parking'))) {
			redirect('');
		}

		#print_r($I2_ARGS);

		if (isset($I2_ARGS[2]) && $I2_ARGS[2] == 'searchdone' && Search::get_results()) {
			$this->template_args['results_destination'] = 'parking/partner/select/';
			$this->template_args['return_destination'] = 'parking';
			$this->template_args['info'] = Search::get_results();
			if(count($this->template_args['info']) == 1) {
				redirect($this->template_args['results_destination'] . $this->template_args['info'][0]->uid);
			}
		}
		else if (isset($I2_ARGS[2]) && $I2_ARGS[2] == 'select') {
			if ($I2_SQL->query('SELECT other_driver_approved FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value()) {
				$olduid = $I2_SQL->query('SELECT other_driver FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value();
				$I2_SQL->query('UPDATE parking_apps SET other_driver_approved=0 WHERE uid=%d or uid=%d', $I2_USER->uid, $olduid);
			}
			$partner = new User($I2_ARGS[3]);
			if ($partner->grade != 10 && $partner->grade != 11) {
				$this->template_args['search_destination'] = 'parking/partner/searchdone';
				$this->template_args['action_name'] = 'Find Person';
				$this->template_args['message'] = 'Your may only do a joint application with a current sophomore or junior.';
			}
			else if ($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value() != 1) {
				$this->template_args['search_destination'] = 'parking/partner/searchdone';
				$this->template_args['action_name'] = 'Find Person';
				$this->template_args['message'] = 'You must first submit an application, then fill out the joint application section.';
			}
			else {
				$I2_SQL->query('UPDATE parking_apps SET other_driver=%d WHERE uid=%d', $partner->uid, $I2_USER->uid);
				$res = $I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE uid=%d AND other_driver=%d',
					$partner->uid, $I2_USER->uid);
				if ($res->fetch_single_value()) {
					$I2_SQL->query('UPDATE parking_apps SET other_driver_approved=1 WHERE uid=%d OR uid=%d',
						$I2_USER->uid, $partner->uid);
				}
				redirect('parking/apply');
			}
		}
		else if (isset($I2_ARGS[2]) && $I2_ARGS[2] == 'remove') {
			$olduid = $I2_SQL->query('SELECT other_driver FROM parking_apps WHERE uid=%d', $I2_USER->uid)->fetch_single_value();
			$I2_SQL->query('UPDATE parking_apps SET other_driver=NULL, other_driver_skips=0, other_driver_approved=0 WHERE uid=%d OR uid=%d', $I2_USER->uid, $olduid);
			redirect('parking/apply');
		}
		else {
			$this->template_args['search_destination'] = 'parking/partner/searchdone';
			$this->template_args['action_name'] = 'Find Person';
		}

		$this->template = 'parking_partner.tpl';
	}

	/**
	* Review/administrate submitted applications.
	*
	* Grabs applications, sorts, assigns spots, whatever else the security office people need.
	*/
	function admin() {
		global $I2_USER, $I2_SQL, $I2_QUERY;

		if(! $I2_USER->is_group_member('admin_parking')) {
			redirect('parking');
		}

		$sortmap = array(	'none' 			=> array('1', 'none (leave last on sorting if used)'),
					'nameasc' 		=> array('name', 'name, A-Z'),
					'namedesc' 		=> array('name DESC', 'name, Z-A'),
					'gradeasc' 		=> array('grade', 'grade, rising juniors first'),
					'gradedesc' 		=> array('grade DESC', 'grade, rising seniors first'),
					'skipsasc' 		=> array('(skips + other_driver_skips)', '8th period skips, least to most'),
					'skipsdesc' 		=> array('(skips + other_driver_skips) DESC', '8th period skips, most to least'),
					'mentorshipasc' 	=> array('mentorship', 'mentorship, not first'),
					'mentorshipdesc' 	=> array('mentorship DESC', 'mentorship, yes first'),
					'jointdesc' 		=> array('(other_driver!="")', 'joint applications, first'),
					'assignedasc' 		=> array('assigned_sort', 'assigned spot, ascending'),
					'assigneddesc' 		=> array('assigned_sort DESC', 'assigned spot, descending'),
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
				if($I2_SQL->query('SELECT COUNT(*) FROM parking_apps WHERE assigned=%s', $_REQUEST['spot'])->fetch_single_value() > 0 && ! isset($_REQUEST['override'])) {
					$info = $I2_SQL->query('SELECT uid, name FROM parking_apps WHERE assigned=%s', $_REQUEST['spot'])->fetch_array();
					$this->template_args['ask_override'] = TRUE;
					$this->template_args['override_id'] = $_REQUEST['person_id'];
					$this->template_args['override_name'] = $I2_SQL->query('SELECT name FROM parking_apps WHERE uid=%d', $_REQUEST['person_id'])->fetch_single_value();
					$this->template_args['override_spot'] = $_REQUEST['spot'];
					$this->template_args['override_othername'] = $info['name'];
					$this->template_args['override_otherid'] = $info['uid'];
				}
				else {
					$I2_SQL->query('UPDATE parking_apps SET assigned=%s WHERE uid=%d', $_REQUEST['spot'], $_REQUEST['person_id']);
					if ($other = $I2_SQL->query('SELECT other_driver FROM parking_apps WHERE uid=%d', $_REQUEST['person_id'])->fetch_single_value()) {
						$I2_SQL->query('UPDATE parking_apps SET assigned=%s WHERE uid=%d', $_REQUEST['spot'], $other);
					}
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
			$I2_SQL->query('UPDATE parking_apps SET other_driver_skips=%d WHERE other_driver=%d', count(EighthSchedule::get_absences($uid)), $uid);
		}

		$this->template_args['people'] = array();
		$driveruids = array();
		//$res = $I2_SQL->query('SELECT assigned FROM parking_apps ORDER BY '.join(", ", $sortarr));
		//while ($record = $res->fetch_array()) {
		//	print_r($record);
		//}
		//die();
		//$res = $I2_SQL->query('SELECT uid, name, special_name, mentorship, other_driver, assigned, grade, (skips + other_driver_skips) AS skips, CAST(assigned AS UNSIGNED INTEGER) AS assigned_sort FROM parking_apps ORDER BY '.join(", ", $sortarr));
		$res = $I2_SQL->query($this->query);
		while ($record = $res->fetch_array()) {
			if (in_array($record['uid'], $driveruids)) {
				// joint app, caught on partner's go-around
				continue;
			}

			$person = array();

			if($record['special_name'] != "") {
				$person['isTeacher'] = TRUE;
				$person['name'] = $record['special_name'];
			}
			else {
				try {
					$user = new User($record['uid']);
					if($user->objectclass == 'tjhsstTeacher') {
						$person['isTeacher'] = TRUE;
					}
					else {
						$person['isTeacher'] = FALSE;
					}
				} catch (I2Exception $e) {
					$user = new FakeUser($record['uid'],$record['name']);
					$person['isTeacher'] = FALSE;
				}
				if ($record['other_driver'] && $record['other_driver_approved']) {
					
					try {
						$otherdriver = new User($record['other_driver']);
					} catch (Exception $e) {
						$otherdriver = new FakeUser($record['other_driver'],'Someone Else');
					}
					$person['name'] = $record['name'] . ' AND ' . $otherdriver->name_comma;
					$driveruids[] = $record['uid'];
					$driveruids[] = $otherdriver->uid;
				}
				else {
					$person['name'] = $record['name'];
				}
			}

			$person['assigned'] = $record['assigned'];
			//d('assigned: ' . $record['assigned'], 1);
			$person['id'] = $record['uid'];
			$person['grade'] = $record['grade'];
			$person['email'] = "<a href=\"mailto:" . $record['email'] . "\">" . $record['email'] . "</a>";
			//d('mentorship: ' . $record['mentorship']);
			if($record['mentorship']) {
				$person['mentor'] = 'Y';
			}
			else {
				$person['mentor'] = 'N';
			}
			$person['skips'] = $record['totalskips'];
			if (!empty($record['other_driver']) && is_numeric($record['other_driver'])) {
				try {
					$person['otherdriver'] = new User($record['other_driver']);
				} catch (I2Exception $e) {
					$person['otherdriver'] = new FakeUser($record['other_driver'],'Someone Else');
				}
			}
			$person['numcars'] = 0;
			$person['cars'] = array();
			$car_res = $I2_SQL->query('SELECT plate, make, model, year FROM parking_cars WHERE uid=%d', $record['uid']);
			$n = 0;
			while ($car = $car_res->fetch_array()) {
				$person['numcars']++;
				$car['index'] = $n++;
				$person['cars'][] = $car;
			}
			$car_res = $I2_SQL->query('SELECT plate, make, model, year FROM parking_cars WHERE uid=%d', $record['other_driver']);
			while ($car = $car_res->fetch_array()) {
				$person['numcars']++;
				$car['index'] = $n++;
				$person['cars'][] = $car;
			}

			$this->template_args['people'][] = $person;
		}

		// Sort the data if the user wants it.
		if (isset($I2_QUERY['sort'])) {
			switch ($I2_QUERY['sort']) {
				case 'name'   :
					usort($this->template_args['people'],"Parking::compare_name");
					$this->template_args['sort']='name';
					break;
				case 'spot'   :
					usort($this->template_args['people'],"Parking::compare_spot");
					$this->template_args['sort']='spot';
					break;
				case 'year'   :
					usort($this->template_args['people'],"Parking::compare_year");
					$this->template_args['sort']='year';
					break;
				case 'mentor' :
					usort($this->template_args['people'],"Parking::compare_mentor");
					$this->template_args['sort']='mentor';
					break;
				case 'skips'  :
					usort($this->template_args['people'],"Parking::compare_skips");
					$this->template_args['sort']='skips';
					break;
				case 'email'  :
					usort($this->template_args['people'],"Parking::compare_email");
					$this->template_args['sort']='email';
					break;
				case 'name_reverse'  :
					usort($this->template_args['people'],"Parking::compare_name");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='name_reverse';
					break;
				case 'spot_reverse'  :
					usort($this->template_args['people'],"Parking::compare_spot");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='spot_reverse';
					break;
				case 'year_reverse'  :
					usort($this->template_args['people'],"Parking::compare_year");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='year_reverse';
					break;
				case 'mentor_reverse':
					usort($this->template_args['people'],"Parking::compare_mentor");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='mentor_reverse';
					break;
				case 'skips_reverse' :
					usort($this->template_args['people'],"Parking::compare_skips");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='skips_reverse';
					break;
				case 'email_reverse' :
					usort($this->template_args['people'],"Parking::compare_email");
					$this->template_args['people'] = array_reverse($this->template_args['people']);
					$this->template_args['sort']='email_reverse';
					break;
			}
		}

		$this->template_args['options'] = $sortmap;

		$this->template = 'parking_admin.tpl';
	}

	// Sorting functions
	static function compare_name($person1, $person2) {
		return strnatcmp($person1['name'],$person2['name']);
	}
	static function compare_spot($person1, $person2) {
		//if($person1['assigned']==$person2['assigned'])
		//	return strnatcmp($person1['name'],$person2['name']);
		return strnatcmp($person1['assigned'],$person2['assigned']);
	}
	static function compare_year($person1, $person2) {
		if($person1['grade']==$person2['grade'])
			return strnatcmp($person1['name'],$person2['name']);
		return strnatcmp($person1['grade'],$person2['grade']);
	}
	static function compare_mentor($person1, $person2) {
		if($person1['mentor']==$person2['mentor'])
			return strnatcmp($person1['name'],$person2['name']);
		return strnatcmp($person1['mentor'],$person2['mentor']);
	}
	static function compare_skips($person1, $person2) {
		if($person1['skips']==$person2['skips'])
			return strnatcmp($person1['name'],$person2['name']);
		return strnatcmp($person1['skips'],$person2['skips']);
	}
	static function compare_email($person1, $person2) {
		if($person1['email']==$person2['email'])
			return strnatcmp($person1['name'],$person2['name']);
		return strnatcmp($person1['email'],$person2['email']);
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
					'skipsasc' 		=> array('(skips + other_driver_skips)', '8th period skips, least to most'),
					'skipsdesc' 		=> array('(skips + other_driver_skips) DESC', '8th period skips, most to least'),
					'mentorshipasc' 	=> array('mentorship', 'mentorship, not first'),
					'mentorshipdesc' 	=> array('mentorship DESC', 'mentorship, yes first'),
					'jointdesc' 		=> array('(other_driver!="")', 'joint applications, first'),
					'assignedasc' 		=> array('assigned_sort', 'assigned spot, ascending'),
					'assigneddesc' 		=> array('assigned_sort DESC', 'assigned spot, descending'),
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
			$I2_SQL->query('UPDATE parking_apps SET other_driver_skips=%d WHERE other_driver=%d', count(EighthSchedule::get_absences($uid)), $uid);
		}

		$people = array();
		$res = $I2_SQL->query($this->query);
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
				if ($record['other_driver']) {
					$otherdriver = new User($record['other_driver']);
					$person['name'] = $record['name'] . ' AND ' . $otherdriver->name_comma;
					$driveruids[] = $record['uid'];
					$driveruids[] = $otherdriver->uid;
				}
				else {
					$person['name'] = $record['name'];
				}
			}

			$person['assigned'] = $record['assigned'];
			$person['id'] = $record['uid'];
			$person['grade'] = $record['grade'];
			$person['email'] = $record['email'];
			if($record['mentorship']) {
				$person['mentor'] = 'Y';
			}
			else {
				$person['mentor'] = 'N';
			}
			$person['skips'] = $record['totalskips'];
			if (!empty($record['other_driver']) && is_numeric($record['other_driver'])) {
				$user = new User($record['other_driver']);
				$person['otherdriver'] = $user->name;
			}
			$person['numcars'] = 0;
			$person['cars'] = array();
			$car_res = $I2_SQL->query('SELECT plate, make, model, year FROM parking_cars WHERE uid=%d', $record['uid']);
			$n = 0;
			while ($car = $car_res->fetch_array()) {
				$person['numcars']++;
				$car['index'] = $n++;
				$person['cars'][] = $car;
			}
			$car_res = $I2_SQL->query('SELECT plate, make, model, year FROM parking_cars WHERE uid=%d', $record['other_driver']);
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
