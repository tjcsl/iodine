<?php
/**
* Just contains the definition for the class {@link EighthRoom}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period room.
* @package modules
* @subpackage Eighth
*/

class EighthRoom {

	private $data = array();

	/**
	* The constructor for the {@link EighthRoom} class.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function __construct($roomid) {
		global $I2_SQL;
		$this->data = $I2_SQL->query("SELECT * FROM eighth_rooms WHERE rid=%d", $roomid)->fetch_array(Result::ASSOC);
	}

	/**
	* Get the utilization of rooms for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param array $columns The columns to include.
	* @param bool $overbooked Whether to only show overbooked activities or not.
	* @todo Make it actually use $columns.
	*/
	public static function get_utilization($blockid, $columns, $overbooked) {
		global $I2_SQL;
		$activities = EighthActivity::id_to_activity($I2_SQL->query("SELECT eighth_block_map.activityid,bid FROM eighth_block_map LEFT JOIN eighth_activities ON (eighth_block_map.activityid=eighth_activities.aid) WHERE bid=%d", $blockid)->fetch_all_arrays(Result::NUM));
		$utilizations = array();
		foreach($activities as $activity) {
			$rooms = $activity->block_rooms;
			foreach($rooms as $room) {
				$room = new EighthRoom($room);
				$students = EighthSchedule::count_members($blockid, $activity->aid);
				if(!$overbooked || $students > $room->capacity) {
					$utilizations[] = array("room" => $room, "activity" => $activity, "students" => $students);
				}
			}
		}
		usort($utilizations, array("self", "sort_rooms"));
		return $utilizations;
	}

	/**
	* Custom function to sort rooms.
	*
	* @param array $utilization1 The first room utilization.
	* @param array $utilization2 The second room utilization.
	* @return int Less than 0, 0, or greater than 0.
	*/
	private static function sort_rooms($utilization1, $utilization2) {
		return strcasecmp($utilization1['room']->name, $utilization2['room']->name);
	}

	/**
	* Gets the room assignment conflicts for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	*/
	public static function get_conflicts($blockid) {
		global $I2_SQL;
		$result = $I2_SQL->query("SELECT aid,name,restricted,eighth_block_map.rooms FROM eighth_block_map LEFT JOIN eighth_activities ON (eighth_block_map.activityid=eighth_activities.aid) WHERE bid=%d AND eighth_block_map.rooms != ''", $blockid)->fetch_all_arrays(Result::ASSOC);
		$conflicts = array();
		foreach($result as $activity) {
			$rooms = explode(",", $activity['rooms']);
			foreach($rooms as $room) {
				$eighth_room = new EighthRoom($room);
				if(!array_key_exists($eighth_room->name, $conflicts)) {
					$conflicts[$eighth_room->name] = array(array("aid" => $activity['aid'], "name" => ($activity['name'] . ($activity['restricted'] ? " (R)" :""))));
				}
				else {
					$conflicts[$eighth_room->name][] = array("aid" => $activity['aid'], "name" => ($activity['name'] . ($activity['restricted'] ? " (R)" :"")));
				}
			}
		}
		return array_filter($conflicts, array('EighthRoom', 'conflict_filter'));
	}

	/**
	* The filter to get only conflicts.
	*
	* @access public
	* @param array $element The array element.
	*/
	public static function conflict_filter($element) {
		return (count($element) > 1);
	}

	/**
	* Get all the rooms.
	*
	* @access public
	*/
	public static function get_all_rooms() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT rid, name FROM eighth_rooms ORDER BY name")->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Adds a room to the list.
	*
	* @access public
	* @param string $name The name of the room.
	* @param int $capacity The capacity of the room, -1 if unlimited.
	*/
	public static function add_room($name, $capacity) {
		global $I2_SQL;
		$result = $I2_SQL->query("INSERT INTO eighth_rooms (name, capacity) VALUES (%s,%d)", $number, $name, $capacity);
		return $result->get_insert_id();
	}

	/**
	* Removes a room from the list.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public static function remove_room($roomid) {
		global $I2_SQL;
		$result = $I2_SQL->query("DELETE FROM eighth_rooms WHERE rid=%d", $roomid);
		// TODO: Fix all the problems caused by taking away a room
	}

	/**
	* Removes this room from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_room($this->data['rid']);
		$this->data = array();
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
	* @param mixed $value The value to assign to the field;
	*/
	public function __set($name, $value) {
		global $I2_SQL;
		if($name == "name") {
			$result = $I2_SQL->query("UPDATE eighth_rooms SET  name=%s WHERE rid=%d", $value, $this->data['rid']);
			$this->data['name'] = $value;
		}
		else if($name == "capacity") {
			$result = $I2_SQL->query("UPDATE eighth_rooms SET capacity=%d WHERE rid=%d", $value, $this->data['rid']);
			$this->data['capacity'] = $value;
		}
	}

	/**
	* Convert as array of room IDs into {@link EighthRoom} objects.
	*
	* @access public
	* @param array $roomids The room IDs.
	*/
	public static function id_to_room($roomids) {
		$ret = array();
		foreach($roomids as $roomid) {
			$ret[] = new EighthRoom($roomid);
		}
		return $ret;
	}
}

?>
