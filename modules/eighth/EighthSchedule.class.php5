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
			$attendancetaken = FALSE, $cancelled = FALSE, $advertisement='') {
		global $I2_SQL;
		Eighth::check_admin();
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
		/*
		** Warning: adding a check for $aid validity will break dataimport.
		*/
		$result = $I2_SQL->query("REPLACE INTO eighth_block_map 
			(bid,activityid,sponsors,rooms,comment,attendancetaken,cancelled,advertisement) 
			VALUES (%d,%d,'%D','%D',%s,%d,%d,%s)", 
			$blockid, $activityid, $sponsors, $rooms, $comment,$attendancetaken,$cancelled,$advertisement);
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
		$result = $I2_SQL->query('DELETE FROM eighth_block_map WHERE bid=%d AND activityid=%d', $blockid, $activityid);
	}

	/**
	* Adds an absentee for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $userid The student's user ID.
	*/
	public static function add_absentee($blockid, $userid) {
		global $I2_SQL;
		Eighth::check_admin();
		$result = $I2_SQL->query('REPLACE INTO eighth_absentees (bid,userid) VALUES (%d,%d)', $blockid, $userid);
	}

	/**
	* Removes an absentee for a block.
	*
	* @access public
	* @param int $blockid The block ID.
	* @param int $userid The student's user ID.
	*/
	public static function remove_absentee($blockid, $userid) {
		global $I2_SQL;
		Eighth::check_admin();
		$result = $I2_SQL->query('DELETE FROM eighth_absentees WHERE bid=%d AND userid=%d', $blockid, $userid);
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
		return flatten($I2_SQL->query('SELECT eighth_absentees.userid FROM eighth_absentees LEFT JOIN eighth_activity_map USING (userid,bid) WHERE eighth_absentees.bid=%d AND aid=%d', $blockid, $activityid)->fetch_all_arrays(Result::NUM));
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
			$wheres[] = 'date >= %T';
		}
		if($end != null) {
			$wheres[] = 'date <= %T';
		}
		if(($start == null || $end == null) && count($wheres) == 1) {
			return $I2_SQL->query("SELECT userid, COUNT(userid) AS absences FROM eighth_absentees LEFT JOIN eighth_blocks USING (bid) WHERE {$wheres[0]} GROUP BY userid HAVING COUNT(*) >= %d AND COUNT(*) <= %d", ($start == null ? $end : $start), $lower, $upper)->fetch_all_arrays(MYSQL_ASSOC);
		}
		return $I2_SQL->query('SELECT userid, COUNT(userid) AS absences FROM eighth_absentees LEFT JOIN eighth_blocks USING (bid) ' . (count($wheres) != 0 ? 'WHERE ' : '') . implode(' AND ', $wheres) . ' GROUP BY userid HAVING COUNT(*) >= %d AND COUNT(*) <= %d', $start, $end, $lower, $upper)->fetch_all_arrays(MYSQL_ASSOC);
	}

	/**
	* Get the absences for a student.
	*
	* @access public
	* @param int $userid The student's user ID.
	*/
	public static function get_absences($userid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT aid,eighth_activity_map.bid FROM eighth_absentees LEFT JOIN eighth_activity_map USING (userid,bid) WHERE eighth_absentees.userid=%d', $userid)->fetch_all_arrays(Result::NUM);
	}

	/**
	* Get the next eighth period date.
	*
	* @access public
	*/
	public static function get_next_date() {
		global $I2_SQL;
		$date = $I2_SQL->query('SELECT date FROM eighth_blocks WHERE date >= %t ORDER BY date,block LIMIT 1', date('Y-m-d'))->fetch_array(Result::NUM);
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
	public static function get_activities($userid, $starting_date = NULL, $number_of_days = 14) {
		global $I2_SQL;
		if($starting_date == NULL) {
			$starting_date = date('Y-m-d');
		}
		return $I2_SQL->query('SELECT aid,eighth_blocks.bid FROM eighth_activity_map LEFT JOIN eighth_blocks USING (bid) WHERE userid=%d AND date >= %t AND date < ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block', $userid, $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(Result::NUM);
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
	public static function get_activity_schedule($activityid, $starting_date = NULL, $number_of_days = 14) {
		global $I2_SQL;
		$blocks = EighthBlock::get_all_blocks($starting_date, $number_of_days);
		$activities = array();
		$scheduled = TRUE;
		foreach($blocks as $block) {
			$result = $I2_SQL->query('SELECT rooms,sponsors,cancelled,comment from eighth_block_map WHERE bid=%d AND activityid=%d', $block['bid'], $activityid);
			if($result->num_rows() == 0) {
				$result = $I2_SQL->query('SELECT rooms,sponsors FROM eighth_activities WHERE aid=%d', $activityid);
				$scheduled = FALSE;
			}
			$activities[] = array('block' => $block, 'scheduled' => $scheduled) + $result->fetch_array(Result::ASSOC);
		}
		return $activities;
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
		return $I2_SQL->query('SELECT NULL FROM eighth_block_map WHERE activityid=%d AND bid=%d', $activityid, $blockid)->more_rows();
	}
}
