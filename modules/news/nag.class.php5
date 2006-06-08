<?php
/**
* Just contains the definition for the class Nag.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage News
* @filesource
*/

/**
* A class to represent a "Nag", i.e. a piece of news that users absolutely must see, and nag them.
* @package modules
* @subpackage News
*/
class Nag {

	private $data;

	public function __construct($nid) {
		$this->data = array('nid'=>$nid);
	}

	public function __get($name) {
		global $I2_SQL;
		$name = strtolower($name);
		if (isSet($this->data[$name])) {
			return $this->data[$name];
		}
		switch ($name) {
			case 'location':
				$locs = $this->__get('locations');
				$this->data['location'] = $locs[0];
				return $locs[0];
			case 'locations':
				$locs = $I2_SQL->query('SELECT locations FROM nags WHERE nid=%d',$this->data['nid'])->fetch_single_value();
				$this->data['locations'] = explode(',',$locs);
				return $this->data['locations'];
			case 'groups':
				$gids = flatten($I2_SQL->query('SELECT gid FROM nag_group_map WHERE nid=%d',$this->data['nid'])->fetch_all_arrays(Result::NUM));
				$this->data['groups'] = $gids;
				return $gids;
		}
		$res = $I2_SQL->query("SELECT $name FROM nags WHERE nid=%d",$this->data['nid'])->fetch_single_value();
		$this->data[$name] = $res;
		return $res;
	}

	public static function create($name,$locations) {
		global $I2_SQL;
		$res = $I2_SQL->query('INSERT INTO nags SET name=%s,locations=%s',$name,implode(',',$locations));
		return new Nag($res->get_insert_id());
	}

	public function add_group($gid) {
	}

	public function is_visible(User $user) {
		$gids = $this->__get('groups');
		foreach ($gids as $gid) {
			$group = new Group($gid);
			if ($group->has_member($user)) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public static function get_user_nags($user=NULL) {
		global $I2_USER;
		if (!$user) {
			$user = $I2_USER;
		}
		$ret = array();
		$nags = self::get_active_nags();
		foreach ($nags as $nag) {
			if ($nag->is_visible($user)) {
				$ret[] = $nag;
			}
		}
		return $ret;
	}

	public function allows_visit($triedlocation) {
		$locations = $this->__get('locations');
		foreach ($locations as $location) {
			if (stripos($triedlocation,$location) === 0) {
				return TRUE;
			}
		}
		return FALSE;
	}

	public static function get_active_nags() {
		global $I2_SQL;
		$res = $I2_SQL->query('SELECT DISTINCT nid FROM nag_group_map WHERE active=1');
		$ret = array();
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$ret[] = new Nag($row['nid']);
		}
		return $ret;
	}

}
?>
