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
			'schedule' => 'Period 1: 8:30-9:05<br />Period 2: 9:10-9:45<br />Period 3: 9:50-10:25<br />Period 4: 10:30-11:05<br />Lunch: 11:05-11:55<br />Period 5: 11:55-12:30<br />Period 6: 12:35-1:10<br />Period 7: 1:15-1:50'
		),
		'telelearnanchor' => array(
		    'description' => 'Telelearn Day',
			'schedule' => 'Period 1: 8:30-9:05<br />Period 2: 9:10-9:45<br />Period 3: 9:50-10:25<br />Period 4: 10:30-11:05<br />Lunch: 11:05-11:55<br />Period 5: 11:55-12:30<br />Period 6: 12:35-1:10<br />Period 7: 1:15-1:50'
		),
		'bluemidterm' => array(
			'description' => 'Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 8:30-10:30<br />Period 2: 10:40-11:45<br />Period 3: 12:30-2:30<br />Period 4: 2:40-3:50'
		),
		'red1midterm' => array(
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 5: 8:30-10:30<br />Period 6: 10:40-11:45<br />Period 7: 12:30-2:30<br />Period 8A: 2:40-3:10<br />Period 8B: 3:20-3:50'
		),
		'jlcmidterm' => array(
			'description' => 'JLC Blue Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 1: 9:00-9:55<br />Period 2: 10:05-12:05<br />Period 3: 12:45-1:40<br />Period 4: 1:50-3:50'
		),
		'red2midterm' => array(
			'description' => 'Red Day - Adjusted Midterm Schedule',
			'schedule' => 'Period 6: 8:30-10:30<br />Period 5: 10:40-11:45<br />Period 7: 12:30-1:35<br />Period 8A: 1:50-2:45<br />Period 8B: 2:55-3:50'
		)
	);
	private static $apExamSchedule = array(
		6 => array(
			'description' => 'Adjusted Blue Day',
			'schedule' => 'Period 1: 8:30-10:20<br />Period 2: 10:30-12:15<br />Period 3: 1:00-2:20<br />Period 4: 2:30-3:50'
		),
		7 => array(
			'description' => 'Adjusted Red Day',
			'schedule' => 'Period 7: 8:30-10:20<br />Period 6: 10:30-12:15<br />Period 5: 1:00-2:20<br />Period 8A: 2:30-3:05<br />Period 8B: 3:15-3:50'
		),
		8 => array(
			'description' => 'Adjusted Blue Day',
			'schedule' => 'Period 4: 8:30-10:25<br />Period 3: 10:35-12:30<br />Period 2: 1:15-2:30<br />Period 4: 2:40-3:50'
		),
		9 => array(
			'description' => 'Adjusted Red Day',
			'schedule' => 'Period 5: 8:30-10:10<br />Period 7: 10:20-12:00<br />Period 6: 12:45-2:05<br />Period 8A: 2:20-3:00<br />Period 8B: 3:10-3:50'
		),
		10 => array(
			'description' => 'Adjusted Blue Day',
			'schedule' => 'Period 1: 8:30-10:25<br />Period 2: 10:35-12:30<br />Period 3: 1:15-2:20<br />Period 4: 2:30-3:50'
		),
		13 => array(
			'description' => 'Adjusted Red Day',
			'schedule' => 'Period 5: 8:30-10:10<br />Period 6: 10:20-12:00<br />Period 7: 12:45-2:15<br />Period 8A: 2:30-3:05<br />Period 8B: 3:15-3:50'
		),
		14 => array(
			'description' => 'Adjusted Blue Day',
			'schedule' => 'Period 1: 8:30-10:05<br />Period 2: 10:15-11:45<br />Period 3: 12:30-2:05<br />Period 4: 2:20-3:50'
		),
		15 => array(
			'description' => 'Adjusted Red Day',
			'schedule' => 'Period 5: 8:30-10:25<br />Period 6: 10:35-12:30<br />Period 7: 1:15-2:30<br />Period 8A: 2:40-3:10<br />Period 8B: 3:20-3:50'
		),
		16 => array(
			'description' => 'Adjusted Blue Day',
			'schedule' => 'Period 1: 8:30-10:10<br />Period 2: 10:20-12:00<br />Period 3: 12:45-2:15<br />Period 4: 2:25-3:50'
		),
		17 => array(
			'description' => 'Adjusted Red Day',
			'schedule' => 'Period 5: 8:30-10:05<br />Period 6: 10:15-11:45<br />Period 7: 12:30-2:05<br />Period 8A: 2:20-3:00<br />Period 8B: 3:10-3:50'
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
		return false;
	}

	/**
	* Required by the {@link Module} interface.
	*/
	public function display_pane($disp) {
		return false;
	}

	public function init_box() {
		return false;
	}

	public function display_box($disp) {
		return false;
	}
	
	/**
	* Get the schedule from the TJ CalendarWiz iCal feed
	*
	* @return array An array containing the schedule description and periods
	*/
	public static function get_schedule() {
		global $I2_QUERY;
		// Get the cache file location
		$cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'bellschedule.cache';
		
		// Don't let the cache get older than an hour, and update if the day the file was updated is not today
		if(!file_exists($cachefile) || !($contents = file_get_contents($cachefile)) || (time() - filemtime($cachefile) > 600) || date('z', filemtime($cachefile)) != date('z') || isset($I2_QUERY['update_schedule'])) {
			$contents = BellSchedule::update_schedule();
			BellSchedule::store_schedule($cachefile, serialize($contents));
		// do not update cache
		} else if(isset($I2_QUERY['start_date'])) {
			$contents = BellSchedule::update_schedule();
		} else {
			$contents = unserialize($contents);
		}
		return $contents;
	}
	private static function store_schedule($cachefile,$string) {
		$fh = fopen($cachefile,'w');
		fwrite($fh, $string);
		fclose($fh);
	}
	private static function update_schedule() {
		global $I2_QUERY;
		// TJ CalendarWiz iCal URL
		// HTTPS because otheriwse it gets cached by the proxy
		$url = 'https://www.calendarwiz.com/CalendarWiz_iCal.php?crd=tjhsstcalendar';
		if($str = BellSchedule::curl_file_get_contents($url)) { // Returns false if can't get anything
			if(isset($I2_QUERY['start_date'])) $startd = $I2_QUERY['start_date'];
			else $startd = date('Ymd');
			$starter = 'DTSTART;VALUE=DATE:'. $startd;
			$ender = 'END:VEVENT';

			//Find events on the current day that indicate a schedule type
			$regex = '/'.$starter.'((?:(?!END:VEVENT).)*?)CATEGORIES:(Anchor Day|Blue Day|Red Day|JLC Blue Day|Special Schedule)(.*?)'.$ender.'/s';
			// Is any type of schedule set?
			if(preg_match($regex, $str, $dayTypeMatches) > 0) {
				// Does it have a day type described?
				if(preg_match('/SUMMARY:.(Blue Day - Adjusted Schedule for Mid Term Exams|Red Day - Adjusted Schedule for Mid Term Exams|JLC Blue Day - Adjusted Schedule for Mid Term Exams|AMC Blue Day|Anchor Day|Blue Day|Red Day|JLC Blue Day|Holiday|Student Holiday|Telelearn Day|Telelearn Anchor Day|Winter Break|Spring Break|Modified Blue Day|Modified Red Day)/', $dayTypeMatches[0], $descriptionMatches) > 0||1!=1) {
					
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
					}else if($descriptionMatches[1] == 'Modified Blue Day' || $descriptionMatches[1] == 'Modified Red Day' && date('Y M') == '2013 May' && ((int)date('j'))>6) {
						$d = (isset($I2_QUERY['start_date'])?substr($I2_QUERY['start_date'],6):date('j'));
						if(((int)$d) < 10) $d = substr($d, 1);
						d('Day: '.$d);
						d(BellSchedule::$apExamSchedule[$d]);
						if(isset(BellSchedule::$apExamSchedule[$d])) {
							return array('description' => BellSchedule::$apExamSchedule[$d]['description'], 'schedule' => BellSchedule::$apExamSchedule[$d]['schedule']);
						} else {
							return BellSchedule::get_default_schedule();
						}
					}else{
						return array('description' => $descriptionMatches[1], 'schedule' => BellSchedule::$normalSchedules[strtolower(str_replace(array(' Day',' '),'',$descriptionMatches[1]))]['schedule']);
					}
				} else { // If no day type is set, use the default schedule for that day
					return BellSchedule::get_default_schedule();
				}
			} else { // If no schedule data, use the default schedule for that type of day
					return BellSchedule::get_default_schedule();
			}
				
		} else {
			return array('description' => 'Error: Could not load schedule', 'schedule' => '');
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
	
	/**
	* Returns the default schedule for a given day
	*
	* @param string $type (Optional) The type of schedule whose default should be fetched
	* @return array An array containing the schedule description and periods
	*/
	private static function get_default_schedule($type=null) {
		if(isset($type) && array_key_exists($type, BellSchedule::$normalSchedules)) {
			return BellSchedule::$normalSchedules[$type];
		} else {
			$day = date('N');
			
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
}
?>
