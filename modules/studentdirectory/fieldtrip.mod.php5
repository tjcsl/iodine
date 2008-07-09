<?php
/**
* Just contains the definition for the class {@link Fieldtrip}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage StudentDirectory
* @filesource
*/

/**
* This module helps teachers find students' teachers in particular periods.
* @package modules
* @subpackage StudentDirectory
*/
class Fieldtrip implements Module {
	
	private $template;
	private $template_args = array();
		
	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_ROOT,$I2_SQL,$I2_ARGS,$I2_USER;

		$admin = FALSE;
		$teacher = FALSE;

		if ($I2_USER->is_group_member('admin_all')) {
			$admin = TRUE;
		}

		if ($I2_USER->grade == 'staff') {
			$teacher = TRUE;
		}

		if (!$admin && !$teacher) {
			return FALSE;
		}

		$this->template_args['student'] = FALSE;
		if ($admin && !$teacher) {
			$this->template_args['student'] = TRUE;
		}

		$teachers = array();

		if (array_key_exists('fieldtrip_submit', $_REQUEST)) {
			$input = trim($_REQUEST['students']);
			$students = split("[, \n\r\t]+", $input);
			$periods = range(1,7);
			if (array_key_exists('periods', $_REQUEST)) {
				$periods = $_REQUEST['periods'];
			}

			foreach ($students as $student) {
				$user = new User($student);
				$sections = $user->schedule();
				foreach ($sections as $section) {
					if (in_array($section->period, $periods) && in_array($_REQUEST['quarter'], $section->quarters)) {
						$tchr = $section->teacher->name_comma;
						$pd = $section->period;
						if (!array_key_exists($tchr, $teachers)) {
							$teachers[$tchr] = array();
						}
						if (!array_key_exists($pd, $teachers[$tchr])) {
							$teachers[$tchr][$pd] = array();
						}
						if (!in_array($user, $teachers[$tchr][$pd])) {
							$teachers[$tchr][$pd][] = $user;
						}
					}
				}
			}

			ksort($teachers);
			foreach ($teachers as $tchr => $pds) {
				ksort($teachers[$tchr]);
				foreach ($pds as $pd => $students) {
					usort($teachers[$tchr][$pd], array('Fieldtrip', 'sort_lastname'));
				}
			}

			$this->template = "fieldtrip_out.tpl";
			$this->template_args['teachers'] = $teachers;
		}
		else {
			$this->template = "fieldtrip.tpl";
			$this->template_args['periods'] = range(1,7);
		}
		
		return "Get Students' Teachers by Period";
	}
	
	private function teacherperiodsort($one, $two) {
			  $diff = strcasecmp($one['class']->teacher->name_comma,$two['class']->teacher->name_comma);
			  if ($diff != 0) {
			  	return $diff;
			  }
			  return $one['class']->period-$two['class']->period;
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
		return 'Search the Directory'; // right now we don't need to get any initial values, the box will just contain a form like the old intranet for queries
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
		$display->disp('studentdirectory_box.tpl');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'Fieldtrip';
	}

	public static function sort_lastname($a, $b) {
		return strcmp($a->name_comma, $b->name_comma);
	}

	public static function sort_name($a, $b) {
		return strcmp($a->name, $b->name);
	}

	public static function sort_teacher($a, $b) {
		return strcmp($a->teacher->name_comma, $b->teacher->name_comma);
	}

	public static function sort_period($a, $b) {
		$tem = strcmp($a->period, $b->period);
		if($tem == 0)
			//sub-sort by term
			return sort_term($a, $b);
		return $tem;
	}

	public static function sort_room($a, $b) {
		return strcmp($a->room, $b->room);
	}

	public static function sort_term($a, $b) {
		if (count($a->quarters) < count($b->quarters)) {
			return -1;
		}
		if (count($a->quarters) > count($b->quarters)) {
			return 1;
		}
		for ($x = 0; $x < count($a->quarters); $x++) {
			if ($a->quarters[$x] < $b->quarters[$x]) {
				return -1;
			}
			if ($a->quarters[$x] > $b->quarters[$x]) {
				return 1;
			}
		}
		return 0;
	}

}

?>
