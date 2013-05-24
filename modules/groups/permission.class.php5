<?php
/**
* Just contains the definition for the class {@link Permission}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Group
* @filesource
*/

/**
* The module that runs permissions
* @package modules
* @subpackage Group
*/
class Permission {

	private $pid;
	private $name;
	private $desc;

	private static $pid_map;
	private static $name_map;
	
	private static $perm_cache;

	/**
	 * Helper method to generate PID/name maps
	 */
	private static function gen_maps() {
		global $I2_SQL, $I2_CACHE;
		self::$pid_map = unserialize($I2_CACHE->read(get_class(),'pid_map'));
		self::$name_map = unserialize($I2_CACHE->read(get_class(),'name_map'));
		if(self::$pid_map === FALSE) {
			$res = $I2_SQL->query('SELECT name,pid FROM permissions')->fetch_all_arrays(Result::ASSOC);
			foreach ($res as $row) {
				self::$pid_map[$row['name']] = $row['pid'];
				self::$name_map[$row['pid']] = $row['name'];
			}
			$I2_CACHE->store(get_class(),'pid_map',serialize(self::$pid_map));
			$I2_CACHE->store(get_class(),'name_map',serialize(self::$name_map));
		}
	}

	/**
	 * The constructor
	 */
	private function __construct($perm) {
		global $I2_SQL, $I2_CACHE;

		// Generate the PID/name maps if they do not exist
		if (self::$pid_map === NULL) {
			self::gen_maps();
		}
		
		if (isset(self::$name_map[$perm])) {
			// Passed PID
			$this->pid = $perm;
			$this->name = self::$name_map[$perm];
			$this->desc = $I2_CACHE->read($this,'desc_'.$this->pid);
			if ($this->desc === FALSE) {
				$this->desc = $I2_SQL->query('SELECT description FROM permissions WHERE pid=%d', $perm)->fetch_single_value();
				$I2_CACHE->store($this,'desc_'.$this->pid,$this->desc);
			}
		}
		elseif (isset(self::$pid_map[$perm])) {
			// Passed permission name
			$perm = strtoupper($perm);
			$this->name = $perm;
			$this->pid = self::$pid_map[$perm];
			$this->desc = $I2_CACHE->read($this,'desc_'.$this->pid);
			if ($this->desc === FALSE) {
				$this->desc = $I2_SQL->query('SELECT description FROM permissions WHERE name=%s', $perm)->fetch_single_value();
				$I2_CACHE->store($this,'desc_'.$this->pid,$this->desc);
			}
		}
		else {
			throw new I2Exception("Nonexistant Permission $perm given to the Permission constructor");
		}

		self::$perm_cache[$perm] = $this;
	}
	
	public static function getPermission($perm) {
		if (isset(self::$perm_cache[$perm])) {
			return self::$perm_cache[$perm];
		} else {
			return new Permission($perm);
		}
	}

	/**
	 * Create a new permission
	 *
	 * @param string $perm The name of the new permission
	 * @param string $desc The description of the new permission
	 * @return int The ID number of the new permission
	 */
	public static function add_perm($perm, $desc = 'No description available') {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if (! Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to create a new permission!');
		}

		$res = $I2_SQL->query('INSERT INTO permissions (name, description) VALUES (%s, %s)', strtoupper($perm), $desc);
		return self::getPermission($res->get_insert_id());
	}

	/**
	 * List all permissions
	 *
	 * @return array An array containing all existant permissions
	 */
	public static function list_permissions() {
		if (self::$pid_map === NULL) {
			self::gen_maps();
		}

		$ret = [];
		foreach (self::$pid_map as $name=>$pid) {
			d($pid);
			$ret[] = self::getPermission($pid);
		}
		return $ret;
	}

	/**
	 * Determine if a permission exists
	 *
	 * @param $perm Either a PID or permission name
	 * @return boolean TRUE if the given permission exists, FALSE otherwise
	 */
	public static function perm_exists($perm) {
		if (is_numeric($perm)) {
			return array_key_exists($perm, self::$name_map);
		}
		else {
			return array_key_exists($perm, self::$pid_map);
		}
	}

	/**
	 * The PHP magical __get function
	 */
	public function __get($var) {
		switch ($var) {
		case 'pid':
			return $this->pid;
		case 'name':
			return $this->name;
		case 'desc':
		case 'description':
			return $this->desc;
		default:
			throw new I2Exception("Attempted to retrieve invalid variable $var in Permission");
		}
	}

	/**
	 * Delete the permission
	 */
	public function del_perm() {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if (! Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to create a new permission!');
		}

		$I2_SQL->query('DELETE FROM permissions WHERE pid=%d', $this->pid);
		$I2_SQL->query('DELETE FROM groups_user_perms WHERE pid=%d', $this->pid);
		$I2_SQL->query('DELETE FROM groups_group_perms WHERE pid=%d', $this->pid);
	}

	/**
	 * Rename the permission
	 *
	 * @param string $name The new name of the permission
	 */
	public function set_name($name) {
		global $I2_SQL, $I2_USER;

		if (! Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to edit permissions');
		}

		$name = strtoupper($name);

		$I2_SQL->query('UPDATE permissions SET name=%s WHERE pid=%d', $name, $this->pid);
		$this->name = $name;
	}

	/**
	 * Give the permission a new description
	 *
	 * @param string $desc The new description for the permission
	 */
	public function set_desc($desc) {
		global $I2_SQL, $I2_USER;

		if (! Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to edit permissions');
		}

		$I2_SQL->query('UPDATE permissions SET description=%s WHERE pid=%d', $desc, $this->pid);
		$this->desc = $desc;
	}
}
?>
