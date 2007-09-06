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

	private static $cache = array();

	private $data = array();

	/**
	* The constructor for the {@link EighthRoom} class.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function __construct($roomid) {
		global $I2_SQL;
		if (!$roomid) {
			d('Null room constructed...',3);
			return;
		}
		if (isSet($cache[$roomid])) {
				  $this->data = &self::$cache[$roomid]->data;
		} else {
			$this->data = $I2_SQL->query('SELECT * FROM eighth_rooms WHERE rid=%d', $roomid)->fetch_array(Result::ASSOC);
			self::$cache[$roomid] = $this;
		}
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
	public static function get_utilization($blockid, $columns = NULL, $overbooked = FALSE, $sort = NULL) {
		global $I2_SQL;
		$activities = EighthActivity::id_to_activity($I2_SQL->query('SELECT eighth_block_map.activityid,bid FROM eighth_block_map LEFT JOIN eighth_activities ON (eighth_block_map.activityid=eighth_activities.aid) WHERE bid=%d', $blockid)->fetch_all_arrays(Result::NUM));
		$utilizations = array();
		foreach($activities as $activity) {
			$students = EighthSchedule::count_members($blockid, $activity->aid);
			$rooms = $activity->block_rooms;
			foreach($rooms as $room) {
				$room = new EighthRoom($room);
				if(!$overbooked || $students > $room->capacity) {
					$utilizations[] = array('room' => $room, 'activity' => $activity, 'students' => $students);
				}
			}
			if (count($rooms) == 0) {
				// foreach loop didn't catch the activity
				$utilizations[] = array('room' => new EighthRoom(i2config_get('default_rid', 934, 'eighth')), 'activity' => $activity, 'students' => $students);
			}
		}
		if (!$sort) {
				  return $utilizations;
		}
		if ($sort == 'room') {
			usort($utilizations, array('EighthRoom', 'sort_rooms'));
		} elseif ($sort == 'aid') {
			usort($utilizations, array('EighthRoom', 'sort_by_aid'));
		} elseif ($sort == 'name') {
			usort($utilizations, array('EighthRoom', 'sort_by_name'));
		} elseif ($sort == 'teacher') {
			usort($utilizations, array('EighthRoom', 'sort_by_teacher'));
		} elseif ($sort == 'students') {
			usort($utilizations, array('EighthRoom', 'sort_by_students'));
		} elseif ($sort == 'comments') {
			usort($utilizations, array('EighthRoom', 'sort_by_comments'));
		} else {
				  throw new I2Exception("Unknown sort type \"$sort\"");
		}
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

	// This is borrowed in EighthPrint
	public static function sort_by_teacher($utilization1, $utilization2) {
		return strcasecmp($utilization1['activity']->block_sponsors_comma, $utilization2['activity']->block_sponsors_comma);
	}
	
	private static function sort_by_name($utilization1, $utilization2) {
		return strcasecmp($utilization1['activity']->name, $utilization2['activity']->name);
	}
	
	private static function sort_by_comments($utilization1, $utilization2) {
		return strcasecmp($utilization1['activity']->block_comments, $utilization2['activity']->block_comments);
	}

	private static function sort_by_aid($utilization1, $utilization2) {
		return $utilization1['activity']->aid-$utilization2['activity']->aid;
	}
	
	private static function sort_by_students($utilization1, $utilization2) {
		return $utilization1['students']-$utilization2['students'];
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
			$rooms = explode(',', $activity['rooms']);
			foreach($rooms as $room) {
				$eighth_room = new EighthRoom($room);
				if(!array_key_exists($eighth_room->name, $conflicts)) {
					$conflicts[$eighth_room->name] = array(array('aid' => $activity['aid'], 'name' => ($activity['name'] . ($activity['restricted'] ? ' (R)' :''))));
				}
				else {
					$conflicts[$eighth_room->name][] = array('aid' => $activity['aid'], 'name' => ($activity['name'] . ($activity['restricted'] ? ' (R)' :'')));
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
		return $I2_SQL->query('SELECT rid, name FROM eighth_rooms ORDER BY name')->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Adds a room to the list.
	*
	* @access public
	* @param string $name The name of the room.
	* @param int $capacity The capacity of the room, -1 if unlimited.
	*/
	public static function add_room($name, $capacity, $rid=NULL) {
		global $I2_SQL;
		Eighth::check_admin();
		if (!$rid) {
				  $query = 'REPLACE INTO eighth_rooms (name, capacity) VALUES (%s,%d)';
				  $queryarg = array($name, $capacity);
				  $rid = $I2_SQL->query_arr($query,$queryarg)->get_insert_id();
				  $invquery = 'DELETE FROM eighth_rooms WHERE rid=%d';
				  $invarg = array($id);
		} else {
				  $old = $I2_SQL->query('SELECT * FROM eighth_rooms WHERE rid=%d',$rid)->fetch_array(Result::ASSOC);
				  $I2_SQL->query('REPLACE INTO eighth_rooms (name, capacity,rid) VALUES (%s,%d,%d)', $name, $capacity, $rid);
				  if (!$old) {
							 $invquery = 'DELETE FROM eighth_rooms WHERE rid=%d';
							 $invarg = array($rid);
				  } else {
							 $invquery = $query;
							 $invarg = array($old['name'],$old['capacity'],$old['rid']);
				  }
		}
		$query = 'REPLACE INTO eighth_rooms (rid,name,capacity) VALUES (%d,%s,%d)';
		$queryarg = array($name,$capacity,$rid);
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Add Room');
		return $rid;
	}

	/**
	* Removes a room from the list.
	*
	* @access public
	* @param int $rid The room ID.
	*/
	public static function remove_room($rid) {
		global $I2_SQL;
		Eighth::check_admin();
		$old = $I2_SQL->query('SELECT * FROM eighth_rooms WHERE rid=%d',$rid)->fetch_array(Result::ASSOC);
		if (!$old) {
				  d('Attempt made to delete nonexistant room '.$rid,5);
				  return;
		}

		//Eighth::start_undo_transaction();
		// Get rid of all block references to the room
		
		$res = $I2_SQL->query("SELECT bid,activityid,rooms FROM eighth_block_map WHERE rooms LIKE '%%?%'");
		$query = 'UPDATE eighth_activity_map SET rooms=%s WHERE bid=%d AND activityid=%d';
		while ($row = $res->fetch_array(Result::ASSOC)) {
				  $newrooms = array();
				  foreach (explode(',',$row['rooms']) as $room) {
							 if ($room != $rid) {
										$newrooms[] = $rid;
							 }
				  }
				  $queryarg = array(implode(',',$newrooms),$row['bid'],$row['activityid']);
				  $invarg = array($row['rooms'],$row['bid'],$row['activityid']);
				  $I2_SQL->query_arr($query,$queryarg);
				  Eighth::push_undoable($query,$queryarg,$query,$invarg,'Delete Room [from block]');
		}
	
		
		// Get rid of all activity references to the room
		
		$res = $I2_SQL->query("SELECT aid,rooms FROM eighth_activities WHERE rooms LIKE '%%?%'");
		$aquery = 'UPDATE eighth_activities SET rooms=%s WHERE aid=%d';
		while ($row = $res->fetch_array(Result::ASSOC)) {
				  $newrooms = array();
				  foreach (explode(',',$row['rooms']) as $room) {
							 if ($room != $rid) {
										$newrooms[] = $rid;
							 }
				  }
				  $queryarg = array(implode(',',$newrooms),$row['aid']);
				  $invarg = array($row['rooms'],$row['aid']);
				  $I2_SQL->query_arr($aquery,$queryarg);
				  Eighth::push_undoable($aquery,$queryarg,$query,$invarg,'Delete Room [from activity]');
		}

		
		$rquery = 'DELETE FROM eighth_rooms WHERE rid=%d';
		$queryarg = array($rid);
		$I2_SQL->query_arr($rquery, $queryarg);
		$invquery = 'REPLACE INTO eighth_rooms (rid,name,capacity) VALUES (%d,%s,%d)';
		$invarg = array($old['rid'],$old['name'],$old['capacity']);
		Eighth::push_undoable($rquery,$queryarg,$invquery,$invarg,'Remove Room');
		//Eighth::end_undo_transaction();
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
		if(is_array($this->data) && array_key_exists($name, $this->data)) {
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
		Eighth::check_admin();
		$old = $I2_SQL->query("SELECT $name FROM eighth_rooms WHERE rid=%d",$this->data['rid'])->fetch_single_value();
		if ($old === $value) {
				  //No change
				  return;
		}
		if($name == 'name') {
			$query = 'UPDATE eighth_rooms SET name=%s WHERE rid=%d';
		}
		else if($name == 'capacity') {
			$query = 'UPDATE eighth_rooms SET capacity=%d WHERE rid=%d';
		}
		$queryarg = array($value, $this->data['rid']);
		$I2_SQL->query_arr($query,$queryarg);
		$this->data[$name] = $value;
		$invarg = array($old, $this->data['rid']);
		Eighth::push_undoable($query,$queryarg,$query,$invarg,"Set Room $name");
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
			if (!$roomid) {
				continue;
			}
			$ret[] = new EighthRoom($roomid);
		}
		return $ret;
	}
}

?>
