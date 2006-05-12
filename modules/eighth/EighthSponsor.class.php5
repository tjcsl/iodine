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

	private static $cache = array();

	private $data = array();

	/**
	* The constructor for the {@link EighthSponsor} class.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public function __construct($sponsorid) {
		global $I2_SQL;
		if (isSet(self::$cache[$sponsorid])) {
				  return self::$cache[$sponsorid];
		}
		$this->data = $I2_SQL->query('SELECT * FROM eighth_sponsors WHERE sid=%d', $sponsorid)->fetch_array(Result::ASSOC);
		self::$cache[$sponsorid] = &$this;
	}

	/**
	* Get all the sponsors.
	*
	* @access public
	*/
	public static function get_all_sponsors() {
		global $I2_SQL;
		return $I2_SQL->query("SELECT sid,fname,lname,CONCAT(lname,', ',fname) 
			AS name_comma FROM eighth_sponsors ORDER BY lname,fname")->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* Gets conflicts/impossibilities for sponsors in the given block
	*/
	public static function get_conflicts($blockid) {
		global $I2_SQL;
		$conflicts = array();
		$sponsorstorooms = array();
		$res = $I2_SQL->query('SELECT rooms,sponsors FROM eighth_block_map');
		while ($row = $res->fetch_array(Result::ASSOC)) {
			$sponsors = explode(',',$row['sponsors']);
			$rooms = explode(',',$row['rooms']);
			foreach ($sponsors as $sponsorid) {
				if (!isSet($sponsorstorooms[$sponsor])) {
					$sponsorstorooms[$sponsor] = array();
				}
				foreach ($rooms as $room) {
					$sponsorstorooms[$sponsor][] = $room;
				}
			}
		}
		$ret = array();
		/*
		** Return an array like this:
		** 	$ret[$sponsor] =
		**			array(
		**				$room =>
		**					array(
		**						array($sponsorwithconflicts => $otherrooms),
		**						array($othersponsorwithconflicts => $otherrooms)
		**						)
		**			);
		*/
		foreach ($sponsorstorooms as $sponsorid=>$rooms) {
			if (count($rooms) == 0) {
				continue;
			}
			foreach ($rooms as $room) {
				if (!isSet($ret[$room])) {
					$ret[$room] = array();
				}
				$sponsorotherrooms = array();
				/*
				** Make a list of the rooms OTHER THAN THIS ONE that the sponsor is in
				*/
				foreach ($rooms as $checkroom) {
					if ($checkroom != $room) {
						$sponsorotherrooms[] = $checkroom;
					}
				}
				$sponsor = new EighthSponsor($sponsorid);
				$ret[$room][] = array($sponsor => $sponsorotherrooms);
			}
		}
		return $ret;
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
	public static function add_sponsor($fname, $lname, $sid = NULL) {
		global $I2_SQL;
		Eighth::check_admin();
		if (!$sid) {
			$query = 'REPLACE INTO eighth_sponsors (fname,lname) VALUES (%s,%s)';
			$queryarg = array($fname,$lname);
			$result = $I2_SQL->query_arr($query, $queryarg);
			$id = $result->get_insert_id();
			$invquery = 'DELETE FROM eighth_sponsors WHERE sid=%d';
			$invarg = array($id);
			Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Add Sponsor');
			return $id;
		} else {
			$old = $I2_SQL->query('SELECT fname,lname FROM eighth_sponsors WHERE sid=%d',$sid)->fetch_array(Result::ASSOC);
			$query = 'REPLACE INTO eighth_sponsors (fname,lname,sid) VALUES (%s,%s,%d)';
			$queryarg = array($fname, $lname, $sid);
			$I2_SQL->query_arr($query, $queryarg);
			$id = $sid;
			if (!$old) {
					  $invquery = 'DELETE FROM eighth_sponsors WHERE sid=%d';
					  $invarg = array($sid);
			} else {
					  $invquery = $query;
					  $invarg = array($old['fname'],$old['lname'],$sid);
			}
			Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Create Sponsor');
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
		Eighth::start_undo_transaction();
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
		$invquery = 'REPLACE INTO eighth_sponsors (fname,lname,sid) VALUES(%s,%s,%d)';
		$invarg = array($old['fname'],$old['lname'],$sponsorid);
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Remove Sponsor');
		Eighth::end_undo_transaction();
	}

	/**
	* Removes this sponsor from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_sponsor($this->data['sid']);
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
		else if($name == 'name') {
			return "{$this->data['fname']} {$this->data['lname']}";
		}
		else if($name == 'name_comma') {
			//Allow hacky last-name-only sponsors from old Intranet
			if ($this->data['fname'] && trim($this->data['fname']) != '') {
				return "{$this->data['lname']}, {$this->data['fname']}";
			}
			return $this->data['lname'];
		}
		else if($name == 'schedule') {
			$result = $I2_SQL->query('SELECT bid,activityid,sponsors FROM eighth_block_map ORDER BY bid');
			$activities = array();
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
		$ret = array();
		foreach($sponsorids as $sponsorid) {
			$ret[] = new EighthSponsor($sponsorid);
		}
		return $ret;
	}
}

?>
