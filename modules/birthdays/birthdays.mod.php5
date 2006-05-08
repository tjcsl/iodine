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

	private $birthdays;

	private $template_args = array();

	function init_box() {
		global $I2_ROOT;

		$this->cachefile = i2config_get('cache_dir','/var/cache/iodine/','core') . 'birthdays.cache';
		$mytime = getdate(time());

		if(!($this->birthdays = $this->get_cache($mytime))) {
			$this->birthdays = $this->get_birthdays(time());
			$this->store_cache($this->birthdays,$mytime);
		}
		
		return 'Today\'s Birthdays';
	}
	
	function display_box($disp) {
		$disp->disp('birthdays_box.tpl', array('birthdays' => $this->birthdays));
	}
	
	function init_pane() {
		global $I2_ARGS;
		
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
		for($i = -3; $i <= 3; $i+=1) {
			$timestamp = $time + ($i * Birthdays::DAY);
			$birthday = array();
			$birthday['date'] = $timestamp;
			$birthday['people'] = $this->get_birthdays($timestamp);
			$birthdays[] = $birthday;
		}
		
		$this->template_args['date'] = $time;
		$this->template_args['today'] = time();
		$this->template_args['birthdays'] = $birthdays;
		return 'Birthdays';
	}
	
	function display_pane($disp) {
		$disp->disp('birthdays_pane.tpl', $this->template_args);
	}

	private function get_birthdays($timestamp) {
		global $I2_LDAP;
		
		$date = '*' . date('md', $timestamp);
		$year = (int)date('Y', $timestamp);

		$people = array();
		$result = $I2_LDAP->search('ou=people,dc=tjhsst,dc=edu', "(birthday=$date)", 'iodineUidNumber');
		while ($uid = $result->fetch_single_value()) {
			$user = new User((int)$uid);
			$people[] = array(
				'uid' => $user->uid,
				'name' => $user->name,
				'grade' => $user->grade,
				'age' => $year - ((int)substr($user->birthday, 0, 4))
			);
		}

		usort($people, array($this, 'person_compare'));
		
		return $people;
	}

	private function person_compare($person1, $person2) {
		if ($person1['age'] == $person2['age']) {
			return strcmp($person1['name'], $person2['name']);
		}
		return $person2['age'] - $person1['age'];
	}

	private function get_cache($mytime) {
		if(!file_exists($this->cachefile) || !($contents = file_get_contents($this->cachefile))) {
			return FALSE;
		}
		
		$cache = unserialize($contents);
		
		if($cache['mday'] == $mytime['mday'] && $cache['mon'] == $mytime['mon']) {
			return $cache['bdays'];
		}
		return FALSE;
		
	}

	private function store_cache($birthdays,$mytime) {
		$store = array(
			'bdays' => $birthdays,
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
