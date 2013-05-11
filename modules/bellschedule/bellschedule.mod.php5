<?php
/**
* Just contains the definition for the class {@link News}.
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
	* The default schedules
	*/
	private static $url = 'https://www.calendarwiz.com/CalendarWiz_iCal.php?crd=tjhsstcalendar';
	private static $normalSchedules = array(
		'anchor' => array(
			'description' => 'Anchor Day',
			'schedule' => 'Period 1: 8:30 - 9:15<br />Period 2: 9:25 - 10:05<br />Period 3: 10:15 - 10:55<br />Period 4: 11:05 - 11:45<br />Lunch: 11:45 - 12:35<br />Period 5: 12:35 - 1:15<br />Period 6: 1:25 - 2:05<br />Period 7: 2:15 - 2:55<br />Break: 2:55 - 3:10<br />Period 8: 3:10 - 3:50'
		),
		'blue' => array(
			'description' => 'Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:05<br />Period 2: 10:15 - 11:45<br />Lunch: 11:45 - 12:30<br />Period 3: 12:30 - 2:05<br />Break: 2:05 - 2:20<br />Period 4: 2:20 - 3:50'
		),
		'red' => array(
			'description' => 'Red Day',
			'schedule' => 'Period 5: 8:30 - 10:05<br />Period 6: 10:15 - 11:45<br />Lunch: 11:45 - 12:30<br />Period 7: 12:30 - 2:05<br />Break: 2:05 - 2:20<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		),
		'jlcblue' => array(
			'description' => 'JLC Blue Day',
			'schedule' => 'JLC: 8:00 - 8:55<br />Period 1: 9:00 - 10:28<br />Period 2: 10:37 - 12:05<br />Lunch: 12:05 - 12:45<br />Period 3: 12:45 - 2:13<br />Break: 2:13 - 2:22<br />Period 4: 2:22 - 3:50'
		),
		'telelearn' => array(
			'description' => 'Telelearn Day',
			'schedule' => 'Period 1: 8:30 - 9:05<br />Period 2: 9:10 - 9:45<br />Period 3: 9:50 - 10:25<br />Period 4: 10:30 - 11:05<br />Lunch: 11:05 - 11:55<br />Period 5: 11:55 - 12:30<br />Period 6: 12:35 - 1:10<br />Period 7: 1:15 - 1:50'
		),
		'telelearnanchor' => array(
		    'description' => 'Telelearn Day',
			'schedule' => 'Period 1: 8:30 - 9:05<br />Period 2: 9:10 - 9:45<br />Period 3: 9:50 - 10:25<br />Period 4: 10:30 - 11:05<br />Lunch: 11:05 - 11:55<br />Period 5: 11:55 - 12:30<br />Period 6: 12:35 - 1:10<br />Period 7: 1:15 - 1:50'
		),
		'bluemidterm' => array(
			'description' => 'Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 8:30 - 10:30<br />Period 2: 10:40 - 11:45<br />Period 3: 12:30 - 2:30<br />Period 4: 2:40 - 3:50'
		),
		'red1midterm' => array(
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 5: 8:30 - 10:30<br />Period 6: 10:40 - 11:45<br />Period 7: 12:30 - 2:30<br />Period 8A: 2:40 - 3:10<br />Period 8B: 3:20 - 3:50'
		),
		'jlcmidterm' => array(
			'description' => 'JLC Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 9:00 - 9:55<br />Period 2: 10:05 - 12:05<br />Period 3: 12:45 - 1:40<br />Period 4: 1:50 - 3:50'
		),
		'red2midterm' => array(
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 6: 8:30 - 10:30<br />Period 5: 10:40 - 11:45<br />Period 7: 12:30 - 1:35<br />Period 8A: 1:50 - 2:45<br />Period 8B: 2:55 - 3:50'
		),
		'noschool' => array('description' => 'No school', 'schedule' => '')
	);
	private static $apExamSchedule = array(
		4 => array('description' => 'No school', 'schedule' => ''),
		5 => array('description' => 'No school', 'schedule' => ''),
		6 => array(
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:20<br />Period 2: 10:30 - 12:15<br />Lunch: 12:25 - 1:00<br />Period 3: 1:00 - 2:20<br />Period 4: 2:30 - 3:50'
		),
		7 => array(
			'description' => 'Modified Red Day',
			'schedule' => 'Period 7: 8:30 - 10:20<br />Period 6: 10:30 - 12:15<br />Lunch: 12:15 - 1:00<br />Period 5: 1:00 - 2:20<br />Period 8A: 2:30 - 3:05<br />Period 8B: 3:15 - 3:50'
		),
		8 => array(
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 4: 8:30 - 10:25<br />Period 3: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 2: 1:15 - 2:30<br />Period 1: 2:40 - 3:50'
		),
		9 => array(
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:10<br />Period 7: 10:20 - 12:00<br />Lunch: 12:00 - 12:45<br />Period 6: 12:45 - 2:05<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		),
		10 => array(
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:25<br />Period 2: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 3: 1:15 - 2:20<br />Period 4: 2:30 - 3:50'
		),
		13 => array(
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:10<br />Period 6: 10:20 - 12:00<br />Period 7: 12:45 - 2:15<br />Period 8A: 2:30 - 3:05<br />Period 8B: 3:15 - 3:50'
		),
		14 => array(
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:05<br />Period 2: 10:15 - 11:45<br />Lunch: 11:15 - 12:30<br />Period 3: 12:30 - 2:05<br />Period 4: 2:20 - 3:50'
		),
		15 => array(
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:25<br />Period 6: 10:35 - 12:30<br />Lunch: 12:30 - 1:15<br />Period 7: 1:15 - 2:30<br />Period 8A: 2:40 - 3:10<br />Period 8B: 3:20 - 3:50'
		),
		16 => array(
			'description' => 'Modified Blue Day',
			'schedule' => 'Period 1: 8:30 - 10:10<br />Period 2: 10:20 - 12:00<br />Lunch: 12:00 - 12:45<br />Period 3: 12:45 - 2:15<br />Period 4: 2:25 - 3:50'
		),
		17 => array(
			'description' => 'Modified Red Day',
			'schedule' => 'Period 5: 8:30 - 10:05<br />Period 6: 10:15 - 11:45<br />Lunch: 11:45 - 12:30<br />Period 7: 12:30 - 2:05<br />Period 8A: 2:20 - 3:00<br />Period 8B: 3:10 - 3:50'
		)
	);

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Template arguments for the specified action
	*/
	private $template_args = array();
	
	public function get_name() {
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
	public function init_pane() {
		return "Bell Schedule";
	}

	/**
	* Required by the {@link Module} interface.
	*/
	public function display_pane($disp) {
		global $I2_QUERY;
		$schedule = BellSchedule::get_schedule();
		// Week view
		if(isset($I2_QUERY['week'])) {
			$c = "::START::";
			$md = isset($I2_QUERY['day']) ? date('Ymd', BellSchedule::parse_day_query()) : null;
			$ws = isset($I2_QUERY['start']) ? $I2_QUERY['start'] : null;
			$we = isset($I2_QUERY['end']) ? $I2_QUERY['end'] : null;
			$schedules = BellSchedule::get_schedule_week($ws, $we, $md);

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
			$c.="::END::";
			$disp = new Display('bellschedule');
			$disp->raw_display($c);
			exit();
			return FALSE;
		}
		$schedule['header'] = "Today's Schedule";
		if(isset($I2_QUERY['day'])) {
			$cday = $I2_QUERY['day'];
			if(substr($cday, 0, 1) == '-') $cday = '-'.substr($cday, 1);
			else $cday = '+'.$cday;
			d($cday);
			$schedule['date'] = date('l, F j', strtotime($cday.' day'));
			if($schedule['date'] !== date('l, F j')) {
				$schedule['header'] = "Schedule for<br />".$schedule['date'];
			}
			if(substr($cday, 0, 1) == '+') $dint = substr($cday, 1);
			else $dint = $cday;
			d($dint);
			$schedule['yday'] = ((int)$dint)-1;
			$schedule['nday'] = ((int)$dint)+1;
			$template_args['has_custom_day'] = ($cday !== "+0");
		} else {
			$schedule['yday'] = -1;
			$schedule['nday'] = 1;

			$template_args['has_custom_day'] = false;
		}
		$template_args['schedule'] = $schedule;
		$disp->disp('pane_schedule.tpl', $template_args);
	}

	public function init_box() {
		return "Bell Schedule";
	}

	public function display_box($disp) {
		return $this->display_pane($disp);
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
			$contents = BellSchedule::update_schedule();
			BellSchedule::store_schedule($cachefile, serialize($contents));
		// do not update cache
		} else if(isset($I2_QUERY['start_date'])) {
			$contents = BellSchedule::update_schedule($I2_QUERY['start_date']);
		} else if(isset($I2_QUERY['day'])) {
			$cd = $I2_QUERY['day'];
			$cb = "+";
			if(substr($cd, 0, 1) == '-') $cb = "-";
			$cinc = strtotime($cb.$cd." day");
			$cdate = date('Ymd', $cinc);
			d($cinc.' '.$cdate);
			$str = BellSchedule::get_saved_schedule($cachedir . 'bellschedule-save.cache');
			$contents = BellSchedule::update_schedule_contents($str, $cdate);
		} else {
			$contents = unserialize($contents);
		}
		return $contents;
	}

	public static function parse_day_query($cday=null) {
		global $I2_QUERY;
		if(!isset($cday)) $cday = $I2_QUERY['day'];
		if(substr($cday, 0, 1) == '-') $cday = '-'.substr($cday, 1);
		else $cday = '+'.$cday;
		return strtotime($cday.' day');
	}
	/**
	* Get a week view
	*
	* @return array An array containing schedule description and periods for each day
	*/
	public static function get_schedule_week($start=null, $end=null, $mid=null) {
		$cachedir = i2config_get('cache_dir','/var/cache/iodine/','core');
		$cachefile = $cachedir.'bellschedule-save.cache';
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile)) || (time() - filemtime($cachefile) > 600) || date('z', filemtime($cachefile)) != date('z') || isset($I2_QUERY['update_schedule'])) {
			$contents = BellSchedule::update_schedule();
			BellSchedule::store_schedule($cachefile, serialize($contents));
		} else {
			$contents = BellSchedule::get_saved_schedule($cachedir . 'bellschedule-save.cache');
		}

		if(!isset($mid)) $mid = ((int)date('Ymd'));
		if(!isset($start)) $start = $mid - 2;
		if(!isset($end)) $end = $mid + 2;
		$contentsr = array();
		for($i=$start; $i<($end); $i++) {
			$contentsr[$i] = BellSchedule::update_schedule_contents($contents, $i);
			$contentsr[$i]['day'] = $i;
		}
		return $contentsr;
	}
	private static function store_schedule($cachefile,$string) {
		d('Updating schedule cache');
		$fh = fopen($cachefile,'w');
		fwrite($fh, $string);
		fclose($fh);
	}
	private static function get_saved_schedule($cachefile) {
		d('Getting saved calendar contents');
		if(!file_exists($cachefile)) {
			$fc = BellSchedule::store_schedule($cachefile, BellSchedule::get_calendar_contents());
			return $fc;
		}
		$fc = file_get_contents($cachefile);
		return $fc;
	}
	private static function get_calendar_contents() {
		$cachedir = i2config_get('cache_dir','/var/cache/iodine/','core');
		d('Getting new calendar contents');
		$url = BellSchedule::$url;
		if($str = BellSchedule::curl_file_get_contents($url)) {
			BellSchedule::store_schedule($cachedir . 'bellschedule-save.cache', $str);
			return $str;
		} else {
			return false;
		}
	}
	private static function update_schedule($day=null) {
		global $I2_QUERY;
		// TJ CalendarWiz iCal URL
		// HTTPS because otheriwse it gets cached by the proxy
		$url = BellSchedule::$url;
		if($str = BellSchedule::get_calendar_contents()) { // Returns false if can't get anything
		
			return BellSchedule::update_schedule_contents($str, $day);
		} else {
			return array('description' => 'Error: Could not load schedule', 'schedule' => '');
		}
	}
	private static function update_schedule_contents($str, $day=null) {
		global $I2_QUERY;
		if(isset($day) && $day!==null) $startd = $day;
		else $startd = date('Ymd');
		$starter = 'DTSTART;VALUE=DATE:'. $startd;
		$ender = 'END:VEVENT';
		$dwk = date('N', strtotime($startd));
		//Find events on the current day that indicate a schedule type
		$regex = '/'.$starter.'((?:(?!END:VEVENT).)*?)CATEGORIES:(Anchor Day|Blue Day|Red Day|JLC Blue Day|Special Schedule)(.*?)'.$ender.'/s';
		// Is any type of schedule set?
		if(preg_match($regex, $str, $dayTypeMatches) > 0) {
			d('First regex: '.$dayTypeMatches[0]);
			// Does it have a day type described?
			if(preg_match('/SUMMARY:.(Blue Day - Adjusted Schedule for Mid Term Exams|Red Day - Adjusted Schedule for Mid Term Exams|JLC Blue Day - Adjusted Schedule for Mid Term Exams|AMC Blue Day|Anchor Day|Blue Day|Red Day|JLC Blue Day|Holiday|Student Holiday|Telelearn Day|Telelearn Anchor Day|Winter Break|Spring Break|Modified Blue Day|Modified Red Day)/', $dayTypeMatches[0], $descriptionMatches) > 0||1!=1) {
				d('Second regex: '.$descriptionMatches[1]);
				if($descriptionMatches[1]=='Student Holiday'||$descriptionMatches[1]=='Holiday'||$descriptionMatches[1]=='Winter Break'||$descriptionMatches[1]=='Spring Break'){
					return array('description' => 'No school', 'schedule' => '');
				} else if($descriptionMatches[1]=='Blue Day - Adjusted Schedule for Mid Term Exams'){
					return array('description' => BellSchedule::$normalSchedules['bluemidterm']['description'], 'schedule' => BellSchedule::$normalSchedules['bluemidterm']['schedule']);
				} else if($descriptionMatches[1]=='Red Day - Adjusted Schedule for Mid Term Exams'){
					if(date('w')=='5'){
						return array('description' => BellSchedule::$normalSchedules['red2midterm']['description'], 'schedule' => BellSchedule::$normalSchedules['red2midterm']['schedule']);
					} else {
						return array('description' => BellSchedule::$normalSchedules['red1midterm']['description'], 'schedule' => BellSchedule::$normalSchedules['red1midterm']['schedule']);
					}
				} else if($descriptionMatches[1]=='AMC Blue Day'){
					return array('description' => 'AMC Blue Day', 'schedule' => 'AMC/Study Hall: 8:30 - 10:00<br />Period 1: 10:10 - 11:20<br />Lunch: 11:20 - 12:00<br />Period 2: 12:00 - 1:10<br />Period 3: 1:20 - 2:30<br />Period 4: 2:40 - 3:50');
				
				}else if($descriptionMatches[1]=='JLC Blue Day - Adjusted Schedule for Mid Term Exams'){
					return array('description' => BellSchedule::$normalSchedules['jlcmidterm']['description'], 'schedule' => BellSchedule::$normalSchedules['jlcmidterm']['schedule']);
				/* 2013 AP EXAMS */
				}else if(($descriptionMatches[1] == 'Modified Blue Day' || $descriptionMatches[1] == 'Modified Red Day') && date('Y M') == '2013 May') {
					if(isset($I2_QUERY['start_date'])) $d = substr($I2_QUERY['start_date'], 6);
					else $d = date('j', strtotime($startd));
					if(substr($d, 0, 1) == '0') $d = substr($d, 1);
					d('Modified AP Day: '.$d.' exists: '.isset(BellSchedule::$apExamSchedule[$d]));
					if(isset(BellSchedule::$apExamSchedule[$d])) {
						return array('description' => BellSchedule::$apExamSchedule[$d]['description'], 'schedule' => BellSchedule::$apExamSchedule[$d]['schedule'], 'modified' => true);
					} else {
						d('Using default schedule--may not be correct!');

						return BellSchedule::get_default_schedule(null, $dwk);
					}
				}else{
					d('no descriptionMatches');
					return array('description' => $descriptionMatches[1], 'schedule' => BellSchedule::$normalSchedules[strtolower(str_replace(array(' Day',' '),'',$descriptionMatches[1]))]['schedule']);
				}
			} else { // If no day type is set, use the default schedule for that day
				d('No day type set');
				return BellSchedule::get_default_schedule(null, $dwk);
			}
		} else { // If no schedule data, use the default schedule for that type of day
				d('No schedule data'.$dwk);
				return BellSchedule::get_default_schedule(null, $dwk);
		}
	}
	private static function curl_file_get_contents($url) {
		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $url);
		$contents = curl_exec($c);
		curl_close($c);
		
		if ($contents) return $contents;
		else return FALSE;
	}
	
	public static function parse_schedule_day($desc) {
		if(strpos($desc, "Blue") !==false) {
			return 'blue';
		} else if(strpos($desc, "Red") !== false) {
			return 'red';
		} else if(strpos($desc, "Anchor") !== false) {
			return 'anchor';
		} else if(strpos($desc, "No school") !== false) {
			return 'noschool';
		} else {
			return 'other';
		}
	}
	/**
	* Returns the default schedule for a given day
	*
	* @param string $type (Optional) The type of schedule whose default should be fetched
	* @return array An array containing the schedule description and periods
	*/
	private static function get_default_schedule($type=null, $day=null) {
		global $I2_QUERY;d($day);
		if(isset($type) && array_key_exists($type, BellSchedule::$normalSchedules)) {
			return BellSchedule::$normalSchedules[$type];
		} else {
			if(isset($I2_QUERY['day'])&&$day==null) {
				$day = ((int)date('N', BellSchedule::parse_day_query()));
			}
			if(!isset($day)||$day==null) $day = date('N');
			d('Default: '.$day);
			if($day == 1) {
				return BellSchedule::$normalSchedules['anchor'];
			} else if($day == 2) {
				return BellSchedule::$normalSchedules['blue'];
			} else if($day == 3 || $day == 5) {
				return BellSchedule::$normalSchedules['red'];
			} else if($day == 4) {
				return BellSchedule::$normalSchedules['jlcblue'];
			} else {
				return array('description' => 'No school', 'schedule' => '');
			}
		}
	}

	function is_intrabox() {
		return true;
	}
}
?>
