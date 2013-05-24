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
class Fieldtrip extends Module {
	
	private $template;
	private $template_args = [];

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

		if (array_key_exists('fieldtrip_submit', $_REQUEST)) {
			$input = trim($_REQUEST['students']);
			$students = split("[, \n\r\t]+", $input);
			$periods = range(1,7);
			if (array_key_exists('periods', $_REQUEST)) {
				$periods = $_REQUEST['periods'];
			}

			$teachers = [];
			$notfound = [];
			foreach ($students as $student) {
				try {
					$user = new User($student);
				}
				catch (I2Exception $e) {
					$notfound[] = $student;
					continue;
				}
				$sections = $user->schedule();
				foreach ($sections as $section) {
					if (in_array($section->period, $periods) && in_array($_REQUEST['quarter'], $section->quarters)) {
						$tchr = $section->teacher->name_comma;
						$pd = $section->period;
						if (!array_key_exists($tchr, $teachers)) {
							$teachers[$tchr] = [];
						}
						if (!array_key_exists($pd, $teachers[$tchr])) {
							$teachers[$tchr][$pd] = [];
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
			$this->template_args['notfound'] = $notfound;
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
	function get_name() {
		return 'Fieldtrip';
	}

	public static function sort_lastname($a, $b) {
		return strcmp($a->name_comma, $b->name_comma);
	}

}

?>
