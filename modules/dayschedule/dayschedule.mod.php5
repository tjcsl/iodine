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
	**/
	private static $default_summaries = array(
		"Anchor Day" => "anchor",
		"Blue Day" => "blue",
		"Red Day" => "red",
		"JLC Blue" => "jlc",
		"Tele-Learn Day (Anchor)" => "telelearn",
		"School Closed" => "noschool"
	);

	/**
	* Custom summaries (for special days), which are stored in MySQL
	**/
	// private static $custom_summaries = self::get_custom_summaries();

	/**
	* Combine the default and custom summaries
	**/
	private static $summaries = array(); // array_merge($default_summaries, $custom_summaries);

	/**
	* The default schedule information (name, periods) for default days
	* Custom schedules should be stored in MySQL!
	**/
	private static $default_schedules = array(
		"blue" => array(
			"name" => "Blue Day",
			"times" => array(
				array("Period 1", "8:30", "10:05"),
				array("Period 2", "10:15", "11:45"),
				array("Lunch", "11:45", "12:30"),
				array("Period 3", "12:30", "2:05"),
				array("Break", "2:05", "2:20"),
				array("Period 4", "2:20", "3:50")
			)
		)
	);

	/**
	* Custom schedules, which are stored in MySQL
	**/
	// private static $custom_schedules = self::get_custom_schedules();

	/**
	* Combine the default and custom schedules
	**/
	private static $schedules = array(); //array_merge($default_schedules, $custom_schedules);

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
	* The displayed name of the module (required)
	**/
	function get_name() {
		return "Bell Schedule";
	}

	/**
	* Initialization for the pane goes here
	**/
	function init_pane() {
		self::find_day_types(self::convert_to_array(self::fetch_ical()));
		return "Bell Schedule";
	}
	/**
	* Displaying of the pane goes here
	**/
	function display_pane($disp) {
		$args = gen_day_args(); /*array(
			"dayname" => "Tuesday, October 15th",
			"daytype" => "Anchor",
			"schedule" => array(
				array("pd"=>"Period 1","start"=>"8:30","end"=>"9:15")
			)
		);*/
		$disp->disp('pane.tpl', $args);
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
		/* TODO */
	}

	/**
	* Calculate the schedule for today and return the array
	* which is passed to $disp
	* @return Array the template arguments
	**/
	function gen_day_args() {
		
	}
	/**
        * Downloads a file.
        *
        * @param string $url The file to download.
        * @return string The contents of the file or FALSE in case of failure.
        */
        function curl_file_get_contents($url) {
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
	function fetch_ical() {
		self::$icsStr = self::curl_file_get_contents(self::$iCalURL);
		d_r(self::$icsStr, 0);
		return self::$icsStr;
	}

	/**
	* Convert the iCal file to a PHP-readable array
	* @attr String $icsFile the contents of an iCal file
	* @return Array with the iCal files' contents
	**/
	function convert_to_array($icsFile) {
	    $icsData = explode("BEGIN:", $icsFile);
	    $icsDates = array();
	    foreach($icsData as $key => $value) {
	        $icsDatesMeta[$key] = explode("\n", $value);
	    }
	    foreach($icsDatesMeta as $key => $value) {
	        foreach($value as $subKey => $subValue) {
	            if($subValue != "" && $key == 0 && $subKey != 0) {
                    $subValueArr = explode(":", $subValue, 2);
                    if(in_array($subValueArr[0], array("DTSTART;VALUE=DATE", "DTEND;VALUE=DATE", "SUMMARY", "CATEGORIES"))) {
	                    $icsDates[$key][$subValueArr[0]] = $subValueArr[1];
	                }
	            }
	        }
	    }
	    d_r($icsDates,0);
	    self::$icsArr = $icsDates;
	    return $icsDates;
	}


	/**
	* Convert the iCal array (from convert_to_array) to an array
	* assigning a day to it's day type. Stores this result in self::$dayTypes
	* @attr Array $arr from convert_to_array
	* @return Array with the day types
	**/
	function find_day_types($arr) {
		global $summaries;
		$ret = array();
		foreach($arr as $item) {
			if(in_array(trim($item['SUMMARY']), array_keys($summaries))) {
				$day = $item['DTSTART;VALUE=DATE'];
				$ret[$day] = $summaries[trim($item['SUMMARY'])];
			}
		}
		self::$dayTypes = $ret;
	}

	/**
	* Return the type of day for one day
	* @attr String the date in format YYYYMMDD
	**/
	function find_day_type($day) {
		return self::$dayTypes[$day];
	}


	/**
	* Fetch the custom schedules stored in SQL
	* @return Array containing custom summaries
	**/
	function get_custom_summaries() {
		global $I2_SQL;
		// return $I2_SQL->query('SELECT * FROM dayschedule_custom_summaries')->fetch_all_arrays(MYSQLI_ASSOC);
		return array();
	}

	function get_custom_schedules() {
		global $I2_SQL;
		//return $I2_SQL->query('SELECT * FROM dayschedule_custom_schedules')->fetch_all_arrays(MYSQLI_ASSOC);
		return array();
	}
}

?>
