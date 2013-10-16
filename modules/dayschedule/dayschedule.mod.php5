<?php
/**
* The DaySchedule module, which shows a daily and week schedule,
* as well as the current class period you're in during a school day.
* @copyright 2013 The Intranet Development Team
* @package modules
* @subpackage DaySchedule
*/

class DaySchedule extends Module {
	
	/**
	* The TJ CalendarWiz iCal URL, which is used to find out
	* what type of day it is.
	**/

	private static $iCalURL = 'https://www.calendarwiz.com/CalendarWiz_iCal.php?crd=tjhsstcalendar';

	/**
	* Values of the SUMMARY iCal field, which are then mapped
	* to IDs for the type of day (as defined in $default_day_schedules)
	* Custom summaries should be stored in MySQL!
	**/
	private static $summaries = array(
		"Anchor Day" => "anchor",
		"Blue Day" => "blue",
		"Red Day" => "red",
		"JLC Blue" => "jlc",
		"Tele-Learn Day (Anchor)" => "telelearn",
		"School Closed" => "schoolclosed",
		"No School" => "noschool"
	);

	/**
	* The default schedule information (name, periods) for default days
	* Custom schedules should be stored in MySQL!
	**/
	private static $schedules = array(
		"blue" => array(
			array("Period 1", "8:30", "10:05"),
			array("Period 2", "10:15", "11:45"),
			array("Lunch", "11:45", "12:30"),
			array("Period 3", "12:30", "2:05"),
			array("Break", "2:05", "2:20"),
			array("Period 4", "2:20", "3:50")
		),
		"red" => array(
			array("Period 5", "8:30", "10:05"),
			array("Period 6", "10:15", "11:45"),
			array("Lunch", "11:45", "12:30"),
			array("Period 7", "12:30", "2:05"),
			array("Break", "2:05", "2:20"),
			array("Period 8A", "2:20", "3:00"),
			array("Period 8B", "3:10", "3:50")
		),
		"anchor" => array(
			array("Period 1", "8:30", "9:15"),
			array("Period 2", "9:25", "10:05"),
			array("Period 3", "10:15", "10:55"),
			array("Period 4", "11:05", "11:45"),
			array("Lunch", "11:45", "12:35"),
			array("Period 5", "12:35", "1:15"),
			array("Period 6", "1:25", "2:05"),
			array("Period 7", "2:15", "2:55"),
			array("Break", "2:55", "3:10"),
			array("Period 8B", "3:10", "3:50")
		),
		"noschool" => array(),
		"schoolclosed" => array()
	);
	/**
	* A to-be-filled array containing the types of days for each day
	**/
	private static $dayTypes = array();

	/**
	* Contains the fetched ics file
	**/
	private static $icsStr = "";

	/**
	* A to-be-filled array containing the parsed ics file
	**/
	private static $icsArr = array();

	/**
	* The template arguments
	**/
	private static $args = array();
	/**
	* The displayed name of the module (required)
	**/
	function get_name() {
		return "Day Schedule";
	}

	/**
	* Initialization for the pane goes here
	**/
	function init_pane() {
		global $I2_QUERY;
		/* set the day that we are querying */
		if(isset($I2_QUERY['date'])) {
			self::$args['date'] = $I2_QUERY['date'];
		} else {
			self::$args['date'] = date('Ymd');
		}
		self::check_show_next_day();
		/* load the custom sql entries */
		self::fetch_custom_entries();

		/* get the parsed iCal */
		$ical = self::convert_to_array(self::fetch_ical());
		/* find the day types */
		self::find_day_types($ical);


		return "Day Schedule";
	}
	/**
	* Displaying of the pane goes here
	**/
	function display_pane($disp) {
		self::gen_day_args();
		d_r(self::$args,0);
		$disp->disp('pane.tpl', self::$args);
	}

	/**
	* Used for accessing data over the Iodine "API"
	**/
	function api() {
		/* TODO */
	}

	/**
	* Used for accessing data on the client side through AJAX
	**/
	function ajax() {
		global $I2_AJAX, $I2_FS_ROOT, $I2_ARGS, $I2_QUERY;
		$disp = new Display('dayschedule');
		
		/**
		* Do initialization which is needed for the following code
		* to work (specifically fetching the custom SQL and iCal)
		**/

		self::init_pane();

		if(isset($I2_ARGS[2]) && $I2_ARGS[2] == 'week') {
			$week = array();
			$d = isset($I2_QUERY['days']) ? $I2_QUERY['days'] : 5;
			self::gen_day_args();
			for($i=0; $i<$d; $i++) {
				$week[] = $disp->fetch($I2_FS_ROOT . 'templates/dayschedule/pane.tpl', self::$args, false);
				/* make date be the next day */
				self::increment_date('+1 day');
			}
			echo json_encode($week);
		} else {
			self::gen_day_args();
			echo $disp->fetch($I2_FS_ROOT . 'templates/dayschedule/pane.tpl', self::$args, false);
		}

	}

	/**
	* Get the pretty name of a summary
	* (e.x. get "Anchor Day" from "anchor")
	* @attr String $daytype the ugly name
	* @return String the pretty name
	**/
	private static function get_display_summary($daytype) {
		return array_search($daytype, self::$summaries);
	}

	/**
	* Check if tomorrow's schedule should be shown
	* Currently, this is after 4PM
	**/
	private static function check_show_next_day() {
		$hr = (int)date('G');
		if($hr >= 16) {
			self::increment_date('+1 day');
		}
	}

	/**
	* Increment the args['date'] variable by a strtotime expression
	* e.x. increment_date('+1 day')
	* @attr String $inc the strtotime expression
	**/
	private static function increment_date($inc) {
		self::$args['date'] = date('Ymd', strtotime($inc, strtotime(self::$args['date'])));
	}

	/**
	* Calculate the schedule for today and return the array
	* which is passed to $disp
	* @return Array the template arguments
	**/
	private static function gen_day_args() {
		$day = self::$args['date'];
		$daytype = self::find_day_type($day);
		self::$args['dayname'] = date('l, F j', strtotime($day));
		self::$args['summaryid'] = $daytype;
		self::$args['summary'] = self::get_display_summary($daytype);
		self::$args['schedule'] = isset(self::$schedules[$daytype]) ? self::$schedules[$daytype] : array('error' => 'No schedule information available.');
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
	* Fetch the iCal file from CalendarWiz
	* @return String the iCal files's contents
	**/
	private static function fetch_ical() {
		self::$icsStr = self::curl_file_get_contents(self::$iCalURL);
		return self::$icsStr;
	}

	/**
	* Convert the iCal file to a PHP-readable array, saves to self::$icsArr
	* @attr String $icsFile the contents of an iCal file
	* @return Array with the iCal files' contents
	**/
	private static function convert_to_array($icsFile) {
	    $icsData = explode("BEGIN:", $icsFile);
	    foreach($icsData as $key => $value) {
	        $icsDatesMeta[$key] = explode("\n", $value);
	    }
	    foreach($icsDatesMeta as $key => $value) {
	        foreach($value as $subKey => $subValue) {
	        	if ($subValue != "") {
		            if ($key != 0 && $subKey == 0) {
	                    // $icsDates[$key]["BEGIN"] = $subValue;
	                } else {
	                    $subValueArr = explode(":", $subValue, 2);
	                    if(in_array($subValueArr[0], array("DTSTART;VALUE=DATE", "DTEND;VALUE=DATE", "SUMMARY", "CATEGORIES"))) {
		                    $icsDates[$key][$subValueArr[0]] = $subValueArr[1];
		                }
		            }
	            }
	        }
	    }
	    self::$icsArr = $icsDates;
	    return $icsDates;
	}


	/**
	* Convert the iCal array (from convert_to_array) to an array
	* assigning a day to it's day type. Stores this result in self::$dayTypes
	* @attr Array $arr from convert_to_array
	**/
	private static function find_day_types($arr) {
		$ret = array();
		foreach($arr as $item) {
			if(in_array(trim($item['SUMMARY']), array_keys(self::$summaries))) {
				$day = $item['DTSTART;VALUE=DATE'];
				$ret[trim($day)] = self::$summaries[trim($item['SUMMARY'])];
			}
		}
		d_r($ret, 0);
		self::$dayTypes = $ret;
		return $ret;
	}

	/**
	* Return the type of day for one day
	* @attr String the date in format YYYYMMDD
	**/
	private static function find_day_type($day) {
		return isset(self::$dayTypes[$day]) ? self::$dayTypes[$day] : "noschool";
	}

	/**
	* Add custom summary information to the database
	* @attr String $daytype Day type
	* @attr String $daydesc the SUMMARY field from iCal
	**/
	private static function add_summary($daytype, $daydesc) {
		// INSERT INTO `iodine-dev`.`dayschedule_custom_summaries` (`daytype`, `daydesc`) VALUES ('psat-half', 'PSAT (Early Dismissal for Students)');
	}

	/**
	* Add custom schedule information to the database
	* @attr String $daytype Day type
	* @attr String $json JSON containing the schedule
	* information, for example:
	* [["Period 1", "8:30", "10:05"], ["Period 2", "10:15", "11:45"]]
	**/
	private static function add_schedule($daytype, $json) {
		// INSERT INTO `iodine-dev`.`dayschedule_custom_summaries` (`daytype`, `daydesc`) VALUES ('psat-half', 'PSAT (Early Dismissal for Students)');
	}

	/**
	* Fetch the custom schedules and summaries stored in SQL
	* and append them to the current schedules and summaries
	**/
	private static function fetch_custom_entries() {
		global $I2_SQL;
/*
CREATE TABLE IF NOT EXISTS `dayschedule_custom_summaries` (
  `daytype` varchar(32) NOT NULL,
  `daydesc` varchar(128) NOT NULL
)
CREATE TABLE IF NOT EXISTS `dayschedule_custom_schedules` (
  `daytype` varchar(32) NOT NULL,
  `json` varchar(2048) NOT NULL
)
*/
		
		$custom_summaries = $I2_SQL->query('SELECT * FROM dayschedule_custom_summaries')->fetch_all_arrays(MYSQLI_ASSOC);
		foreach($custom_summaries as $s) {
			self::$summaries[$s['daydesc']] = $s['daytype'];
		}
		d_r(self::$summaries, 0);

		
		$custom_schedules = $I2_SQL->query('SELECT * FROM dayschedule_custom_schedules')->fetch_all_arrays(MYSQLI_ASSOC);
		foreach($custom_schedules as $s) {
			self::$schedules[$s['daytype']] = json_decode($s['json']);
		}
		d_r(self::$schedules, 0);
		
		
	}
}

?>
