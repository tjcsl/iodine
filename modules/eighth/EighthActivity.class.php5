<?php
/**
* Just contains the definition for the class {@link EighthActivity}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period activity.
* @package modules
* @subpackage Eighth
*/

class EighthActivity {

	private $data = array();
	const CANCELLED = 1;
	const PERMISSIONS = 2;
	const CAPACITY = 4;
	const STICKY = 8;
	const ONEADAY = 16;
	const PRESIGN = 32;

	/**
	* The constructor for the {@link EighthActivity} class
	*
	* @access public
	* @param int $activityid The activity ID.
	* @param int $blockid The block ID for an activity, NULL in general.
	*/
	public function __construct($activityid, $blockid = NULL) {
		global $I2_SQL;
		if ($activityid != NULL && $activityid != "") {
			$this->data = $I2_SQL->query("SELECT * FROM eighth_activities WHERE aid=%d", $activityid)->fetch_array(Result::ASSOC);
			$this->data['sponsors'] = (!empty($this->data['sponsors']) ? explode(",", $this->data['sponsors']) : array());
			$this->data['rooms'] = (!empty($this->data['rooms']) ? explode(",", $this->data['rooms']) : array());
			if($blockid) {
				$additional = $I2_SQL->query("SELECT bid,sponsors AS block_sponsors,rooms AS block_rooms,cancelled,comment,advertisement,attendancetaken FROM eighth_block_map WHERE bid=%d AND activityid=%d", $blockid, $activityid)->fetch_array(MYSQL_ASSOC);
				$this->data = array_merge($this->data, $additional);
				$this->data['block_sponsors'] = (!empty($this->data['block_sponsors']) ? explode(",", $this->data['block_sponsors']) : array());
				$this->data['block_rooms'] = (!empty($this->data['block_rooms']) ? explode(",", $this->data['block_rooms']) : array());
				$this->data['block'] = new EighthBlock($blockid);
			}
		}
	}

	/**
	* Adds a member to the activity.
	*
	* @access public
	* @param int $userid The student's user ID.
	* @param boolean $force Force the change.
	* @param int $blockid The block ID to add them to.
	*/
	public function add_member($userid, $force = false, $blockid = NULL) {
		global $I2_SQL;
		if($blockid == NULL) {
			$blockid = $this->data['bid'];
		}
		$ret = 0;
		if(count($this->get_members()) > $this->__get("capacity")) {
			$ret |= EighthActivity::CAPACITY;
		}
		if($this->cancelled) {
			$ret |= EighthActivity::CANCELLED;
		}
		if($this->data['restricted'] && !in_array($userid, $this->get_restricted_members())) {
			$ret |= EighthActivity::PERMISSIONS;
		}
		if(0/* check sticky */) {
			$ret |= EighthActivity::STICKY;
		}
		if(0/* check for one-a-day */) {
			$ret |= EighthActivity::ONEADAY;
		}
		if($this->presign && 0/* check days till */) {
			$ret |= EighthActivity::PRESIGN;
		}
		if(!$ret || $force) {
			$result = $I2_SQL->query("REPLACE INTO eighth_activity_map (aid,bid,userid) VALUES (%d,%d,%d)", $this->data['aid'], $blockid, $userid);
			if(mysql_error()) {
				$ret = -1;
			}
		}
		if($force && $ret != -1) {
			return 0;
		}
		return $ret;
	}
	
	/**
	* Add multiple members to the activity.
	*
	* @access private
	* @param array $userids The students' user IDs.
	* @param int $blockid The block ID to add them to.
	*/
	public function add_members($userids, $force = FALSE, $blockid = NULL) {
		foreach($userids as $userid) {
			$this->add_member($userid, $force, $blockid);
		}
	}

	/**
	* Removes a member from the activity.
	*
	* @access public
	* @param int $userid The student's user ID.
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_member($userid, $blockid = NULL) {
		global $I2_SQL;
		if($blockid == NULL) {
			$blockid = $this->data['bid'];
		}
		$result = $I2_SQL->query("DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d AND userid=%d", $this->data['aid'], $blockid, $userid);
	}

	/**
	* Removes multiple members from the activity.
	*
	* @access public
	* @param array $userid The students' user IDs.
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_members($userids, $blockid = NULL) {
		foreach($userids as $userid) {
			$this->remove_member($userid, $blockid);
		}
	}

	/**
	* Gets the members of the activity.
	*
	* @access public
	* @param int $blockid The block ID to get the members for.
	*/
	public function get_members($blockid = NULL) {
		global $I2_SQL;
		if($blockid == NULL) {
			if($this->data['bid']) {
				return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d", $this->data['bid'], $this->data['aid'])->fetch_all_arrays(Result::NUM));
			}
			else {
				return array();
			}
		}
		else {
			return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d", $blockid, $this->data['aid'])->fetch_all_arrays(Result::NUM));
		}
	}

	/**
	* Removes all members from the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_all($blockid = NULL) {
		global $I2_SQL;
		if($blockid == NULL) {
			$blockid = $this->data['bid'];
		}
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
	* @param array $userids The students' user IDs.
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
	* @param array $userids The students' user IDs.
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
		return flatten($I2_SQL->query("SELECT userid FROM eighth_activity_permissions WHERE aid=%d", $this->data['aid'])->fetch_all_arrays(Result::NUM));
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
	public static function get_all_activities($blockid = NULL, $restricted = FALSE) {
		global $I2_SQL;
		if($blockid == NULL) {
			return self::id_to_activity(flatten($I2_SQL->query("SELECT aid FROM eighth_activities " . ($restricted ? "WHERE restricted=1 " : "") . "ORDER BY name")->fetch_all_arrays(Result::NUM)));
		}
		else {
			return self::id_to_activity($I2_SQL->query("SELECT aid,bid FROM eighth_activities LEFT JOIN eighth_block_map ON (eighth_activities.aid=eighth_block_map.activityid) WHERE bid=%d " . ($restricted ? "AND restricted=1 " : "") . "ORDER BY name", $blockid)->fetch_all_arrays(Result::NUM));
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
		$result = $I2_SQL->query("INSERT INTO eighth_activities (name,sponsors,rooms,description,restricted) VALUES (%s,'%D','%D',%s,%d)", $name, $sponsors, $rooms, $description, ($restricted ? 1 : 0));
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
		else if($name == "members" && $this->data['bid']) {
			return $this->get_members();
		}
		else if($name == "members_obj" && $this->data['bid']) {
			return User::id_to_user($this->get_members());
		}
		else if($name == "absentees" && $this->data['bid']) {
			return EighthSchedule::get_absentees($this->data['bid'], $this->data['aid']);
		}
		else {
			switch($name) {
				case "comment_short":
					return substr($this->data['comment'], 0, 15);
				case "name_r":
					return $this->data['name'] . ($this->data['restricted'] ? " (R)" : "");
				case "sponsors_comma":
					$sponsors = EighthSponsor::id_to_sponsor($this->data['sponsors']);
					$temp_sponsors = array();
					foreach($sponsors as $sponsor) {
						$temp_sponsors[] = $sponsor->name;
					}
					return implode(",", $temp_sponsors);
				case "block_sponsors_comma":
					$sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
					$temp_sponsors = array();
					foreach($sponsors as $sponsor) {
						$temp_sponsors[] = $sponsor->name;
					}
					return implode(",", $temp_sponsors);
				case "block_sponsors_comma_short":
					$sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
					$temp_sponsors = array();
					foreach($sponsors as $sponsor) {
						$temp_sponsors[] = substr($sponsor->fname, 0, 1) . ". {$sponsor->lname}";
					}
					return implode(",", $temp_sponsors);
				case "rooms_comma":
					$rooms = EighthRoom::id_to_room($this->data['rooms']);
					$temp_rooms = array();
					foreach($rooms as $room) {
						$temp_rooms[] = $room->name;
					}
					return implode(",", $temp_rooms);
				case "block_rooms_comma":
					$rooms = EighthRoom::id_to_room($this->data['block_rooms']);
					$temp_rooms = array();
					foreach($rooms as $room) {
						$temp_rooms[] = $room->name;
					}
					return implode(", ", $temp_rooms);
				case "restricted_members":
					return $this->get_restricted_members();
				case "restricted_members_obj":
					return User::id_to_user($this->get_restricted_members());
				case "capacity":
					return $I2_SQL->query("SELECT SUM(capacity) FROM eighth_rooms WHERE rid IN (%D)", $this->data['block_rooms'])->fetch_single_value();
				case "member_count":
					return count($this->get_members());
			}
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
		else {
			switch($name) {
				case "sponsors":
					if(!is_array($value)) {
						$value = array($value);
					}
					$result = $I2_SQL->query("UPDATE eighth_activities SET sponsors='%D' WHERE aid=%d", $value, $this->data['aid']);
					$this->data['sponsors'] = $value;
					return;
				case "rooms":
					if(!is_array($value)) {
						$value = array($value);
					}
					$result = $I2_SQL->query("UPDATE eighth_activities SET rooms='%D' WHERE aid=%d", $value, $this->data['aid']);
					$this->data['rooms'] = $value;
					return;
				case "description":
					$result = $I2_SQL->query("UPDATE eighth_activities SET description=%s WHERE aid=%d", $value, $this->data['aid']);
					$this->data['description'] = $value;
					return;
				case "restricted":
					$result = $I2_SQL->query("UPDATE eighth_activities SET restricted=%d WHERE aid=%d", (int)$value, $this->data['aid']);
					$this->data['restricted'] = $value;
					return;
				case "presign":
					$result = $I2_SQL->query("UPDATE eighth_activities SET presign=%d WHERE aid=%d", (int)$value, $this->data['aid']);
					$this->data['presign'] = $value;
					return;
				case "oneaday":
					$result = $I2_SQL->query("UPDATE eighth_activities SET oneaday=%d WHERE aid=%d", (int)$value, $this->data['aid']);
					$this->data['oneaday'] = $value;
					return;
				case "bothblocks":
					$result = $I2_SQL->query("UPDATE eighth_activities SET bothblocks=%d WHERE aid=%d", (int)$value, $this->data['aid']);
					$this->data['bothblocks'] = $value;
					return;
				case "sticky":
					$result = $I2_SQL->query("UPDATE eighth_activities SET sticky=%d WHERE aid=%d", (int)$value, $this->data['aid']);
					$this->data['sticky'] = $value;
					return;
				case "cancelled":
					$result = $I2_SQL->query("UPDATE eighth_block_map SET cancelled=%d WHERE bid=%d AND activityid=%d", (int)$value, $this->data['bid'], $this->data['aid']);
					$this->data['cancelled'] = $value;
					return;
				case "comment":
					$result = $I2_SQL->query("UPDATE eighth_block_map SET comment=%s WHERE bid=%d AND activityid=%d", $value, $this->data['bid'], $this->data['aid']);
					$this->data['comment'] = $value;
					return;
				case "advertisement":
					$result = $I2_SQL->query("UPDATE eighth_block_map SET advertisement=%s WHERE bid=%d AND activityid=%d", $value, $this->data['bid'], $this->data['aid']);
					$this->data['advertisement'] = $value;
					return;
				case "attendancetaken":
					$result = $I2_SQL->query("UPDATE eighth_block_map SET attendancetaken=%d WHERE bid=%d AND activityid=%d", (int)$value, $this->data['bid'], $this->data['aid']);
					$this->data['attendancetaken'] = $value;
					return;
			}
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
			if(is_array($activityid)) {
				$ret[] = new EighthActivity($activityid[0], $activityid[1]);
			}
			else {
				$ret[] = new EighthActivity($activityid);
			}
		}
		return $ret;
	}

	public static function cancel($blockid, $activityid) {
		global $I2_SQL;
		$I2_SQL->query("UPDATE eighth_block_map SET cancelled=1 WHERE bid=%d AND activityid=%d", $blockid, $activityid);
	}
}

?>
