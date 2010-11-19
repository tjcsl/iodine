<?php
/**
* Just contains the definition for the class {@link Birthdays}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Birthday
* @filesource
*/

/**
* A module that displays users with birthdays close to the current date.
* @package modules
* @subpackage Birthday
*/

class Birthdays implements Module {

	const DAY = 86400;

	private $birthdays_today;
	private $birthdays_tomorrow;

	private $template_args = array();

	private $tmp_ldap;

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
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

	function init_box() {
		global $I2_ROOT;

		$this->cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'birthdays.cache';
		$mytime = getdate();

		if(!(list($this->birthdays_today, $this->birthdays_tomorrow) = $this->get_cache($mytime))) {
			$cache = FALSE;
			try {
				$this->tmp_ldap = LDAP::get_generic_bind();
				$cache = TRUE;
			} catch (Exception $e) {
				d("Could not bind as generic user, searching birthdays as current user, not writing cache",1);
				global $I2_LDAP;
				$this->tmp_ldap = $I2_LDAP;
			}
			$this->birthdays_today = $this->get_birthdays(time());
			$this->birthdays_tomorrow = $this->get_birthdays(time() + self::DAY);
			//If the generic bind fails, bind as the user and don't cache the results.
			if ($cache) {	
				$this->store_cache($this->birthdays_today, $this->birthdays_tomorrow, $mytime);
			}
		}
		
		return 'Birthdays';
	}
	
	function display_box($disp) {
		$disp->disp('birthdays_box.tpl', array('today' => $this->birthdays_today, 'tomorrow' => $this->birthdays_tomorrow));
	}
	
	function init_pane() {
		global $I2_ARGS, $I2_LDAP;
		
		if (isset($_POST['date'])) {
			redirect('birthdays/' . str_replace('.', '/', $_POST['date']));
		}

		if (count($I2_ARGS) == 4) {
			list($month, $day, $year) = array_slice($I2_ARGS, 1);
			$time = mktime(0, 0, 0, $month, $day, $year);
		} else {
			$time = time();
		}

		$birthdays = array();

		$this->tmp_ldap = $I2_LDAP;

		for($i = -3; $i <= 3; $i+=1) {
			$timestamp = strtotime(date("j F, Y",$time)." +$i days ");
			$birthday = array();
			$birthday['date'] = $timestamp;
			$birthday['people'] = $this->get_birthdays($timestamp);
			$birthdays[] = $birthday;
		}
		
		$this->template_args['date'] = strtotime(date("j F, Y",$time));
		$this->template_args['today'] = time();
		$this->template_args['birthdays'] = $birthdays;
		return 'Birthdays';
	}
	
	function display_pane($disp) {
		$disp->disp('birthdays_pane.tpl', $this->template_args);
	}

	private function get_birthdays($timestamp) {
		$date = '*' . date('md', $timestamp);
		$year = (int)date('Y', $timestamp);

		$people = array();
		$result = $this->tmp_ldap->search('ou=people,dc=tjhsst,dc=edu', "(birthday=$date)", 'iodineUidNumber');
		while ($uid = $result->fetch_single_value()) {
			$user = new User((int)$uid);
			$person = array(
				'uid' => $user->uid,
				'name' => $user->name,
				'name_comma' => $user->name_comma,
				'grade' => $user->grade,
				'age' => $year - ((int)substr($user->birthday, 0, 4))
			);
			if ($person['age'] >= 0 && strpos($person['name'], "Withdrawn") == FALSE) {
				$people[] = $person;
			}
		}

		usort($people, array($this, 'person_compare'));
		
		return $people;
	}

	private function person_compare($person1, $person2) {
		//Sort by age, grade, then name_comma.
		if ($person1['age'] == $person2['age']) {
			if($person1['grade'] == $person2['grade'])
				return strcmp($person1['name_comma'], $person2['name_comma']);
			return $person2['grade'] - $person1['grade'];
		}
		return $person2['age'] - $person1['age'];
	}

	private function get_cache($mytime) {
		if(!file_exists($this->cachefile) || !($contents = file_get_contents($this->cachefile))) {
			return FALSE;
		}
		
		$cache = unserialize($contents);
		
		if($cache['mday'] == $mytime['mday'] && $cache['mon'] == $mytime['mon']) {
			return array($cache['bdays'], $cache['bdays_tom']);
		}
		return FALSE;
		
	}

	private function store_cache($today,$tomorrow,$mytime) {
		$store = array(
			'bdays' => $today,
			'bdays_tom' => $tomorrow,
			'mday' => $mytime['mday'],
			'mon' => $mytime['mon']
		);

		$fh = fopen($this->cachefile,'w');
		$serial = serialize($store);
		fwrite($fh,$serial);
		fclose($fh);
	}

	function get_name() {
		return 'Birthdays';
	}
}
?>
