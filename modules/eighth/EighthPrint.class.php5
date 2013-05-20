<?php
/**
* Just contains the definition for the class {@link EighthPrint}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the utilities for an eighth period printing.
* @package modules
* @subpackage Eighth
*/

class EighthPrint {

	public static $sections = [];
	public static $printing_path = NULL;
	private static $author = 'Eighth Period Office';
	private static $creator = 'Eighth Period Office Online';

	/**
	* Print the activity rosters for the given date and block(s).
	*
	* @access public
	* @param int The activity ID
	* @param int The block ID
	* @param string The output format
	*/
	public static function print_class_roster($aid, $bid, $format = 'print') {
		Eighth::check_admin();
		$activity = new EighthActivity($aid, $bid);
		$output = Printing::latexify('class_roster');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Class Roster', "{$activity->name} ({$activity->block->date} - {$activity->block->block} Block)", $creator);
			}
			Printing::do_display($output, $format, "Class Roster for {$activity->name} ({$activity->block->date} - {$activity->block->block} Block)", 'EighthPrinting');
		}
	}

	/**
	* Print the activity rosters for the given date and block(s).
	*
	* @access public
	* @param int The sponsor ID
	* @param string The output format
	*/
	public static function print_sponsor_schedule($sid, $format = 'print') {
		$sponsor = new EighthSponsor($sid);
		$output = Printing::latexify('sponsor_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Sponsor Schedule', $sponsor->name, $creator);
			}
			Printing::do_display($output, $format, "Sponsor Schedule for {$sponsor->name}",'EighthPrinting');
		}
	}

	public static function print_activity_schedule($aid, $format = 'print') {
		$activity = new EighthActivity($aid);
		$output = Printing::latexify('activity_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Activity Schedule', $activity->name, $creator);
			}
			Eighth::do_display($output, $format, "Activity Schedule for {$activity->name}",'EighthPrinting');
		}
	}
	
	/**
	* Print the attendance data for the given bloack and activity.
	*
	* @access public
	* @param int The activity ID
	* @param int The block ID
	* @param string The output format
	*/
	public static function print_attendance_data($aid, $bid, $format = 'print') {
		$activity = new EighthActivity($aid, $bid);
		$output = Printing::latexify('attendance_data');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Attendance Data', "{$activity->name} ({$activity->block->date} - {$activity->block->block} Block)", $creator);
			}
			Printing::do_display($output, $format, "Attendance Data for {$activity->name} ({$activity->block->date} - {$activity->block->block} Block)",'EighthPrinting');
		}
	}

	/**
	* Print the activity rosters for the given date and block(s).
	*
	* @access public
	* @param array The block IDs
	* @param string The output format
	*/
	public static function print_activity_rosters($bids, $color, $format = "print") {
		global $I2_SQL;
		$color = str_replace('-', ',', $color);
		$activities = EighthActivity::id_to_activity($I2_SQL->query('SELECT activityid,bid FROM eighth_block_map WHERE bid IN (%D) ORDER BY activityid ASC, bid ASC', $bids)->fetch_all_arrays(MYSQLI_NUM));
		usort($activities, array('EighthPrint', 'sort_by_pickup_then_sponsor'));
		$block = NULL;
		$blocks = [];
		foreach($bids as $bid) {
			$block = new EighthBlock($bid);
			$blocks[] = "{$block->date} ({$block->block} block)";
		}
		$output = Printing::latexify('activity_rosters');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Activity Rosters', implode(', ', $blocks) . ' Activity Rosters', $creator);
			}
			Printing::do_display($output, $format, 'Activity Rosters for ' . implode(', ', $blocks),'EighthPrinting');
		}
	}

	/**
	* Print a student's schedule.
	*
	* @access public
	* @param int The user's ID
	* @param string The print format.
	*/
	public static function print_student_schedule($uid, $start_date = NULL, $format = 'html') {
		global $I2_USER;
		$user = new User($uid);
		if ($user->uid != $I2_USER->uid) {
			Eighth::check_admin();
		}
		$activities = EighthActivity::id_to_activity(EighthSchedule::get_activities($uid, $start_date));
		$absences = EighthSchedule::get_absences($uid);
		$output = Printing::latexify('student_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == "print") {
			Printing::do_print($output);
		}
		else {
			if($format == "pdf") {
				Printing::add_info($output, $author, 'Print Student Schedule', $user->name, $creator);
			}
			Printing::do_display($output, $format, "Student Schedule for {$user->name}", TRUE,'EighthPrinting');
		}
	}
	
	/**
	* Print the room utilizations.
	*
	* @access public
	* @param int The user's ID
	* @param string The print format.
	*/
	public static function print_room_utilization($bid, $format = 'pdf') {
		$block = new EighthBlock($bid);
		$utilizations = EighthRoom::get_utilization($bid);
		$output = Printing::latexify('room_utilization');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			Printing::do_print($output);
		}
		else {
			if($format == 'pdf') {
				Printing::add_info($output, $author, 'Print Room Utilization', "{$activity->block->date} - {$activity->block->block} Block", $creator);
			}
			Printing::do_display($output, $format, "Room Utilization for {$activity->block->date} - {$activity->block->block} Block", 'EighthPrinting', TRUE);
		}
	}

	public static function sort_by_sponsor($act1, $act2) {
		return strcasecmp($act1->block_sponsors_comma, $act2->block_sponsors_comma);
	}
	public static function sort_by_pickup_then_sponsor($act1, $act2) {
		$cmp = strcasecmp($act1->pickups_comma, $act2->pickups_comma);
		return $cmp ? $cmp : self::sort_by_sponsor($act1, $act2);
	}

}
