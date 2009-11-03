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

	public static $sections = array();
	public static $printing_path = NULL;

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
		$output = self::latexify('class_roster');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Class Roster', "{$activity->name} ({$activity->block->date} - {$activity->block->block} Block)");
			}
			self::do_display($output, $format, "Class Roster for {$activity->name} ({$activity->block->date} - {$activity->block->block} Block)");
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
		$output = self::latexify('sponsor_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Sponsor Schedule', $sponsor->name);
			}
			self::do_display($output, $format, "Sponsor Schedule for {$sponsor->name}");
		}
	}

	public static function print_activity_schedule($aid, $format = 'print') {
		$activity = new EighthActivity($aid);
		$output = self::latexify('activity_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Activity Schedule', $activity->name);
			}
			self::do_display($output, $format, "Activity Schedule for {$activity->name}");
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
		$output = self::latexify('attendance_data');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Attendance Data', "{$activity->name} ({$activity->block->date} - {$activity->block->block} Block)");
			}
			self::do_display($output, $format, "Attendance Data for {$activity->name} ({$activity->block->date} - {$activity->block->block} Block)");
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
		$activities = EighthActivity::id_to_activity($I2_SQL->query('SELECT activityid,bid FROM eighth_block_map WHERE bid IN (%D) ORDER BY activityid ASC, bid ASC', $bids)->fetch_all_arrays(MYSQL_NUM));
		usort($activities, array('EighthPrint', 'sort_by_pickup_then_sponsor'));
		$block = NULL;
		$blocks = array();
		foreach($bids as $bid) {
			$block = new EighthBlock($bid);
			$blocks[] = "{$block->date} ({$block->block} block)";
		}
		$output = self::latexify('activity_rosters');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Activity Rosters', implode(', ', $blocks) . ' Activity Rosters');
			}
			self::do_display($output, $format, 'Activity Rosters for ' . implode(', ', $blocks));
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
		$output = self::latexify('student_schedule');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == "print") {
			self::do_print($output);
		}
		else {
			if($format == "pdf") {
				self::add_info($output, 'Print Student Schedule', $user->name, TRUE);
			}
			self::do_display($output, $format, "Student Schedule for {$user->name}", TRUE);
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
		$output = self::latexify('room_utilization');
		ob_start();
		eval($output);
		$output = ob_get_clean();
		if($format == 'print') {
			self::do_print($output);
		}
		else {
			if($format == 'pdf') {
				self::add_info($output, 'Print Room Utilization', "{$activity->block->date} - {$activity->block->block} Block", TRUE);
			}
			self::do_display($output, $format, "Room Utilization for {$activity->block->date} - {$activity->block->block} Block", TRUE);
		}
	}

	public static function sort_by_sponsor($act1, $act2) {
		return strcasecmp($act1->block_sponsors_comma, $act2->block_sponsors_comma);
	}
	public static function sort_by_pickup_then_sponsor($act1, $act2) {
		$cmp = strcasecmp($act1->pickups_comma, $act2->pickups_comma);
		return $cmp ? $cmp : self::sort_by_sponsor($act1, $act2);
	}

	/**
	* Prints the given LaTeX output.
	*
	* @access private
	* @param string The LaTeX output to print.
	*/
	private static function do_print($output, $landscape = FALSE) {
		$temp = tempnam('/tmp', 'EighthPrinting');
		file_put_contents($temp, $output);
		exec("cd /tmp; latex {$temp}");
		exec("cd /tmp; dvips {$temp}.dvi -t letter" . ($landscape ? ' -t landscape' : ''));
		$ftpconn = ftp_connect(Eighth::printer_ip());
		ftp_login($ftpconn, 'anonymous', '');
		ftp_chdir($ftpconn, 'PORT1');
		ftp_put($ftpconn, "{$temp}.ps", "{$temp}.ps", FTP_BINARY);
		ftp_close($ftpconn);
		unlink($temp . ".ps");
		unlink($temp . ".dvi");
	}
	
	/**
	* Displays the given LaTeX output.
	*
	* @access private
	* @param string The LaTeX output to print.
	*/
	private static function do_display($output, $format, $filename, $landscape = FALSE) {
		Display::stop_display();
		$temp = tempnam('/tmp', 'EighthPrinting');
		file_put_contents("{$temp}", $output);
		//$disposition = 'attachment';
		$disposition = 'inline';
		if($format == 'pdf') {
			exec("cd /tmp; pdflatex {$temp}");
			exec("cd /tmp; pdflatex {$temp}");
			header('Content-type: application/pdf');
		}
		else if($format == 'ps') {
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; dvips {$temp}.dvi -t letter" . ($landscape ? ' -t landscape' : ''));
			header("Content-type: application/postscript");
		}
		else if($format == 'dvi') {
			exec("cd /tmp; latex {$temp}");
			exec("cd /tmp; latex {$temp}");
			header('Content-type: application/x-dvi');
		}
		else if($format == 'tex' || $format == 'latex') {
			rename($temp, "{$temp}.{$format}");
			header('Content-type: text/plain');
		}
		else if($format == 'html') {
			header('Content-type: text/html');
			$disposition = 'inline';
		}
		else if($format == 'rtf') {
			rename($temp, "{$temp}.tex");
			exec("cd /tmp; latex2rtf {$temp}");
			header('Content-type: application/rtf');
		}
		header("Content-Disposition: {$disposition}; filename=\"{$filename}.{$format}\"");
		header("Pragma: ");
		readfile("{$temp}.{$format}");
	}

	/**
	* Takes input file and makes it into valid latex output
	*
	* @access private
	* @param $filename string The filename
	*/
	private static function latexify($filename) {
		if(!self::$printing_path) {
			self::$printing_path = i2config_get('printing_path', NULL, 'eighth');
		}
		$lines = file(self::$printing_path . "{$filename}.tex.in");
		self::$sections = array();
		$currsections = array();
		$code = '';
		$output = '';
		$echoed = FALSE;
		$incode = FALSE;
		foreach($lines as $line) {
			$line = trim($line);
			if(preg_match('/^\%\@begin (.*)$/', $line, $matches)) {
				$currsections[] = $matches[1];
				self::$sections[$matches[1]] = '';
			}
			else if(preg_match('/^\%\@end (.*)$/', $line, $matches)) {
				unset($currsections[array_search($matches[1], $currsections)]);
			}
			else if(preg_match('/^\%\@include (.*)$/', $line, $matches)) {
				if(count($currsections) == 0) {
					if(!$echoed) {
						$output .= "echo '";
						$echoed = TRUE;
					}
					$output .= self::$sections[$matches[1]];
				}
				else {
					foreach($currsections as $section) {
						self::$sections[$section] .= self::$sections[$matches[1][0]];
					}
				}
			}
			else if(substr($line, 0, 3) == '%@?') {
				$output .= "';\n";
				$echoed = FALSE;
				$incode = TRUE;
				if(substr($line, 3) != '') {
					if(substr($line, -2) != '@%') {
						$code .= substr($line, 3) . "\n";
					}
					else {
						$code .= substr($line, 3, -2);
						$output .= "{$code}\n";
						$code = '';
						$incode = FALSE;
					}
				}
			}
			else if($incode && substr($line, -2) == '@%') {
				$output .= "{$code}\n";
				$code = '';
				$incode = FALSE;
			}
			else {
				$line = preg_replace('/%@(.*?)@%/', '\' . strtr($1, array(\'$\' => \'\\\\$\', \'&\' => \'\&\', \'%\' => \'\%\', \'{\' => \'\{\', \'}\' => \'\}\', \'_\' => \'\_\', \'#\' => \'\#\')) . \'', $line);
				$line = strtr($line, array('\\' => '\\\\'));
				if(count($currsections) == 0) {
					if($incode) {
						$code .= "{$line}\n";
					}
					else {
						if(!$echoed) {
							$output .= "echo '";
							$echoed = TRUE;
						}
						$output .= "{$line}\n";
					}
				}
				else {
					foreach($currsections as $section) {
						self::$sections[$section] .= "{$line}\n";
					}
				}
			}
		}
		if($echoed) {
			$output .= "';";
		}
		return $output;
	}

	/**
	* Add PDF information to the file
	*
	* @access private
	* @param string The file contents
	* @param string Producer
	* @param string Title
	*/
	private static function add_info(&$output, $producer = '', $title = '') {
		$output = "\pdfinfo {
/Author (Eighth Period Office)
/Producer ({$producer})
/Title ({$title})
/Creator (Eighth Period Office Online)
}
{$output}";
	}
}
