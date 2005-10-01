<?php
/**
* Just contains the definition for the class {@link EighthGroup}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period group.
* @package modules
* @subpackage Eighth
*/

class EighthGroup {

	private $data = array();

	/**
	* The constructor for the {@link EighthGroup} class.
	*
	* @access public
	* @param int $groupid The group ID.
	*/
	public function __construct($groupid) {
		global $I2_SQL;
		$this->data = $I2_SQL->query("SELECT * FROM eighth_groups WHERE gid=%d", $groupid)->fetch_array(MYSQL_ASSOC);
		$this->data['members'] = flatten($I2_SQL->query("SELECT userid FROM eighth_group_map WHERE gid=%d", $groupid)->fetch_all_arrays(MYSQL_NUM));
	}

	/**
	* Gets a list of all the groups.
	*
	* @access public
	*/
	public static function get_all_groups() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT gid, name FROM eighth_groups ORDER BY name")->fetch_all_arrays(MYSQL_ASSOC);
	}

	/**
	* Adds a group to the list.
	*
	* @access public
	* @param string $name The name of the group.
	* @param string $description The description of the group.
	*/
	public static function add_group($name, $description = "") {
		global $I2_SQL;
		$result = $I2_SQL->query("INSERT INTO eighth_groups (name, description) VALUES (%s,%s)", $name, $description);
		return $result->get_insert_id();
	}

	/**
	* Removes a group from the list.
	*
	* @access public
	* @param int $groupid The group ID.
	*/
	public static function remove_group($groupid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_groups WHERE gid=%d", $groupid);
		// TODO: Remove from group map and everything else as well
	}

	/**
	* Removes this group from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_group($this->data['gid']);
		$this->data = array();
	}
		
	/**
	* Adds a member to the group.
	*
	* @access public
	* @param int $userid The student's user ID.
	*/
	public function add_member($userid) {
		global $I2_SQL;
		if(!in_array($userid, $this->data['members'])) {
			$result = $I2_SQL->query("INSERT INTO eighth_group_map (gid,userid) VALUES (%d,%d)", $this->data['gid'], $userid);
			$this->data['members'][] = $userid;
		}
	}

	/**
	* Adds multiple members to the group.
	*
	* @access public
	* @param array $userids The students' user IDs.
	*/
	public function add_members($userids) {
		foreach($userids as $userid) {
			$this->add_member($userid);
		}
	}
	
	/**
	* Removes a member from the group.
	*
	* @access public
	* @param int $userid The student's user ID.
	*/
	public function remove_member($userid) {
		global $I2_SQL;
		if(in_array($userid, $this->data['members'])) {
			$result = $I2_SQL->query("DELETE FROM eighth_group_map WHERE gid=%d AND userid=%d", $this->data['gid'], $userid);
			unset($this->data['members'][array_search($userid, $this->data['members'])]);
		}
	}

	/**
	* Removes multiple members from the group.
	*
	* @access public
	* @param array $userids The students' user IDs.
	*/
	public function remove_members($userids) {
		foreach($userids as $userid) {
			$this->remove_member($userid);
		}
	}

	/**
	* Removes all members from the group.
	*
	* @access public
	*/
	public function remove_all() {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_group_map WHERE gid=%d", $this->data['gid']);
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
	}

	/**
	* The magic __set function.
	*
	* @access public
	* @param string $name The name of the field to set.
	* @param mixed $value The value to assign to the field.
	*/
	public function __set($name, $value) {
		global $I2_SQL;
		if($name == "name") {
			$result = $I2_SQL->query("UPDATE eighth_groups SET name=%s WHERE gid=%d", $value, $this->data['gid']);
			$this->data['name'] = $value;
		}
		else if($name == "description") {
			$result = $I2_SQL->query("UPDATE eighth_groups SET description=%s WHERE gid=%d", $value, $this->data['gid']);
			$this->data['description'] = $value;
		}
	}

	/**
	* Convert an array of group IDs to {@link EighthGroup} objects.
	*
	* @access public
	* @param array $groupids The group IDs.
	*/
	public static function id_to_group($groupids) {
		$ret = array();
		foreach($groupids as $groupid) {
			$ret[] = new EighthGroup($groupid);
		}
		return $ret;
	}
}

?>
