<?php
/**
* Just contains the definition for the class {@link EighthSchedule}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the utilities for an eighth period schedule.
* @package modules
* @subpackage Eighth
*/

class EighthSchedule {
	/**
	* Schedule an eighth period activity.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $activityid The activity ID.
	* @param array $sponsors The sponsors for that activity for that block.
	* @param array $rooms The rooms for that activity for that block.
	*/
	public static function schedule_activity($blockid, $activityid, $sponsors = array(), $rooms = array(), $comment = '', 
		$attendancetaken = FALSE, $cancelled = FALSE, $advertisement='',$capacity = -1) {
		global $I2_SQL;
		Eighth::check_admin();
		if (!$sponsors) {
			  $sponsors = array();
		}
		if (!$rooms) {
		   $rooms = array();
		}
		if(!is_array($sponsors)) {
			$sponsors = array($sponsors);
		}
		if(!is_array($rooms)) {
			$rooms = array($rooms);
		}
		if (!$attendancetaken) {
			$attendancetaken = 0;
		}
		if (!$cancelled) {
			$cancelled = 0;
		}
		if (!$advertisement) {
			$advertisement = '';
		}
		if (!$comment) {
			$comment = '';
		}
		/*
		** Warning: adding a check for $aid validity will break dataimport.
		*/
		$old = $I2_SQL->query('SELECT * FROM eighth_block_map WHERE activityid=%d and bid=%d',$activityid,$blockid)->fetch_array(Result::ASSOC);
		
		$query = "REPLACE INTO eighth_block_map (bid,activityid,sponsors,rooms,comment,attendancetaken,cancelled,advertisement,capacity) VALUES (%d,%d,'%D','%D',%s,%d,%d,%s,%d)";
		$queryarg = array($blockid, $activityid, $sponsors, $rooms, $comment,$attendancetaken,$cancelled,$advertisement,$capacity);
		$result = $I2_SQL->query_arr($query,$queryarg);
		if ($old) {
			$invquery = $query;
			if (!isSet($old['comment'])) {
					  $old['comment'] = '';
			}
			if (!isSet($old['advertisement'])) {
					  $old['advertisement'] = '';
			}
			$invarg = array($old['bid'],$old['activityid'],explode(',',$old['sponsors']),explode(',',$old['rooms']),$old['comment'],$old['attendancetaken'],$old['cancelled'],$old['advertisement'],$old['capacity']);
		} else {
			$invquery = 'DELETE FROM eighth_block_map WHERE activityid=%d and bid=%d';
			$invarg = array($activityid, $blockid);
		}
		$specialquery = $I2_SQL->query('SELECT calendar,name,description FROM eighth_activities WHERE aid=%d',$activityid)->fetch_array(Result::ASSOC);
		if($specialquery && $specialquery['calendar']==1) {
			$blockquery = $I2_SQL->query('SELECT block,date FROM eighth_blocks WHERE bid=%d',$blockid)->fetch_array(Result::ASSOC);
			Calendar::add_event('eighth_'.$blockid.'_'.$activityid,True,strtotime($blockquery['date']),'8'.$blockquery['block'],'8'.$blockquery['block'],$specialquery['name'],$specialquery['description'],$specialquery['calendar']==1?array('eighth','eighthspecial'):array('eighth'));
		}
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Schedule Activity');
	}

	/**
	* Unschedule an eighth period activity.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $activityid The activity ID.
	*/
	public static function unschedule_activity($blockid, $activityid) {
		global $I2_SQL;
		Eighth::check_admin();
		
		$old = $I2_SQL->query('SELECT * FROM eighth_block_map WHERE activityid=%d and bid=%d',$activityid,$blockid)->fetch_array(Result::ASSOC);
		$query = 'DELETE FROM eighth_block_map WHERE bid=%d AND activityid=%d';
		$queryarg = array($blockid,$activityid);
		$result = $I2_SQL->query_arr($query,$queryarg);
		$invquery = "REPLACE INTO eighth_block_map (bid,activityid,sponsors,rooms,comment,attendancetaken,cancelled,advertisement) VALUES (%d,%d,'%D','%D',%s,%d,%d,%s)";
		$invarg = array($old['bid'],$old['activityid'],explode(',',$old['sponsors']),explode(',',$old['rooms']),$old['comment'],$old['attendancetaken'],$old['cancelled'],$old['advertisement']);
		$specialquery = $I2_SQL->query('SELECT calendar,name,description FROM eighth_activities WHERE aid=%d',$activityid)->fetch_array(Result::ASSOC);
		if($specialquery && $specialquery['calendar']==1) {
			Calendar::remove_event('eigthspecial_'.$blockid.'_'.$activityid);
		}
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Unschedule Activity');
	}

	/**
	* Adds an absentee for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $userid The student's user ID.
	*/
	public static function add_absentee($blockid, $userid, $issponsor=FALSE) {
		global $I2_SQL;
		//Eighth::check_admin();
		$block=new EighthBlock($blockid);
		if(!(Eighth::is_admin() || ($issponsor && strtotime($block->date) >= time()-24*60*60)))
			throw new I2Exception('Attempted to perform an unauthorized 8th-period action!');
		$user = new User($userid);
		$userid = $user->uid;
		$old = $I2_SQL->query('SELECT userid FROM eighth_absentees WHERE bid=%d AND userid=%d',$blockid,$userid)->fetch_single_value();
		if ($old) {
			//nothing to do
			return;
		}
		$query = 'REPLACE INTO eighth_absentees (bid,userid) VALUES (%d,%d)';
		$queryarg = array($blockid,$userid);
		$I2_SQL->query_arr($query, $queryarg);
		$invquery = 'DELETE FROM eighth_absentees WHERE bid=%d AND userid=%d';
		Eighth::push_undoable($query,$queryarg,$invquery,$queryarg,'Add Absentee');
	}

	/**
	* Removes an absentee for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $userid The student's user ID.
	*/
	public static function remove_absentee($blockid, $userid, $issponsor=FALSE) {
		global $I2_SQL;
		$block=new EighthBlock($blockid);
		if(!(Eighth::is_admin() || ($issponsor && strtotime($block->date) >= time()-24*60*60)))
			throw new I2Exception('Attempted to perform an unauthorized 8th-period action!');
		$user = new User($userid);
		$userid = $user->uid;
		$old = $I2_SQL->query('SELECT userid FROM eighth_absentees WHERE bid=%d AND userid=%d', $blockid, $userid)->fetch_single_value();
		if (!$old) {
			// Absentee not present
			return;
		}
		$query = 'DELETE FROM eighth_absentees WHERE bid=%d AND userid=%d';
		$queryarg = array($blockid, $userid);
		$result = $I2_SQL->query_arr($query,$queryarg);
		$invquery = 'INSERT INTO eighth_absentees (bid,userid) VALUES(%d,%d)';
		Eighth::push_undoable($query,$queryarg,$invquery,$queryarg,'Remove Absentee');
	}

	/**
	* Get the absentees for a block and activity.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $activityid The activity ID.
	*/
	public static function get_absentees($blockid, $activityid) {
		global $I2_SQL;
		return flatten($I2_SQL->query('SELECT eighth_absentees.userid FROM eighth_absentees LEFT JOIN eighth_activity_map USING (userid,bid) WHERE eighth_absentees.bid=%d AND aid=%d ORDER BY eighth_absentees.bid DESC', $blockid, $activityid)->fetch_all_arrays(Result::NUM));
	}

	/**
	* Get all the absentees with lower # <= absences <= upper# and start date <= date <= end date
	*
	* @access public
	* @param int $lower The lower number
	* @param int $upper The upper number
	* @param string $start The start date
	* @param string $end The end date
	*/
	public static function get_delinquents($lower = 1, $upper = 1000, $start = null, $end = null) {
		global $I2_SQL;
		$wheres = array();
		if($start != null) {
			$wheres[] = 'date >= %t';
		}
		if($end != null) {
			$wheres[] = 'date <= %t';
		}
		if(($start == null || $end == null) && count($wheres) == 1) {
			return $I2_SQL->query("SELECT userid, COUNT(userid) AS absences FROM eighth_absentees LEFT JOIN eighth_blocks USING (bid) WHERE {$wheres[0]} GROUP BY userid HAVING COUNT(*) >= %d AND COUNT(*) <= %d", ($start == null ? $end : $start), $lower, $upper)->fetch_all_arrays(Result::ASSOC);
		}
		return $I2_SQL->query('SELECT userid, COUNT(userid) AS absences FROM eighth_absentees LEFT JOIN eighth_blocks USING (bid) ' . (count($wheres) != 0 ? 'WHERE ' : '') . implode(' AND ', $wheres) . ' GROUP BY userid HAVING COUNT(*) >= %d AND COUNT(*) <= %d', $start, $end, $lower, $upper)->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Get the absences for a student.
	*
	* @access public
	* @param int $userid The student's user ID.
	*/
	public static function get_absences($userid) {
		global $I2_SQL,$I2_USER;
		$user = new User($userid);
		if ($user->uid != $I2_USER->uid) {
			//Eighth::check_admin();
			if (!($I2_USER->is_group_member('grade_staff') || $I2_USER->is_group_member('admin_eighth'))) {
				//throw new I2Exception('Unauthorized request for absence information!');
				//Fail silently so students may also use vcp_schedule interface to view other students schedules, but still within the user's privacy settings.
				return array();
			}
		}
		return $I2_SQL->query('SELECT aid,eighth_activity_map.bid FROM eighth_absentees LEFT JOIN eighth_activity_map USING (userid,bid) LEFT JOIN eighth_blocks USING (bid) WHERE eighth_absentees.userid=%d ORDER BY date,block', $userid)->fetch_all_arrays(Result::NUM);
	}

	/**
	* Get the next eighth period date.
	*
	* @access public
	* @param int $delay Amount to delay the rollover.
	*/
	public static function get_next_date($delay=0) {
		global $I2_SQL;
		if(date('Hi')>1550+$delay) {
			$date = $I2_SQL->query('SELECT date FROM eighth_blocks WHERE date > %t ORDER BY date,block LIMIT 1', date('Y-m-d'))->fetch_array(Result::NUM);
		} else {
			$date = $I2_SQL->query('SELECT date FROM eighth_blocks WHERE date >= %t ORDER BY date,block LIMIT 1', date('Y-m-d'))->fetch_array(Result::NUM);
		}
		return $date[0];
	}

	/**
	* Gets the activities a student is signed up for on a specific date.
	*
	* @access public
	* @param $userid The student's user ID.
	* @param $date The date to get activities for.
	* @return array An array of ActivityIDs and BlockIDs.
	*/
	public static function get_activities($userid, $starting_date = NULL, $number_of_days = 14,$permission_override=FALSE) {
		global $I2_SQL, $I2_USER;
		if(is_numeric($userid)) {
			$user = new User($userid);
		} else if ($userid instanceof User) {
			$user = $userid;
		} else {
			throw new I2Exception("Invalid user data sent to EighthSchedule\-\>get_activities()");
		}
		// Only show students who want to be found.
		if($permission_override===TRUE||EighthSchedule::can_view_schedule($user))
		{
			$userid = $user->uid;
			if($starting_date == NULL) {
				$starting_date = date('Y-m-d');
			}
			return $I2_SQL->query('SELECT aid,eighth_blocks.bid FROM eighth_activity_map LEFT JOIN eighth_blocks USING (bid) WHERE userid=%d AND date >= %t AND date < ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block', $userid, $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(Result::NUM);
		}
		return array();  // I guess this person doesn't want to be found.
	}

	/**
	* Gets the activities a sponsor is sponsoring on a specific date.
	*
	* @access public
	* @param $userid The user ID.
	* @param $date The date to get activities for.
	* @return array An array of ActivityIDs and BlockIDs.
	*/
	public static function get_activities_sponsored($userid, $starting_date = NULL, $number_of_days = 14,$permission_override=FALSE) {
		global $I2_SQL, $I2_USER;
		if ($userid instanceof User) {
			$userid = $userid->iodineUIDNumber;
		} else if(!is_numeric($userid)) {
			throw new I2Exception("Invalid user data sent to EighthSchedule\-\>get_activities()");
		}
		$hosts = $I2_SQL->query("SELECT sid FROM eighth_sponsors WHERE userid=%d",$userid)->fetch_col('sid');
		// If they don't host anything, don't bother with further checks.
		if(empty($hosts))
			return array();
		if($starting_date == NULL) {
			$starting_date = date('Y-m-d');
		}
		$res=$I2_SQL->query('SELECT activityid,eighth_blocks.bid FROM eighth_block_map LEFT JOIN eighth_blocks USING (bid) WHERE sponsors REGEXP "(^|,)(%X)($|,)" AND date >= %t AND date < ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block',implode("|",$hosts), $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(Result::NUM);
		// We do not want the Z-HASNOTSELECTED links in this, so we'll filter them out.
		$ret=array();
		$default_aid=i2config_get('default_aid','999','eighth');
		foreach($res as $r) { // There's probably a more efficient function that does this...
			if($r[0]!=$default_aid)
				$ret[]=$r;
		}
		return $ret;
		//return $I2_SQL->query('SELECT aid,eighth_blocks.bid FROM eighth_activity_map LEFT JOIN eighth_blocks USING (bid) WHERE userid=%d AND date >= %t AND date < ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block', $userid, $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(Result::NUM);
	}

	/**
	* Gets the activity a student is signed up for during the given block.
	*
	* @return array An ActivityID.
	*/
	public static function get_activities_by_block($userid, $blockid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT aid FROM eighth_activity_map WHERE userid=%d AND bid=%d',$userid,$blockid)->fetch_single_value();
	}

	/**
	* Gets the schedule for a particular activity.
	*
	* @access public
	* @param int $activityid The activity ID.
	* @param string $starting_date The starting date for the list, usually NULL.
	* @param int $number_of_days The number of days to return.
	*/
	public static function get_activity_schedule($activityid, $starting_date = NULL, $number_of_days = 999) {
		global $I2_SQL;
		$blocks = EighthBlock::get_all_blocks($starting_date, $number_of_days);
		$unscheduled_blocks = array();
		$activities = array();
		foreach($blocks as $block) {
			$scheduled = TRUE;
			$result = $I2_SQL->query('SELECT rooms,sponsors,cancelled,comment from eighth_block_map WHERE bid=%d AND activityid=%d', $block['bid'], $activityid);
			if($result->num_rows() == 0) {
				$result = $I2_SQL->query('SELECT rooms,sponsors FROM eighth_activities WHERE aid=%d', $activityid);
				$scheduled = FALSE;
			}
			$data = $result->fetch_array(Result::ASSOC);
			$data['rooms_array'] = "'" . strtr($data['rooms'], array(',' => "','")) . "'";
			$data['rooms_obj'] = EighthRoom::id_to_room(array_filter(explode(',', $data['rooms'])));
			$data['rooms_name_array'] = array();
			foreach($data['rooms_obj'] as $room) {
				$data['rooms_name_array'][] = $room->name;
			}
			$data['rooms_name_array'] = "'" . implode("','", $data['rooms_name_array']) . "'";
			$data['sponsors_array'] = "'" . strtr($data['sponsors'], array(',' => "','")) . "'";
			$data['sponsors_obj'] = EighthSponsor::id_to_sponsor(array_filter(explode(',', $data['sponsors'])));
			$data['sponsors_name_array'] = array();
			foreach($data['sponsors_obj'] as $sponsor) {
				$data['sponsors_name_array'][] = $sponsor->name_comma;
			}
			$data['sponsors_name_array'] = "'" . implode("','", $data['sponsors_name_array']) . "'";
			if(!$scheduled) {
				$unscheduled_blocks[] = $block['bid'];
			}
			$activities[] = array('block' => $block, 'scheduled' => $scheduled) + $data;
		}
		return array($unscheduled_blocks, $activities);
	}

	/**
	* Counts the number of students in an activity and block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $activityid The activity ID.
	*/
	public static function count_members($blockid, $activityid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d', $blockid, $activityid)->num_rows();
	}

	/**
	*
	*
	*/
	public static function is_activity_valid($activityid, $blockid) {
		global $I2_SQL;
		if(!$activityid || ! $blockid) {
			return FALSE;
		}
		return $I2_SQL->query('SELECT NULL FROM eighth_block_map WHERE activityid=%d AND bid=%d', $activityid, $blockid)->more_rows();
	}

	/**
	* Determines whether or not the current user can view a specified user's eighth schedule.
	*
	* @access public
	* @param User $user The {@link User} to who we are trying to view.
	* @return boolean
	*/
	public static function can_view_schedule($user) {
		global $I2_USER;
		if($I2_USER->is_group_member('admin_eighth') || $I2_USER->grade=='staff')
			//I am someone who should always see the schedule.
			return TRUE;
		if($user->showeighthself && $user->showeighth)
			//This person both wants to show and has parental permission.
			return TRUE;
		if($user->uid == $I2_USER->uid)
			//It's a me!  Mario!
			return TRUE;
		//I've fallen through.
		return FALSE;
	}
}
