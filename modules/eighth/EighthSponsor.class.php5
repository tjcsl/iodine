<?php
/**
* Just contains the definition for the class {@link EighthSponsor}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period sponsor.
* @package modules
* @subpackage Eighth
*/

class EighthSponsor {
	private $data = [];
	private static $cache = [];

	/**
	* The constructor for the {@link EighthSponsor} class.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public function __construct($sponsorid) {
		global $I2_SQL;

		if (isset(self::$cache[$sponsorid])) {
	        $this->data = &self::$cache[$sponsorid];
	    } else {
            $bulk = $I2_SQL->query('SELECT * FROM eighth_sponsors')->fetch_all_arrays_keyed("sid", Result::ASSOC);
            self::$cache = $bulk;
            if(isset($bulk[$sponsorid])) {
	    	$this->data = $bulk[$sponsorid];
	    } else {
	    	warn("Sponsor with ID does not exist: $sponsorid");
		$this->data = array("sid"=>0,"fname"=>"INVALID","lname"=>"INVALID","pickup"=>"","userid"=>"");
	    }
	    }
	}

	/**
	* Get all the sponsors.
	*
	* @access public
	*/
	public static function get_all_sponsors() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT sid,pickup,fname,lname,userid,CONCAT(lname,', ',fname)
			AS name_comma FROM eighth_sponsors ORDER BY lname,fname")->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Gets conflicts/impossibilities for sponsors in the given block
	*/
	public static function get_conflicts($blockid) {
		global $I2_SQL;
		$conflicts = [];
		$sponsorstorooms = [];
		$res = $I2_SQL->query('SELECT rooms,sponsors FROM eighth_block_map WHERE bid=%d',$blockid)->fetch_all_arrays(Result::ASSOC);
		foreach ($res as $row) {
			$sponsors = explode(',',$row['sponsors']);
			$rooms = explode(',',$row['rooms']);
			foreach ($sponsors as $sponsorid) {
				if (!isset($sponsorstorooms[$sponsorid])) {
					$sponsorstorooms[$sponsorid] = [];
				}
				foreach ($rooms as $room) {
					$sponsorstorooms[$sponsorid][] = $room;
				}
			}
		}
		$ret = [];

		foreach ($sponsorstorooms as $sponsorid=>$rooms) {
			if (count($rooms) < 2) {
				continue;
			}
			foreach ($rooms as $room) {
				if (!isset($ret[$room])) {
					$ret[$room] = [];
				}
				$sponsorotherrooms = [];
				/*
				** Make a list of the rooms OTHER THAN THIS ONE that the sponsor is in
				*/
				foreach ($rooms as $checkroom) {
					if ($checkroom != $room) {
						$sponsorotherrooms[] = $checkroom;
					}
				}
				if(is_numeric($sponsorid)) {
					$sponsor = new EighthSponsor($sponsorid);
					//d(print_r($sponsor->name,1),1);
					$ret[$room][] = [$sponsorid => ['sponsor'=>$sponsor,'rooms'=>$sponsorotherrooms]];
				}
			}
		}
		return $ret;
	}

	/**
	* Gets a sponsor's schedule starting from the given date
	*
	* @param int $sponsor The sponsorID
	* @param string $startdate The date to start from, in Y-m-d format
	* @return array An array of EighthActivity objects.
	*/
	public static function get_schedule($thissponsor,$startdate=NULL) {
			global $I2_SQL;
			if (!$startdate) {
				$startdate = date('Y-m-d');
			}
			$result = $I2_SQL->query('SELECT eighth_block_map.sponsors,eighth_block_map.activityid,eighth_block_map.bid FROM eighth_blocks LEFT JOIN eighth_block_map ON (eighth_block_map.bid = eighth_blocks.bid) WHERE date>=%t AND eighth_block_map.sponsors REGEXP \'(^|,)(%d)($|,)\' ORDER BY eighth_blocks.date,eighth_blocks.block',$startdate,$thissponsor)->fetch_all_arrays(MYSQLI_ASSOC);
			$activities = [];
			foreach($result as $activity) {
				$activities[] = new EighthActivity($activity['activityid'], $activity['bid']);
			}
			return $activities;
	}

	/**
	* Gets a sponsor's schedule on the given date
	* Can also handle an array of sponsors
	*
	* @param int $sponsor The sponsorID, can also be int[]
	* @param string $date The date to check, in Y-m-d format
	* @return array An array of EighthActivity objects.
	*/
	public static function get_schedule_on($thissponsor,$date=NULL) {
			global $I2_SQL;
			if (!$date) {
				$date = date('Y-m-d');
			}
			if(is_array($thissponsor)) {
				if(empty($thissponsor))
					return [];
				$result = $I2_SQL->query('SELECT eighth_block_map.sponsors,eighth_block_map.activityid,eighth_block_map.bid FROM eighth_blocks LEFT JOIN eighth_block_map ON (eighth_block_map.bid = eighth_blocks.bid) WHERE date=%t AND eighth_block_map.sponsors REGEXP "(^|,)(%X)($|,)" ORDER BY eighth_blocks.date,eighth_blocks.block',$date,implode("|",$thissponsor))->fetch_all_arrays(MYSQLI_ASSOC);
			} else {
				$result = $I2_SQL->query('SELECT eighth_block_map.sponsors,eighth_block_map.activityid,eighth_block_map.bid FROM eighth_blocks LEFT JOIN eighth_block_map ON (eighth_block_map.bid = eighth_blocks.bid) WHERE date=%t AND eighth_block_map.sponsors REGEXP \'(^|,)(%d)($|,)\' ORDER BY eighth_blocks.date,eighth_blocks.block',$date,$thissponsor)->fetch_all_arrays(MYSQLI_ASSOC);
			}
			$activities = [];
			foreach($result as $activity) {
				$activities[] = new EighthActivity($activity['activityid'], $activity['bid']);
			}
			return $activities;
	}

	/**
	* Adds a sponsor to the list.
	*
	* @access public
	* @param string $fname The sponsor's first name.
	* @param string $lname The sponsor's last name.
	* @param int $sid The SponsorID number.
	* @return int The ID of the (potentially new) sponsor.
	*/
	public static function add_sponsor($fname, $lname, $pickup = NULL, $sid = NULL, $userid = 0) {
		global $I2_SQL;
		Eighth::check_admin();
		if(is_int($userid)) {
			$userid="".$userid;
		}elseif(!is_numeric($userid)) {
			$userid="0";
		}
		if (!$sid) {
			$query = 'REPLACE INTO eighth_sponsors (fname,lname,pickup,userid) VALUES (%s,%s,%s,%s)';
			$queryarg = array($fname,$lname,$pickup,$userid);
			$result = $I2_SQL->query_arr($query, $queryarg);
			$id = $result->get_insert_id();
			$invquery = 'DELETE FROM eighth_sponsors WHERE sid=%d';
			$invarg = array($id);
			Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Add Sponsor');
			return $id;
		} else {
			$old = $I2_SQL->query('SELECT fname,lname,pickup,userid FROM eighth_sponsors WHERE sid=%d',$sid)->fetch_array(Result::ASSOC);
			$query = 'REPLACE INTO eighth_sponsors (fname,lname,pickup,sid,userid) VALUES (%s,%s,%s,%d,%d)';
			$queryarg = array($fname, $lname,$pickup, $sid, $userid);
			$I2_SQL->query_arr($query, $queryarg);
			$id = $sid;
			if (!$old) {
				$invquery = 'DELETE FROM eighth_sponsors WHERE sid=%d';
				$invarg = array($sid);
			} else {
				$invquery = $query;
				$invarg = array($old['fname'],$old['lname'],$old['pickup'],$sid,$old['userid']);
			}
			Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Modify Sponsor');
			return $sid;
		}
	}

	/**
	* Removes a sponsor from the list.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public static function remove_sponsor($sponsorid) {
		global $I2_SQL;
		Eighth::check_admin();
		$old = $I2_SQL->query('SELECT fname,lname FROM eighth_sponsors WHERE sid=%d',$sponsorid)->fetch_array(Result::ASSOC);
		if (!$old) {
				  //This sponsor already doesn't exist
				  d('Tried to delete nonexistant sponsor '.$sponsorid,5);
				  return;
		}
		//Eighth::start_undo_transaction();
		// TODO: Delete from the sponsor map and everything else as well
		// Get all activities which are sponsored by this person
		// And remove them from the sponsor list
		$actswithsponsor = $I2_SQL->query("SELECT aid,sponsors FROM eighth_activities WHERE sponsors LIKE '%%?%'",$sponsorid);
		$query = 'UPDATE eighth_activities SET sponsors=%s WHERE aid=%d';
		while ($row = $actswithsponsor->fetch_array(Result::ASSOC)) {
				  $heresponsors = explode(',',$row['sponsors']);
				  $ct = 0;
				  while ($ct < count($heresponsors)) {
							 if ($heresponsors[$ct] == $sponsorid) {
										array_splice($heresponsors,$ct,1);
							 }
							 $ct++;
				  }
				  $queryarg = array(implode(',',$heresponsors),$row['aid']);
				  $invarg = array($row['sponsors'],$row['aid']);
				  Eighth::push_undoable($query,$queryarg,$query,$invarg,'Remove Sponsor [from activity]');
		}
		$query = 'DELETE FROM eighth_sponsors WHERE sid=%d';
		$queryarg = array($sponsorid);
		$I2_SQL->query_arr($query, $queryarg);
		$invquery = 'REPLACE INTO eighth_sponsors (fname,lname,pickup,sid) VALUES(%s,%s,%s,%d)';
		$invarg = array($old['fname'],$old['lname'],$old['pickup'],$sponsorid);
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Remove Sponsor');
		//Eighth::end_undo_transaction();
	}

	/**
	* Removes this sponsor from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_sponsor($this->data['sid']);
		$this->data = [];
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		global $I2_SQL;
		if(isset($this->data[$name])) {
			return $this->data[$name];
		}
		else if($name == 'name') {
			return "{$this->data['fname']} {$this->data['lname']}";
		}
		else if($name == 'name_comma') {
			//Allow hacky last-name-only sponsors from old Intranet
			if (isset($this->data['fname']) && trim($this->data['fname']) != '') {
				return "{$this->data['lname']}, {$this->data['fname']}";
			}
			return $this->data['lname'];
		}
		else if($name == 'schedule') {
			$result = $I2_SQL->query('SELECT bid,activityid,sponsors FROM eighth_block_map ORDER BY bid');
			$activities = [];
			foreach($result as $activity) {
				$sponsors = explode(',', $activity['sponsors']);
				foreach($sponsors as $sponsor) {
					if($sponsor == $this->data['sid']) {
						$activities[] = new EighthActivity($activity['activityid'], $activity['bid']);
					}
				}
			}
			return $activities;
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
		Eighth::check_admin();
		if($name == 'fname') {
			$result = $I2_SQL->query('UPDATE eighth_sponsors SET fname=%s WHERE sid=%d', $value, $this->data['sid']);
			$this->data['fname'] = $value;
		}
		else if($name == 'lname') {
			$result = $I2_SQL->query('UPDATE eighth_sponsors SET lname=%s WHERE sid=%d', $value, $this->data['sid']);
			$this->data['lname'] = $value;
		}
		else if($name = 'name' && is_array($value) && count($value) == 2) {
			$result = $I2_SQL->query('UPDATE eighth_sponsors SET fname=%s, lname=%s WHERE sid=%d', $value[0], $value[1], $this->data['sid']);
			$this->data['fname'] = $value[0];
			$this->data['lname'] = $value[1];
		}
	}

	/**
	* Converts an array of sponsor IDs into {@EighthSponsor} objects.
	*
	* @access public
	* @param array $sponsorids The sponsor IDs.
	*/
	public static function id_to_sponsor($sponsorids) {
		$ret = [];
		foreach($sponsorids as $sponsorid) {
			$ret[] = new EighthSponsor($sponsorid);
		}
		return $ret;
	}
}

?>
