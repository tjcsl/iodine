<?php
/**
* Just contains the definition for the class {@link BellSchedule}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2012 The Intranet 2 Development Team
* @package modules
* @subpackage BellSchedule
* @filesource
*/

/**
* The module that pulls bell schedules from the TJ calendar
* @package modules
* @subpackage BellSchedule
*/
class BellSchedule implements Module {

	/**
	* The url to retrive the calendar from.
	* Must be https to avoid being cached by the proxy.
	*/
	private static $url = 'https://www.calendarwiz.com/CalendarWiz_iCal.php?crd=tjhsstcalendar';
	
	/**
	* The normal schedules
	*/
	private static $normalSchedules = [
		'anchor' => [
			'description' => 'Anchor Day',
			'schedule' => 'Period 1: 8:30 - 9:15<br />Period 2: 9:25 - 10:05<br />Period 3: 10:15 - 10:55<br />Period 4: 11:05 - 11:45<br />
				Lunch: 11:45 - 12:35<br />Period 5: 12:35 - 1:15<br />Period 6: 1:25 - 2:05<br />Period 7: 2:15 - 2:55<br />Break: 2:55 - 3:10<br />Period 8: 3:10 - 3:50'
		],
		'blue' => [
			'description' => 'Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:05<br />Period 2: 10:15 - 11:45<br />
				Lunch: 11:45 - 12:30<br />Period 3: 12:30 - 2:05<br />Break: 2:05 - 2:20<br />Period 4: 2:20 - 3:50'
		],
		'red' => [
			'description' => 'Red Day',
			'schedule' => 'Period 5: 8:30 - 10:05<br />Period 6: 10:15 - 11:45<br />
				Lunch: 11:45 - 12:30<br />Period 7: 12:30 - 2:05<br />Break: 2:05 - 2:20<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		],
		'jlcblue' => [
			'description' => 'JLC Blue Day',
			'schedule' => 'JLC: 8:00 - 8:55<br />Period 1: 9:00 - 10:28<br />Period 2: 10:37 - 12:05
				<br />Lunch: 12:05 - 12:45<br />Period 3: 12:45 - 2:13<br />Break: 2:13 - 2:22<br />Period 4: 2:22 - 3:50'
		],
		'telelearn' => [
			'description' => 'Telelearn Day',
			'schedule' => 'Period 1: 8:30 - 9:05<br />Period 2: 9:10 - 9:45<br />Period 3: 9:50 - 10:25<br />Period 4: 10:30 - 11:05
				<br />Lunch: 11:05 - 11:55<br />Period 5: 11:55 - 12:30<br />Period 6: 12:35 - 1:10<br />Period 7: 1:15 - 1:50'
		],
		'telelearnanchor' => [
			'description' => 'Telelearn Day',
			'schedule' => 'Period 1: 8:30 - 9:05<br />Period 2: 9:10 - 9:45<br />Period 3: 9:50 - 10:25<br />Period 4: 10:30 - 11:05
				<br />Lunch: 11:05 - 11:55<br />Period 5: 11:55 - 12:30<br />Period 6: 12:35 - 1:10<br />Period 7: 1:15 - 1:50'
		],
		'bluemidterm' => [
			'description' => 'Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 8:30 - 10:30<br />Period 2: 10:40 - 11:45<br />Period 3: 12:30 - 2:30<br />Period 4: 2:40 - 3:50'
		],
		'red1midterm' => [
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 5: 8:30 - 10:30<br />Period 6: 10:40 - 11:45<br />Period 7: 12:30 - 2:30<br />Period 8A: 2:40 - 3:10<br />Period 8B: 3:20 - 3:50'
		],
		'jlcmidterm' => [
			'description' => 'JLC Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 9:00 - 9:55<br />Period 2: 10:05 - 12:05<br />Period 3: 12:45 - 1:40<br />Period 4: 1:50 - 3:50'
		],
		'red2midterm' => [
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 6: 8:30 - 10:30<br />Period 5: 10:40 - 11:45<br />Period 7: 12:30 - 1:35<br />Period 8A: 1:50 - 2:45<br />Period 8B: 2:55 - 3:50'
		],
		'amcblueday' => [
			'description' => 'AMC Blue Day',
			'schedule' => 'AMC/Study Hall: 8:30 - 10:00<br />Period 1: 10:10 - 11:20<br />
				Lunch: 11:20 - 12:00<br />Period 2: 12:00 - 1:10<br />Period 3: 1:20 - 2:30<br />Period 4: 2:40 - 3:50'
		],
		'noschool' => ['description' => 'No school', 'schedule' => '']
	];

	/**
	 * The schedules for AP weeks
	 * FIXME: currently hardcoded for 2013. Should be a lot more generic.
	*/
	private static $apExamSchedule = [
		4 => ['description' => 'No school', 'schedule' => ''],
		5 => ['description' => 'No school', 'schedule' => ''],
		6 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:20<br />Period 2: 10:30 - 12:15<br />Lunch: 12:25 - 1:00<br />Period 3: 1:00 - 2:20<br />Period 4: 2:30 - 3:50'
		],
		7 => [
			'description' => 'Modified Red Day',
			'schedule' => 'Period 7: 8:30 - 10:20<br />Period 6: 10:30 - 12:15<br />Lunch: 12:15 - 1:00<br />Period 5: 1:00 - 2:20<br />Period 8A: 2:30 - 3:05<br />Period 8B: 3:15 - 3:50'
		],
		8 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 4: 8:30 - 10:25<br />Period 3: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 2: 1:15 - 2:30<br />Period 1: 2:40 - 3:50'
		],
		9 => [
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:10<br />Period 7: 10:20 - 12:00<br />Lunch: 12:00 - 12:45<br />Period 6: 12:45 - 2:05<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		],
		10 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:25<br />Period 2: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 3: 1:15 - 2:20<br />Period 4: 2:30 - 3:50'
		],
		13 => [
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:10<br />Period 6: 10:20 - 12:00<br />Lunch: 12:00 - 12:45<br />Period 7: 12:45 - 2:15<br />Period 8A: 2:30 - 3:05<br />Period 8B: 3:15 - 3:50'
		],
		14 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:05<br />Period 2: 10:15 - 11:45<br />Lunch: 11:45 - 12:30<br />Period 3: 12:30 - 2:05<br />Period 4: 2:20 - 3:50'
		],
		15 => [
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:25<br />Period 6: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 7: 1:15 - 2:30<br />Period 8A: 2:40 - 3:10<br />Period 8B: 3:20 - 3:50'
		],
		16 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:10<br />Period 2: 10:20 - 12:00<br />Lunch: 12:00 - 12:45<br />Period 3: 12:45 - 2:15<br />Period 4: 2:25 - 3:50'
		],
		17 => [
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:05<br />Period 6: 10:15 - 11:45<br />Lunch: 11:45 - 12:30<br />Period 7: 12:30 - 2:05<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		],
		20 => [
			'description' => 'Modified Anchor Day',
			'schedule' => 'Period 1: 8:30 - 9:20<br />Period 2: 9:30 - 10:20<br />Period 3: 10:30 - 11:20<br />Period 4: 11:30 - 12:20<br />
				Lunch: 12:20 - 1:00<br />Period 5: 1:00 - 1:50<br />Period 6: 2:00 - 2:50<br />Period 7: 3:00 - 3:50'
		],
		21 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'SOL: 8:30 - 10:40<br />Period 1: 10:50 - 11:50<br />Lunch: 11:50 - 12:30<br />Period 2: 12:30 - 1:30<br />Period 3: 1:40 - 2:40<br />Period 4: 2:50 - 3:50'
		],
		22 => [
			'description' => 'Modified Red Day',
			'schedule' => 'SOL: 8:30 - 10:40<br />Period 5: 10:50 - 12:10<br />Lunch: 12:10 - 1:00<br />Period 6: 1:00 - 2:20<br />Period 7: 2:20 - 3:50'
		],
		23 => [
			'description' => 'Modified Blue Day',
			'schedule' => 'SOL: 8:30 - 10:40<br />Period 1: 10:50 - 11:50<br />Lunch: 11:50 - 12:30<br />Period 2: 12:30 - 1:30<br />Period 3: 1:40 - 2:40<br />Period 4: 2:50 - 3:50'
		],
		24 => [
			'description' => 'Modified Red Day',
			'schedule' => 'SOL: 8:30 - 10:40<br />Period 5: 10:50 - 12:10<br />Lunch: 12:10 - 1:00<br />Period 6: 1:00 - 2:20<br />Period 7: 2:20 - 3:50'
		]
	];

	/**
	* Template arguments
	*/
	private $template_args = [];

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return "Bell Schedule";
	}

	/**
	* Unused; Not supported for this module.
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
		global $I2_API, $I2_QUERY;
		$c = self::get_schedule();
		$I2_API->startElement('schedule');
		foreach($c as $n=>$v) {
			/* use cdata for schedule */
			if($n == 'schedule') {
				$I2_API->startElement($n);
				$I2_API->writeCData(htmlspecialchars_decode($v));
				$I2_API->endElement();
			} else {
				$I2_API->writeElement($n, htmlspecialchars_decode($v));
			}
		}
		$I2_API->startElement('parsedschedule');
		/* This is a complete hack */
		try {
			$s = explode("<br />", $c['schedule']);
			foreach($s as $v) {
				$t = explode(":", $v, 2);
				$I2_API->startElement('block');

				$I2_API->writeAttribute('pd', trim(str_replace("Period", "", $t[0])));
				$I2_API->writeElement("period", trim($t[0]));

				$I2_API->startElement('time');
				$u = explode("-", trim($t[1]));
				$I2_API->writeAttribute('start', trim($u[0]));
				$I2_API->writeAttribute('end', trim($u[1]));

				/* this is broken */
				$ts = new DateTime(trim($u[0]));
				$te = new DateTime(trim($u[1]));
				$td = $ts->diff($te);
				$tl = (((int)$td->format('%h')));
				if($tl>9) $tl = 1;
				$tl.=":".((int)$td->format('%i'));
				$I2_API->writeAttribute('length', $tl);

				$I2_API->text(trim($t[1]));
				$I2_API->endElement();

				$I2_API->endElement();
			}
		} catch(Exception $e) {
			$I2_API->text('An error occurred.');
		}
		$I2_API->endElement();


		$I2_API->startElement('args');
		foreach($I2_QUERY as $n=>$v) {
			$I2_API->writeElement($n, $v);
		}
		$I2_API->endElement();

		$I2_API->endElement();
		return false;
	}

	
	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return FALSE;
	}
	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		return "Bell Schedule";
	}

	/**
	* Required by the {@link Module} interface.
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		global $I2_QUERY;
		// Week view
		// FIXME: week is badly broken
		if(isset($I2_QUERY['week'])) {
			return self::display_week($disp);
		}
		$template_args = self::gen_day_view();
		$disp->disp('schedule.tpl', $template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return "Bell Schedule";
	}

	/**
	* Required by the {@link Module} interface.
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
		$intrabox_args = self::gen_day_view();
		//FIXME: week dosen't work in the intrabox.
		$intrabox_args['is_intrabox'] = TRUE;
		$intrabox_args['box'] = "_box";
		$disp->disp('schedule.tpl', $intrabox_args);
	}


	// Public helper methods

	/**
	* Generate a day view
	* @param array $args The args for this day.
	*/
	public static function gen_day_view() {
		global $I2_QUERY;
		$args = [];
		$date = self::parse_day_query();
		$args['date'] = date('l, F j', $date);
		$args['is_intrabox'] = FALSE;
		$args['box'] = "";
		// is it after 5pm?
		$args['tomorrow'] = $tomorrow = date('G') > 16 ? TRUE : FALSE;

		if(isset($I2_QUERY['day'])) {
			$day = $I2_QUERY['day'];
		} else {
			$day = $tomorrow ? 1 : 0;
			$args['has_custom_day'] = false;
		}
		$args['day'] = $day;
		$args['dayname'] = $tomorrow ? "Tomorrow" : "Today";

		if($day == 0) {
			$args['header'] = "Today's Schedule<br />";
			if($tomorrow)
				$args['has_custom_day'] = true;
		}
		else if($day == 1 && $tomorrow)
			$args['header'] = "Tomorrow's Schedule<br />";
		else {
			$args['header'] = "Schedule for<br />";
			$args['has_custom_day'] = true;
		}
		$args['header'] .= $args['date'];
		$schedule = self::get_schedule();
		$args['schedday'] = self::parse_schedule_day($schedule['description']);
		if(strpos($schedule['description'], 'Modified')!==false)
			$schedule['description'] = str_replace("Modified", "<span class='schedule-modified'>Modified</span>", $schedule['description']);
		$args['schedule'] = $schedule;
		return $args;
	}

	/**
	* display a week view
	* @param Display $disp The Display object to use for output.
	*/
	public static function display_week($disp) {
		global $I2_QUERY;
		$c = "<span style='display:none'>::START::</span>";
		$md = isset($I2_QUERY['day']) ? date('Ymd', self::parse_day_query()) : null;
		$ws = isset($I2_QUERY['start']) ? $I2_QUERY['start'] : null;
		$we = isset($I2_QUERY['end']) ? $I2_QUERY['end'] : null;
		$schedules = self::get_schedule_week($ws, $we, $md);

		$c.= "<table class='weeksched'><tr class='h' style='min-height: 40px;max-height: 40px;line-height: 25px'>";
		foreach($schedules as $day=>$schedule) {
			$nday = date('l, F j', strtotime($day));
			$c.= "<td style='font-size: 16px;font-weight: bold'>Schedule for<br />".$nday."</td>";
		}
		$c.= "</tr><tr>";

		foreach($schedules as $day=>$schedule) {
			$nday = date('l, F j', strtotime($day));
			$m = (isset($schedule['modified'])? ' desc-modified': '');
			$c.= "<td class='desc".$m."''>";
			$c.=$schedule['description']."</td>";
		}
		$c.= "</tr><tr>";
		foreach($schedules as $day=>$schedule) {
			$c.= "<td>".$schedule['schedule']."</td>";
		}
		$c.= "</tr></table>";
		$c.="<p><span style='max-width: 500px'>Schedules are subject to change.</span></p>";
		$c.="<span style='display:none'>::END::</span>";
		$disp = new Display('bellschedule');
		$disp->raw_display($c);
		return FALSE;
	}

	/**
	* Get the schedule from the TJ CalendarWiz iCal feed
	*
	* @return array An array containing the schedule description and periods
	*/
	public static function get_schedule() {
		global $I2_QUERY;
		// Get the cache file location
		$cachedir = i2config_get('cache_dir','/var/cache/iodine/','core');
		$cachefile = $cachedir . 'bellschedule.cache';

		// Don't let the cache get older than an hour, and update if the day the file was updated is not today
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile)) || (time() - filemtime($cachefile) > 600) || date('z', filemtime($cachefile)) != date('z') || isset($I2_QUERY['update_schedule'])) {
			$contents = self::update_schedule();
			file_put_contents($cachefile, serialize($contents));
		// do not update cache
		} else if(isset($I2_QUERY['start_date'])) {
			$contents = self::update_schedule($I2_QUERY['start_date']);
		} else if(isset($I2_QUERY['day'])) {
			$date = date('Ymd', self::parse_day_query());
			$rawical = self::get_saved_schedule($cachedir . 'bellschedule-ical.cache');
			$contents = self::parse_schedule($rawical, $date);
		} else {
			$contents = unserialize($contents);
		}
		return $contents;
	}

	/**
	* Get the date from the query string
	*
	* @param bool $rawoffset Do we want a raw offset?
	* @return int The date.
	*/
	public static function parse_day_query($rawoffset=false) {
		global $I2_QUERY;
		// is it after 5 pm?
		$tomorrow = date('G') > 16 ? TRUE : FALSE;
		if(isset($I2_QUERY['day']))
			$offset = $I2_QUERY['day'];
		else
			$offset = $tomorrow ? 1 : 0;
		if($rawoffset)
			return $offset;
		return strtotime($offset.' day');
	}

	/**
	* Converts a text description to the correct array index.
	*
	* @param string $desc The schedule description
	* @return string The schedule array index
	*/
	public static function parse_schedule_day($desc) {
		if(strpos($desc, "Blue"))
			return 'blue';
		else if(strpos($desc, "Red"))
			return 'red';
		else if(strpos($desc, "Anchor"))
			return 'anchor';
		else if(strpos($desc, "No school"))
			return 'noschool';
		else
			return 'other';
	}

	/**
	* Get a week view
	*
	* @param string $start (Optional) The start of the week
	* @param string $end (Optional) The end of the week
	* @param string $mid (Optional) The middle of the week
	* @return array An array containing schedule description and periods for each day
	*/
	public static function get_schedule_week($start=null, $end=null, $mid=null) {
		$cachedir = i2config_get('cache_dir','/var/cache/iodine/','core');
		$cachefile = $cachedir.'bellschedule-ical.cache';
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile)) || (time() - filemtime($cachefile) > 600) || date('z', filemtime($cachefile)) != date('z') || isset($I2_QUERY['update_schedule'])) {
			$contents = self::update_schedule();
			file_put_contents($cachefile, serialize($contents));
		} else {
			$contents = self::get_saved_schedule($cachedir . 'bellschedule-ical.cache');
		}

		if(!isset($mid))
		       	$mid = ((int)date('Ymd'));
		if(!isset($start))
		       	$start = $mid - 2;
		if(!isset($end))
		       	$end = $mid + 2;
		$contentsr = [];
		for($i=$start; $i<($end); $i++) {
			$contentsr[$i] = self::parse_schedule($contents, $i);
			$contentsr[$i]['day'] = $i;
		}
		return $contentsr;
	}

	// Private helper methods

	/**
	* Get the cached ical file.
	*
	* @param string $cachefile The path to the cachefile.
	* @return string The raw ical file.
	*/
	private static function get_saved_schedule($cachefile) {
		d('Getting saved calendar contents');
		if(!file_exists($cachefile)) {
			$fc = file_put_contents($cachefile, self::get_calendar_contents());
			return $fc;
		}
		$fc = file_get_contents($cachefile);
		return $fc;
	}

	/**
	* Get the raw ical file.
	*
	* @return string The raw ical file.
	*/
	private static function get_calendar_contents() {
		$cachedir = i2config_get('cache_dir','/var/cache/iodine/','core');
		$cachefile = $cachedir . 'bellschedule-ical.cache';
		d('Getting new calendar contents');
		if($rawical = self::curl_file_get_contents(self::$url)) {
			file_put_contents($cachefile, $rawical);
			return $rawical;
		} else {
			return FALSE;
		}
	}

	/**
	* Get the calendar for the specified day.
	*
	* @param string The date (defaults to today).
	* @return string The calendar for the specified date.
	*/
	private static function update_schedule($day=null) {
		if($rawical = self::get_calendar_contents()) // Returns false if can't get anything
			return self::parse_schedule($rawical, $day);
		else
			return ['description' => 'Error: Could not load schedule', 'schedule' => ''];
	}

	/**
	* Parse the raw ical to get the calendar for the specified day.
	*
	* @param string The raw ical file.
	* @param string The date (defaults to today).
	* @return string The calendar for the specified date.
	*/
	private static function parse_schedule($str, $day=null) {
		global $I2_QUERY;
		$dateoffset = self::parse_day_query(TRUE);
		$day = isset($day) ? $day : date('Ymd',strtotime($dateoffset.' days'));
		$start = 'DTSTART;VALUE=DATE:'. $day;
		$end = 'END:VEVENT';
		$dwk = date('N', strtotime($day));
		//Find events on the current day that indicate a schedule type
		$regex = '/'.$start.'((?:(?!END:VEVENT).)*?)CATEGORIES:(Anchor Day|Blue Day|Red Day|JLC Blue Day|Special Schedule)(.*?)'.$end.'/s';
		// Is any type of schedule set?
		if(preg_match($regex, $str, $dayTypeMatches) > 0) {
			// Does it have a day type described?
			if(preg_match('/SUMMARY:.(Blue Day - Adjusted Schedule for Mid Term Exams|Red Day - Adjusted Schedule for Mid Term Exams|JLC Blue Day - Adjusted Schedule for Mid Term Exams|AMC Blue Day|Anchor Day|Blue Day|Red Day|JLC Blue Day|Holiday|Student Holiday|Telelearn Day|Telelearn Anchor Day|Winter Break|Spring Break|Modified Blue Day|Modified Red Day|Modified Anchor Day)/', $dayTypeMatches[0], $descriptionMatches) > 0) {
				d("DM: ".$descriptionMatches[1]);
				if($descriptionMatches[1]=='Student Holiday'||$descriptionMatches[1]=='Holiday'||$descriptionMatches[1]=='Winter Break'||$descriptionMatches[1]=='Spring Break'){
					return self::get_default_schedule('noschool');
				} else if($descriptionMatches[1]=='Blue Day - Adjusted Schedule for Mid Term Exams'){
					return self::get_default_schedule('bluemidterm');
				} else if($descriptionMatches[1]=='Red Day - Adjusted Schedule for Mid Term Exams'){
					if(date('w',strtotime($day))=='5')
						return self::get_default_schedule('red2midterm');
					else
						return self::get_default_schedule('red1midterm');
				} else if($descriptionMatches[1]=='AMC Blue Day'){
						return self::get_default_schedule('amcblueday');
				} else if($descriptionMatches[1]=='JLC Blue Day - Adjusted Schedule for Mid Term Exams'){
						return self::get_default_schedule('jlcmidterm');
				/*
				* 2013 AP EXAMS 
				* FIXME: hard coded
				*/
				} else if(($descriptionMatches[1] == 'Modified Blue Day' || $descriptionMatches[1] == 'Modified Red Day' || $descriptionMatches[1] == 'Modified Anchor Day')
					&& date('Y M', strtotime($day)) == '2013 May' && isset(self::$apExamSchedule[date('j',strtotime($day))]) && date('j',strtotime($dateoffset.' days'))>=6) {
					if(isset($I2_QUERY['start_date']))
					       	$d = substr($I2_QUERY['start_date'], 6);
					else
					       	$d = date('j', strtotime($day));
					if(substr($d, 0, 1) == '0')
					       	$d = substr($d, 1);
					d('Modified AP Day: '.$d.' exists: '.isset(self::$apExamSchedule[$d]));
					if(isset(self::$apExamSchedule[$d])) {
						return array('description' => self::$apExamSchedule[$d]['description'], 'schedule' => self::$apExamSchedule[$d]['schedule'], 'modified' => true);
					} else {
						d('Using default schedule--may not be correct!');
						return self::get_default_schedule(null, $dwk);
					}
				} else {
					d('no descriptionMatches');
					return self::get_default_schedule(null, $dwk);
				}
			} else { // If no day type is set, use the default schedule for that day
				d('No day type set');
				return self::get_default_schedule(null, $dwk);
			}
		} else { // If no schedule data, use the default schedule for that type of day
				d('No schedule data: '.$dwk);
				return self::get_default_schedule(null, $dwk);
		}
	}
	
	/**
	* Downloads a file.
	*
	* @param string $url The file to download.
	* @return string The contents of the file or FALSE in case of failure.
	*/
	private static function curl_file_get_contents($url) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $url);
		$contents = curl_exec($c);
		curl_close($c);
		return isset($contents) ? $contents : FALSE;
	}

	/**
	* Returns the default schedule for a given day
	*
	* @param string $type (Optional) The type of schedule whose default should be fetched
	* @param string $day (Optional) The day to get the schedule for
	* @return array An array containing the schedule description and periods
	*/
	private static function get_default_schedule($type=null, $day=null) {
		if(isset($type) && array_key_exists($type, self::$normalSchedules)) {
			return self::$normalSchedules[$type];
		} else {
			$day = isset($day) ? $day : date('N',self::parse_day_query());
			d('Default: ' . $day);
			switch($day) {
			case 1:
				return self::$normalSchedules['anchor'];
			case 2:
				return self::$normalSchedules['blue'];
			case 3:
				return self::$normalSchedules['red'];
			case 4:
				return self::$normalSchedules['jlcblue'];
			case 5:
				return self::$normalSchedules['red'];
			default:
				return ['description' => 'No school', 'schedule' => ''];
			}
		}
	}

}
?>
