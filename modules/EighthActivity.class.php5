<?php
/**
* Just contains the definition for the class {@link EighthActivity}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package eighth
* @subpackage EighthActivity
* @filesource
*/

/**
* The module that holds the definition for an eighth period activity.
* @package eighth
* @subpackage EighthActivity
*/

class EighthActivity {

	private $data = array();

	/**
	* The constructor for the {@link EighthActivity} class
	*
	* @access public
	* @param int $activityid The activity ID.
	* @param int $blockid The block ID for an activity, NULL in general.
	*/
	public function __construct($activityid, $blockid = NULL) {
		global $I2_SQL;
		$this->data = $I2_SQL->query("SELECT * FROM eighth_activities WHERE aid=%d", $activityid)->fetch_array(MYSQL_ASSOC);
		$this->data['sponsors'] = (!empty($this->data['sponsors']) ? explode(",", $this->data['sponsors']) : array());
		$this->data['rooms'] = (!empty($this->data['rooms']) ? explode(",", $this->data['rooms']) : array());
		if($blockid != NULL) {
			$this->data += $I2_SQL->query("SELECT bid,sponsors AS block_sponsors,rooms AS block_rooms,cancelled,comment,advertisement,attendancetaken FROM eighth_block_map WHERE bid=%d AND activityid=%d", $blockid, $activityid)->fetch_array(MYSQL_ASSOC);
		$this->data['block_sponsors'] = (!empty($this->data['block_sponsors']) ? explode(",", $this->data['block_sponsors']) : array());
		$this->data['block_rooms'] = (!empty($this->data['block_rooms']) ? explode(",", $this->data['block_rooms']) : array());
			$this->data['block'] = new EighthBlock($blockid);
		}
	}

	/**
	* Adds a member to the activity.
	*
	* @access public
	* @param int $blockid The block ID to add them to.
	* @param int $userid The student's user ID.
	*/
	public function add_member($blockid, $userid) {
		global $I2_SQL;
		$result = $I2_SQL->query("REPLACE INTO eighth_activity_map (aid,bid,userid) VALUES (%d,%d,%d)", $this->data['aid'], $blockid, $userid);
	}
	
	/**
	* Add multiple members to the activity.
	*
	* @access private
	* @param int $blockid The block ID to add them to.
	* @param array $userids The students' user IDs.
	*/
	public function add_members($blockid, $userids) {
		foreach($userids as $userid) {
			$this->add_member($blockid, $userid);
		}
	}

	/**
	* Removes a member from the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	* @param int $userid The student's user ID.
	*/
	public function remove_member($blockid, $userid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d AND userid=%d", $this->data['aid'], $blockid, $userid);
	}

	/**
	* Removes multiple members from the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	* @param int $userid The students' user IDs.
	*/
	public function remove_members($blockid, $userids) {
		foreach($userids as $userid) {
			$this->remove_member($blockid, $userid);
		}
	}

	/**
	* Gets the members of the activity.
	*
	* @access public
	* @param int $blockid The block ID to get the members for.
	*/
	public function get_members($blockid) {
		global $I2_SQL;
		return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d", $blockid, $this->data['aid'])->fetch_all_arrays(MYSQL_NUM));
	}

	/**
	* Removes all members from the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_all($blockid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d", $this->data['aid'], $blockid);
	}

	/**
	* Adds a member to the restricted activity.
	*
	* @access public
	* @param int $userid The students's user ID.
	*/
	public function add_restricted_member($userid) {
		global $I2_SQL;
		$result = $I2_SQL->query("REPLACE INTO eighth_activity_permissions (aid,userid) VALUES (%d,%d)", $this->data['aid'], $userid);
	}

	/**
	* Adds multiple members to the restricted activity.
	*
	* @access public
	* @param int $userids The students' user ID.
	*/
	public function add_restricted_members($userids) {
		foreach($userids as $userid) {
			$this->add_restricted_member($userid);
		}
	}

	/**
	* Removes a member from the restricted activity.
	*
	* @access public
	* @param int $userid The students's user ID.
	*/
	public function remove_restricted_member($userid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_activity_permissions WHERE aid=%d AND userid=%d", $this->data['aid'], $userid);
	}

	/**
	* Removes multiple members from the restricted activity.
	*
	* @access public
	* @param int $userids The students' user ID.
	*/
	public function remove_restricted_members($userids) {
		foreach($userids as $userid) {
			$this->remove_restricted_member($userid);
		}
	}

	/**
	* Removes all members from the restricted activity.
	*
	* @access public
	*/
	public function remove_restricted_all() {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_activity_permissions WHERE aid=%d", $this->data['aid']);
	}

	/**
	* Gets the members of the restricted activity.
	*
	* @access public
	*/
	public function get_restricted_members() {
		global $I2_SQL;
		return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_permissions WHERE aid=%d", $this->data['aid'])->fetch_all_arrays(MYSQL_NUM));
	}

	/**
	* Adds a sponsor to the activity.
	*
	* @access public
	* @param int $sponsorid The ssponsor ID.
	*/
	public function add_sponsor($sponsorid) {
		if(!in_array($sponsorid, $this->data['sponsors'])) {
			$this->data['sponsors'][] = $sponsorid;
			$this->__set("sponsors", $this->data['sponsors']);
		}
	}

	/**
	* Removes a sponsor from the activity.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public function remove_sponsor($sponsorid) {
		if(in_array($sponsorid, $this->data['sponsors'])) {
			unset($this->data['sponsors'][array_search($sponsorid, $this->data['sponsors'])]);
			$this->__set("sponsors", $this->data['sponsors']);
		}
	}
	
	/**
	* Adds a room to the activity.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function add_room($roomid) {
		if(!in_array($roomid, $this->data['rooms'])) {
			$this->data['rooms'][] = $roomid;
			$this->__set("rooms", $this->data['rooms']);
		}
	}

	/**
	* Removes a room from the activity.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function remove_room($roomid) {
		if(in_array($roomid, $this->data['rooms'])) {
			unset($this->data['rooms'][array_search($roomid, $this->data['rooms'])]);
			$this->__set("rooms", $this->data['rooms']);
		}
	}
	
	/**
	* Gets all the available activities.
	*
	* @access public
	* @param int $blockid The room ID.
	*/
	public static function get_all_activities($blockid = NULL) {
		global $I2_SQL;
		if($blockid == NULL) {
			return $I2_SQL->query("SELECT aid, name, restricted FROM eighth_activities ORDER BY name")->fetch_all_arrays(MYSQL_ASSOC);
		}
		else {
			return $I2_SQL->query("SELECT aid, name, restricted FROM eighth_activities LEFT JOIN eighth_block_map ON (eighth_activities.aid=eighth_block_map.activityid) WHERE bid=%d ORDER BY name", $blockid)->fetch_all_arrays(MYSQL_ASSOC);
		}
	}

	/**
	* Adds an activity to the list.
	*
	* @access public
	* @param string $name The name of the activity.
	* @param array $sponsors The activity's sponsors.
	* @param array $rooms The activity's rooms.
	* @param string $description The description of the activity.
	* @param bool $restricted If this is a restricted activity.
	*/
	public static function add_activity($name, $sponsors = array(), $rooms = array(), $description = "", $restricted = FALSE) {
		global $I2_SQL;
		if(!is_array($sponsors)) {
			$sponsors = array($sponsors);
		}
		if(!is_array($rooms)) {
			$rooms = array($rooms);
		}
		$result = $I2_SQL->query("INSERT INTO eighth_activities (name,sponsors,rooms,description,restricted) VALUES (%s,%D,%D,%s,%d)", $name, $sponsors, $rooms, $description, ($restricted ? 1 : 0));
		return $result->get_insert_id();
	}

	/**
	* Removes an activity from the list.
	*
	* @access public
	* @param int $activityid The activity ID.
	*/
	public static function remove_activity($activityid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_activities WHERE aid=%d", $activityid);
		// TODO: Deal with the problems of deleting an activity
	}

	/**
	* Removes this activity from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_activity($this->data['aid']);
		$this->data = array();
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		global $I2_SQL;
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
		else if($name == "name_r") {
			return $this->data['name'] . ($this->data['restricted'] ? " (R)" : "");
		}
		else if($name == "sponsors_comma") {
			return implode(",", $this->data['sponsors']);
		}
		else if($name == "rooms_comma") {
			return implode(",", $this->data['rooms']);
		}
		else if($name == "members" && $this->data['bid']) {
			return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d", $this->data['bid'], $this->data['aid'])->fetch_all_arrays(MYSQL_NUM));
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
			$result = $I2_SQL->query("UPDATE eighth_activities SET name=%s WHERE aid=%d", $value, $this->data['aid']);
			$this->data['name'] = $value;
		}
		else if($name == "sponsors") {
			if(!is_array($value)) {
				$value = array($value);
			}
			$result = $I2_SQL->query("UPDATE eighth_activities SET sponsors=%D WHERE aid=%d", $value, $this->data['aid']);
			$this->data['sponsors'] = $value;
		}
		else if($name == "rooms") {
			if(!is_array($value)) {
				$value = array($value);
			}
			$result = $I2_SQL->query("UPDATE eighth_activities SET rooms=%D WHERE aid=%d", $value, $this->data['aid']);
			$this->data['rooms'] = $value;
		}
		else if($name == "description") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET description=%s WHERE aid=%d", $value, $this->data['aid']);
			$this->data['description'] = $value;
		}
		else if($name == "restricted") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET restricted=%d WHERE aid=%d", (int)$value, $this->data['aid']);
			$this->data['restricted'] = $value;
		}
		else if($name == "presign") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET presign=%d WHERE aid=%d", (int)$value, $this->data['aid']);
			$this->data['presign'] = $value;
		}
		else if($name == "oneaday") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET oneaday=%d WHERE aid=%d", (int)$value, $this->data['aid']);
			$this->data['oneaday'] = $value;
		}
		else if($name == "bothblocks") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET bothblocks=%d WHERE aid=%d", (int)$value, $this->data['aid']);
			$this->data['bothblocks'] = $value;
		}
		else if($name == "sticky") {
			$result = $I2_SQL->query("UPDATE eighth_activities SET sticky=%d WHERE aid=%d", (int)$value, $this->data['aid']);
			$this->data['sticky'] = $value;
		}
		else if($name == "cancelled") {
			$result = $I2_SQL->query("UPDATE eighth_block_map SET cancelled=%d WHERE bid=%d AND activityid=%d", (int)$value, $this->data['bid'], $this->data['aid']);
			$this->data['cancelled'] = $value;
		}
		else if($name == "comment") {
			$result = $I2_SQL->query("UPDATE eighth_block_map SET comment=%s WHERE bid=%d AND activityid=%d", $value, $this->data['bid'], $this->data['aid']);
			$this->data['comment'] = $value;
		}
		else if($name == "advertisement") {
			$result = $I2_SQL->query("UPDATE eighth_block_map SET advertisement=%s WHERE bid=%d AND activityid=%d", $value, $this->data['bid'], $this->data['aid']);
			$this->data['advertisement'] = $value;
		}
		else if($name == "attendancetaken") {
			$result = $I2_SQL->query("UPDATE eighth_block_map SET advertisement=%s WHERE bid=%d AND activityid=%d", (int)$value, $this->data['bid'], $this->data['aid']);
			$this->data['attendancetaken'] = $value;
		}
	}

	/**
	* Converts an array of activity IDs into {@link EighthActivity} objects;
	*
	* @access public
	* @param int $activityids The activity IDs.
	*/
	public static function id_to_activity($activityids) {
		$ret = array();
		foreach($activityids as $activityid) {
			$ret[] = new EighthActivity($activityid);
		}
		return $ret;
	}
}

?>
