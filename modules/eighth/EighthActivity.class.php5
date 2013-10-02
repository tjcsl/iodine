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

	private static $membercache = [];
	private static $passcache = [];
	private static $permissionscache = [];
	private $data = [];
	const CANCELLED = 1;
	const PERMISSIONS = 2;
	const CAPACITY = 4;
	const STICKY = 8;
	const ONEADAY = 16;
	const PRESIGN = 32;
	const LOCKED = 64;
	const PAST = 128;
	const RESTRICTLIST = 256;

	/**
	* The constructor for the {@link EighthActivity} class
	*
	* @access public
	* @param int $activityid The activity ID.
	* @param int $blockid The block ID for an activity, NULL in general.
	*/
	public function __construct($activityid, $blockid = NULL, $special = NULL, $data = NULL) {
		global $I2_SQL,$I2_USER;
		if ($special == "CANCELLED") {
			$tmp = new EighthActivity($activityid);
			$this->data['name'] = $tmp->name;
			$this->data['cancelled'] = TRUE;
			$this->data['bothblocks'] = FALSE;
			$this->data['sticky'] = FALSE;
			$this->data['aid'] = $activityid;
			$this->data['bid'] = $blockid;
			$this->data['block'] = new EighthBlock($blockid);
			return;
		}
		if($special == "MASSLOAD") {
			$this->data = $data;
			$this->data['block_sponsors'] = (!empty($this->data['block_sponsors']) ? explode(",", $this->data['block_sponsors']) : []);
			$this->data['block_rooms'] = (!empty($this->data['block_rooms']) ? explode(",", $this->data['block_rooms']) : []);
			return;
		}
		if (! self::activity_exists($activityid)) {
			if ($activityid==-3) {
				$this->data['name']='Signed up for a different block at this time';
				$this->data['description']='You are signed up for another eighth-period activity on this day that occurs at the same time. Because of this, you have been signed up for this activity.';
				$this->data['restricted']=0;
				$this->data['oneaday']=0;
				$this->data['bothblocks']=0;
				$this->data['sticky']=0;
				$this->data['special']=0;
				$this->data['calendar']=0;
				$this->data['sponsors'] = [];
				$this->data['rooms'] = [];
				$this->data['aid'] = $activityid;
				if($blockid) {
					$this->data['bid']=$blockid;
					$this->data['block'] = new EighthBlock($blockid);
					$this->data['cancelled']=0;
					$this->data['attendancetaken']=0;
					$this->data['roomchanged']=0;
					$this->data['comment']='';
					$this->data['advertisement']='';
					$this->data['capacity']=9001;
					$this->data['block_sponsors'] = [];
					$this->data['block_rooms'] = [];
				}
				return;
			}
			throw new I2Exception('Tried to create an EighthActivity object for a nonexistent activity! (Activity id was '.$activityid.')');
		}
		if ($activityid != NULL && $activityid != '') {
			$this->data = $I2_SQL->query('SELECT * FROM eighth_activities WHERE aid=%d', $activityid)->fetch_array(Result::ASSOC);
			$this->data['sponsors'] = (!empty($this->data['sponsors']) ? explode(',', $this->data['sponsors']) : []);
			$this->data['rooms'] = (!empty($this->data['rooms']) ? explode(',', $this->data['rooms']) : []);
			$this->data['aid'] = $activityid;
			if($blockid) {
				$this->data['block'] = new EighthBlock($blockid);
				$additional = $I2_SQL->query('SELECT bid,sponsors AS block_sponsors,rooms AS block_rooms,cancelled,comment,advertisement,attendancetaken FROM eighth_block_map WHERE bid=%d AND activityid=%d', $blockid, $activityid)->fetch_array(MYSQLI_ASSOC);
				if(!$additional)
					throw new I2Exception("Activity $activityid does not exist for block $blockid ({$this->data['block']->date}, {$this->data['block']->block} block)!");
				$this->data = array_merge($this->data, $additional);
				$this->data['block_sponsors'] = (!empty($this->data['block_sponsors']) ? explode(',', $this->data['block_sponsors']) : []);
				$this->data['block_rooms'] = (!empty($this->data['block_rooms']) ? explode(',', $this->data['block_rooms']) : []);
			}
			// Import favorites data. This is a _good_thing_. I think.
			$this->data['favorite']=sizeof($I2_SQL->query('SELECT * FROM eighth_favorites WHERE uid=%d and aid=%d', $I2_USER->uid, $activityid)->fetch_array(MYSQLI_ASSOC))>1?TRUE:FALSE;
		}
	}

	/**
	 * Check whether an activity exists.
	 *
	 * @access public
	 * @param int $aid The activity ID.
	 * @return boolean
	 */
	public static function activity_exists($aid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT COUNT(*) FROM eighth_activities WHERE aid=%d', $aid)->fetch_single_value();
	}

	public static function add_member_to_activity($aid, User $user, $force = FALSE, $blockid = NULL) {
			  $act = new EighthActivity($aid);
			  return $act->add_member($user,$force,$blockid);
	}

	public function add_member_callin($user, $aid, $blockid) {

		global $I2_SQL,$I2_USER,$I2_LOG;
		$defaid = i2config_get('default_aid', 999, 'eighth');

		if (! $user instanceof User) {
			$userid = $user;
		} else {
			$userid = $user->uid;
		}

		$admin = Eighth::is_admin();
		$signup_admin = Eighth::is_signup_admin();
		//$sponsor = Eighth::is_sponsor($aid);

		/*Emergency comment due to is_sponsor not working
		if (!$admin && !$signup_admin && !$sponsor) {
			throw new I2Exception("Only Iodine Admins or Activity Sponsors can call a student into an activity");
		}
		*/

		$oldaid = EighthSchedule::get_activities_by_block($userid, $blockid);
		$oldactivity = new EighthActivity($oldaid, $blockid);
		if($oldactivity->sticky) {
			throw new I2Exception("Student has been stickied into an activity for this block and may not be called in");
		}

		//Postsign stuff, helps the 8th office track trends.
		if(time()>(strtotime($this->data['block']->date)+60*60*13)){
			d('old aid is '.$oldaid);
			$psq = 'INSERT INTO eighth_postsigns (cid,uid,time,fromaid,toaid,bid) VALUES (%d,%d,%s,%d,%d,%d)';
			$I2_SQL->query($psq,$I2_USER->uid,$userid,date("o-m-d H:i:s"),isset($oldaid)?$oldaid:$defaid,$this->data['aid'],$blockid);
		}
		//Now actual stuff...
		$query = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES (%d,%d,%d,0)';
		$args = array($this->data['aid'],$blockid,$userid);
		$result = $I2_SQL->query_arr($query, $args);

		//Clear their absence if they have one for the block already
		//This is needed for when a teacher marks a student absent before another teacher calls them in
		$query = 'DELETE FROM eighth_absentees WHERE bid=%d AND userid=%d';
		$args = array($blockid, $userid);
		$result = $I2_SQL->query_arr($query, $args);

	}
	public function accept_all_passes($aid, $blockid) {

		global $I2_SQL,$I2_USER,$I2_LOG;
		$admin = Eighth::is_admin();

		$signup_admin = Eighth::is_signup_admin();

		if(!$admin && !$signup_admin) {
			throw new I2Exception("Only Iodine Admins can accept all passes");
		}

		$query = 'UPDATE eighth_activity_map SET pass=0 WHERE aid=%d AND bid=%d';
		$args = array($aid, $blockid);
		$result = $I2_SQL->query_arr($query, $args);
	}

	/**
	* Adds a member to the activity.
	*
	* @access public
	* @param int $user The student's user object.
	* @param boolean $force Force the change.
	* @param int $blockid The block ID to add them to.
	*/
	public function add_member($user, $force = FALSE, $blockid = NULL) {
		global $I2_SQL,$I2_USER,$I2_LOG;
		$defaid = i2config_get('default_aid', 999, 'eighth');

		//Assume that we have an iodine uid number
		if (! $user instanceof User) {
			$userid = $user;
		} else {
			$userid = $user->uid;
		}
		$admin = Eighth::is_admin();
		$signup_admin = Eighth::is_signup_admin();
		/*
		** Users need to be able to add themselves to an activity
		*/
		if ($I2_USER->uid != $userid && !$admin && !$signup_admin) {
			throw new I2Exception("You may not change other students' activities!");
		}
		if ($force) {
			Eighth::check_admin(); //Exits on failure
		}
		if($blockid == NULL) {
			$block = $this->data['block'];
			$blockid = $this->data['bid'];
		} else {
			$block = new EighthBlock($blockid);
		}

		$ret = 0;
		$capacity = $this->__get('capacity');
		//Check the capacity. The default activity has unlimited capacity for a special reason.
		if($capacity != -1 && $this->__get('member_count') >= $capacity && $this->data['aid']!=$defaid) {
			$ret |= EighthActivity::CAPACITY;
		}
		if($this->cancelled) {
			$ret |= EighthActivity::CANCELLED;
		}
		if($this->data['restricted'] && !in_array($userid, $this->get_restricted_members())) {
			$ret |= EighthActivity::PERMISSIONS;
		}
		if ($this->presign && $block && time() < strtotime($block->date)-60*60*24*2) {
			$ret |= EighthActivity::PRESIGN;
		}
		if (time() > strtotime($block->date)+60*60*24) {
			$ret |= EighthActivity::PAST;
		}
		if ($block->locked) {
			$ret |= EighthActivity::LOCKED;
		}
		if (!empty($block->restrictionlists)) {
			foreach ($block->restrictionlists as $restrctionentry) {
				if(in_array($this->data['aid'],$restrictionentry['aidlist'])) {
					if($user->is_group_member($restrictionentry['gid'])) {
						$ret = $ret & (!EighthActivity::RESTRICTLIST);
						//See below comment as to why we need this.
						break;
					} else {
						$ret |= EighthActivity::RESTRICTLIST;
						//Don't break, because if the activity is allowed by one group
						//in which the user has membership, they should be allowed to
						//join it.
					}
				}
			}
		}

		$oldaid = EighthSchedule::get_activities_by_block($userid, $blockid);
		if ($oldaid == $this->data['aid']) {
			// The user is already in this activcalendarreturn;
		}
		if (self::activity_exists($oldaid)) {
			try {
				$oldact = new EighthActivity($oldaid, $blockid);
			} catch (I2Exception $e) {
				warn($e);
				$oldact = FALSE;
			}
		} else {
			$oldact = FALSE;
		}

		if ($oldact && $oldact->sticky) {
			$ret |= EighthActivity::STICKY;
		}

		$otheract = FALSE;
		$dayacts = EighthSchedule::get_activities($userid, $block->date, 1);
		foreach ($dayacts as $act) {
			// find one that's not this block
			if ($act[1] != $blockid) {
				try {
					$otheract = new EighthActivity($act[0], $act[1]); //It should recurse, so we don't need to do this again.
					break;
				} catch (I2Exception $e) {
					warn($e);
					$otheract = null; //DO NOT SIGN THEM UP FOR THE OTHER BLOCK IF YOU HAVE AN ERROR CREATING IT.
							  //Should fix a small problem with both block stuff.
				}
			}
		}
		if ($otheract && $otheract->aid == $this->data['aid'] && $this->oneaday) {
			$ret |= EighthActivity::ONEADAY;
		}
		$signup_bothblocks = 0;
		if ($otheract && $this->bothblocks && $otheract->aid != $this->data['aid']) {
			// just flag it, we'll sign them up later
			$signup_bothblocks = 1;
		}
		else if ($oldact && $otheract && $oldact->bothblocks && $otheract->bothblocks) {
			// have to take them out of the other block
			$signup_bothblocks = -1;
		}
		if ($signup_bothblocks != 0 && $otheract->sticky) { // Fix a loophole where the both blocks system circumvents the
			$ret |= EighthActivity::STICKY;
		}

		$query_excludes = $I2_SQL->query('SELECT * FROM eighth_excludes WHERE bid = %d',$blockid)->fetch_all_arrays(Result::ASSOC);	// mutually exclusive blocks
		$excludes = [];
		foreach($query_excludes as $r) {
			$exclude_bid=$r['target_bid'];
			$exclude_aid=EighthSchedule::get_activities_by_block($userid,$exclude_bid);
			if(self::activity_exists($exclude_aid)) {
				try {
					$excludes[] = array("activity"=>(new EighthActivity($exclude_aid,$exclude_bid)), "aid"=>($r['aid']?$r['aid']:-3));
				} catch (I2Exception $e) {
					warn($e);
				}
			}
		}

		foreach($excludes as $exclude) {		// can't use mutex blocks to get out of a sticky
			if($exclude['activity']->sticky && !$force) {
				$ret |= EighthActivity::STICKY;
			}
		}

		if (!$ret || $force) {
			//Postsign stuff, helpw the 8th office track trends.
			if(time()>strtotime($block->date)+60*60*13){
				d('old aid is '.$oldaid);
				$psq = 'INSERT INTO eighth_postsigns (cid,uid,time,fromaid,toaid,bid) VALUES (%d,%d,%s,%d,%d,%d)';
				$I2_SQL->query($psq,$I2_USER->uid,$userid,date("o-m-d H:i:s"),isset($oldaid)?$oldaid:$defaid,$this->data['aid'],$blockid);
			}
			//Now actual stuff...
			if ($block->locked) {
				$query = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES (%d,%d,%d,1)';
			} else {

				$query = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES (%d,%d,%d,0)';
			}
			$args = array($this->data['aid'],$blockid,$userid);
			$result = $I2_SQL->query_arr($query, $args);

			// now deal with bothblocks
			if ($signup_bothblocks == 1) {
				$this->add_member($userid, $force, $otheract->bid);
			}
			else if ($signup_bothblocks == -1) {
				$defact = new EighthActivity($defaid, $otheract->bid);
				$defact->add_member($userid, $force);
				//EighthActivity::add_member_to_activity($defaid, $user, $force, $otheract->bid);
			}

			foreach($excludes as $exclude) {
				$spid=new EighthActivity($exclude['aid'],$exclude['activity']->blockid);
				$spid->add_member($userid,1);
			}

			if (!$oldaid) {
				$inverse = 'DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d AND userid=%d';
				$invargs = $args;
			} else {
				$inverse = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES(%d,%d,%d,0)';
				$invargs = array($oldaid,$blockid,$userid);
			}
			//$I2_LOG->log_file('Changing activity');
			Eighth::push_undoable($query,$args,$inverse,$invargs,'User Schedule Change');
			if(mysql_error()) {
				$ret = -1;
			}
			if (isset($this->data['member_count'])) {
				$this->data['member_count']++;
			}
		}

		if($force && $ret != -1) {
			return 0;
		}
		return $ret;
	}

	public function num_members() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT COUNT(bid) FROM eighth_activity_map WHERE aid=%d AND bid=%d AND pass=0',$this->data['aid'],$this->data['bid'])->fetch_single_value();
	}

	public static function add_members_to_activity($aid,$userids,$force = FALSE, $blockid = NULL) {
			  $act = new EighthActivity($aid);
			  return $act->add_members($userids,$force,$blockid);
	}

	/**
	* Add multiple members to the activity.
	*
	* @access private
	* @param array $userids The students' user IDs.
	* @param int $blockid The block ID to add them to.
	*/
	public function add_members($userids, $force = FALSE, $blockid = NULL) {
		//Eighth::start_undo_transaction();
		foreach($userids as $userid) {
			$ret = $this->add_member(new User($userid), $force, $blockid);
			if ($ret) {
				warn("Could not add user #$userid to activity #{$this->data['aid']}");
			}
		}
		//Eighth::end_undo_transaction();
	}

	public static function remove_member_from_activity($aid, User $user, $blockid = NULL) {
			  $act = new EighthActivity($aid);
			  return $act->remove_member($user,$blockid);
	}

	/**
	* Removes a member from the activity.
	*
	* @access public
	* @param int $userid The student's user object.
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_member(User $user, $blockid = NULL) {
		global $I2_SQL;
		$userid = $user->uid;

		/*
		** Users need to be able to remove themselves from an activity
		*/
		if (!($I2_USER->uid == $userid || Eighth::is_admin())) {
			/*
			** Trigger an error: check_admin() WILL fail.
			*/
			Eighth::check_admin();
		}
		if($blockid == NULL) {
			$blockid = $this->data['bid'];
		}
		$was = $I2_SQL->query('SELECT NULL FROM eighth_activity_map WHERE userid=%d AND bid=%d AND aid=%d',$user->uid,$blockid,$this->data['aid']);
		if (!$was) {
				  // User isn't part of this activity
				  return;
		}
		$queryarg = array($this->data['aid'], $blockid, $userid);
		$query = 'DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d AND userid=%d';
		$invquery = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES(%d,%d,%d,0)';
		$invarg = $queryarg;
		$I2_SQL->query_arr($query, $queryarg);
		Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Remove Student From Activity');
		if (isset($this->data['member_count'])) {
				  $this->data['member_count']--;
		}
	}


	public static function remove_members_from_activity($aid, $userids, $blockid = NULL) {
			  $act = new EighthActivity($aid);
			  $act->remove_members($userids,$blockid);
	}

	/**
	* Removes multiple members from the activity.
	*
	* @access public
	* @param array $userid The students' user IDs.
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_members($userids, $blockid = NULL) {
		//Eighth::start_undo_transaction();
		foreach($userids as $userid) {
			$this->remove_member(new User($userid), $blockid);
		}
		//Eighth::end_undo_transaction();
	}

	/**
	 * Transfers all members from another activity to this one
	 *
	 * @param integer $old_aid The id of the old activity
	 * @param integer $bid The block id (optional)
	 */
	public function transfer_members($old_aid, $bid = NULL) {
		global $I2_SQL;

		if ($bid == NULL) {
			if ($this->data['bid']) {
				$bid = $this->data['bid'];
			}
			else {
				throw new I2Exception('No blockid set to transfer people!');
			}
		}

		$I2_SQL->query('UPDATE eighth_activity_map SET aid=%d WHERE aid=%d AND bid=%d', $this->data['aid'], $old_aid, $bid);
	}

	public function get_passes($blockid = NULL) {

		global $I2_SQL, $I2_USER;
		if($blockid == NULL) {
			if($this->data['bid']) {
				$blockid = $this->data['bid'];
			} else {
				return [];
			}
		}
		if(isset(self::$passcache[$this->data['aid']][$blockid]))
			return self::$passcache[$this->data['aid']][$blockid];

		$res = $I2_SQL->query('SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d AND pass=1', $blockid, $this->data['aid'])->fetch_all_arrays(Result::ASSOC);
		$tocache=[];
		foreach($res as $row) {
			$tocache[] = $row['userid'];
		}
		User::cache_users($tocache,array('nickname','mail','sn','givenname','graduationyear'));
		$ret = [];
		if($this->is_user_sponsor($I2_USER)) {
			foreach ($res as $row) {
				$ret[] = $row['userid'];
			}
		} else {
			foreach ($res as $row) {
				$user = new User($row['userid']);
				if (EighthSchedule::can_view_schedule($user)) {
					$ret[] = $user->uid;
				}
			}
		}
		self::$passcache[$this->data['aid']][$blockid] = $ret;
		return $ret;

	}

	/**
	* Gets the members of the activity.
	*
	* @access public
	* @param int $blockid The block ID to get the members for.
	*/
	public function get_members($blockid = NULL) {
		global $I2_SQL, $I2_USER;
		if($blockid == NULL) {
			if($this->data['bid']) {
					  $blockid = $this->data['bid'];
			}
			else {
				return [];
			}
		}
		if(isset(self::$membercache[$this->data['aid']][$blockid]))
			return self::$membercache[$this->data['aid']][$blockid];
		$res = $I2_SQL->query('SELECT userid FROM eighth_activity_map WHERE bid=%d AND aid=%d AND pass=0', $blockid, $this->data['aid'])->fetch_all_arrays(Result::ASSOC);
		$tocache=[];
		foreach($res as $row) {
			$tocache[]=$row['userid'];
		}
		User::cache_users($tocache,array('nickname','mail','sn','givenname','graduationyear'));
		$ret = [];
		// Only show students who want to be found, unless the person asking is the activity sponsor
		if($this->is_user_sponsor($I2_USER)) {
			foreach ($res as $row) {
				$ret[] = $row['userid'];
			}
		} else {
			foreach ($res as $row) {
				$user = new User($row['userid']);
				if (EighthSchedule::can_view_schedule($user)) {
					$ret[] = $user->uid;
				}
			}
		}

		self::$membercache[$this->data['aid']][$blockid]=$ret;
		return $ret;
	}

	public static function remove_all_from_activity($aid, $blockid = NULL) {
		$act = new EighthActivity($aid);
		$act->remove_all($blockid);
	}

	/**
	* Checks if the user is a sponsor for the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	*/
	public function is_user_sponsor($user) {
		global $I2_SQL;
		if($user instanceOf User) {
			$user=$user->iodineUIDNumber;
		}
		if(!(is_int($user)||(is_string($user)&&ctype_digit($user)))) {
			throw new I2Exception("Tried to check user id for something invalid! Value was $user");
		}
		$hosts = $I2_SQL->query("SELECT sid FROM eighth_sponsors WHERE userid=%d",$user)->fetch_col('sid');
		return count(array_intersect($hosts,$this->data['sponsors']))>0||count(array_intersect($hosts,$this->data['block_sponsors']))>0; // fancy obscure functions save you speed and help remove slow loops!
	}
	/**
	* Removes all members from the activity.
	*
	* @access public
	* @param int $blockid The block ID to remove them from.
	*/
	public function remove_all($blockid = NULL) {
		global $I2_SQL;
		Eighth::check_admin();
		if($blockid == NULL) {
			$blockid = $this->data['bid'];
		}
		$result = $I2_SQL->query('SELECT userid FROM eighth_activity_map WHERE aid=%d AND bid=%d', $this->data['aid'], $blockid);
		//Eighth::start_undo_transaction();
		$query = 'DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d';
		$undoquery = 'DELETE FROM eighth_activity_map WHERE aid=%d AND bid=%d AND userid=%d';
		$queryarg = array($this->data['aid'], $blockid);
		$invquery = 'REPLACE INTO eighth_activity_map (aid,bid,userid,pass) VALUES(%d,%d,%d,0)';
		foreach($result->fetch_col('userid') as $userid) {
			d($userid);
			Eighth::push_undoable($undoquery,$queryarg+array($userid),$invquery,$queryarg+array($userid),'Remove All [student]');
		}
		$I2_SQL->query_arr($query, $queryarg);
		//Eighth::end_undo_transaction();
	}

	public static function add_restricted_member_to_activity($aid, User $user) {
			  $act = new EighthActivity($aid);
			  $act->add_restricted_member($user);
	}

	/**
	* Adds a member to the restricted activity.
	*
	* @access public
	* @param User $user The students's user object.
	*/
	public function add_restricted_member(User $user) {
		global $I2_SQL;
		Eighth::check_admin();
		$query = 'REPLACE INTO eighth_activity_permissions (aid,userid) VALUES (%d,%d)';
		$queryarg = array($this->data['aid'],$user->uid);
		$I2_SQL->query_arr($query, $queryarg);
		$invquery = 'DELETE FROM eighth_activity_permissions WHERE aid=%d AND userid=%d';
		Eighth::push_undoable($query,$queryarg,$invquery,$queryarg,'Add Student to Restricted Activity');
	}

	public static function add_restricted_members_to_activity($aid, $users) {
			  $act = new EighthActivity($aid);
			  $act->add_restricted_members($users);
	}

	/**
	* Adds multiple members to the restricted activity.
	*
	* @access public
	* @param array $userids The students' user objects or IDs.
	*/
	public function add_restricted_members($users) {
		//Eighth::start_undo_transaction();
		foreach($users as $user) {
			$this->add_restricted_member(new User($user));
		}
		//Eighth::end_undo_transaction();
	}

	public static function remove_restricted_member_from_activity($aid, User $user) {
			  $act = new EighthActivity($aid);
			  $act->remove_restricted_member($user);
	}

	/**
	* Removes a member from the restricted activity.
	*
	* @access public
	* @param int $user The students's user object.
	*/
	public function remove_restricted_member(User $user) {
		global $I2_SQL;
		Eighth::check_admin();
		$query = 'DELETE FROM eighth_activity_permissions WHERE aid=%d AND userid=%d';
		$queryarg = array($this->data['aid'],$user->uid);
		$I2_SQL->query_arr($query,$queryarg);
		$invquery = 'INSERT INTO eighth_activity_permissions (aid,userid) VALUES(%d,%d)';
		Eighth::push_undoable($query,$queryarg,$invquery,$queryarg,'Remove Student from Restricted Activity');
	}

	public static function remove_restricted_members_from_activity($aid, $users) {
			  $act = new EighthActivity($aid);
			  $act->remove_restricted_members($users);
	}

	/**
	* Removes multiple members from the restricted activity.
	*
	* @access public
	* @param array $users The students' user objects.
	*/
	public function remove_restricted_members($users) {
		//Eighth::start_undo_transaction();
		foreach($users as $user) {
			$this->remove_restricted_member(new User($user));
		}
		//Eighth::end_undo_transaction();
	}

	public static function remove_restricted_all_from_activity($aid) {
		$act = new EighthActivity($aid);
		$act->remove_restricted_all();
	}

	/**
	* Removes all members from a restricted activity.
	*
	* @access public
	*/
	public function remove_restricted_all() {
		global $I2_SQL;
		Eighth::check_admin();
		//Eighth::start_undo_transaction();
		$old = $I2_SQL->query('SELECT userid FROM eighth_activity_permissions WHERE aid=%d',$this->data['aid']);
		$result = $I2_SQL->query('DELETE FROM eighth_activity_permissions WHERE aid=%d', $this->data['aid']);
		// The query to delete a student from an activity
		$userquery = 'DELETE FROM eighth_activity_permissions WHERE aid=%d AND userid=%d';
		$undouserquery = 'REPLACE INTO eighth_activity_permissions (aid,userid) VALUES(%d,%d)';
		foreach($old->fetch_all_single_values() as $userid) {
			$undoargs = array($this->data['aid'],$userid);
			Eighth::push_undoable($userquery,$undoargs,$undouserquery,$undoargs,'Remove All From Restricted Activity');
		}
		//Eighth::end_undo_transaction();
	}

	/**
	* Gets the members of the restricted activity.
	*
	* @access public
	*/
	public function get_restricted_members() {
		global $I2_SQL;
		return flatten($I2_SQL->query('SELECT userid FROM eighth_activity_permissions WHERE aid=%d', $this->data['aid'])->fetch_all_arrays(Result::NUM));
	}

	/**
	* Checks to see if a user is a member of a restricted activity.
	*
	* @access public
	*/
	public function check_restricted_member($userid = NULL) {
		global $I2_SQL, $I2_USER;

		if($userid === NULL ) {
			$userid = $I2_USER->uid;
		}


		if (!isset(self::$permissionscache[$userid])) {
			$r = $I2_SQL->query('SELECT aid FROM eighth_activity_permissions WHERE userid=%d', $userid)->fetch_all_arrays(Result::NUM);
			$r = array_map("intval", flatten($r));
			self::$permissionscache[$userid] = $r;
		}

		return in_array($this->data['aid'], self::$permissionscache[$userid]);
	}

	/**
	* Adds a sponsor to the activity.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public function add_sponsor($sponsorid) {
		Eighth::check_admin();
		if(!in_array($sponsorid, $this->data['sponsors'])) {
			$this->data['sponsors'][] = $sponsorid;
			$this->__set('sponsors', $this->data['sponsors']);
		}
	}

	/**
	* Removes a sponsor from the activity.
	*
	* @access public
	* @param int $sponsorid The sponsor ID.
	*/
	public function remove_sponsor($sponsorid) {
		Eighth::check_admin();
		if(in_array($sponsorid, $this->data['sponsors'])) {
			unset($this->data['sponsors'][array_search($sponsorid, $this->data['sponsors'])]);
			$this->__set('sponsors', $this->data['sponsors']);
		}
	}

	/**
	* Adds a room to the activity.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function add_room($roomid) {
		Eighth::check_admin();
		if(!in_array($roomid, $this->data['rooms'])) {
			$this->data['rooms'][] = $roomid;
			$this->__set('rooms', $this->data['rooms']);
		}
	}

	/**
	* Removes a room from the activity.
	*
	* @access public
	* @param int $roomid The room ID.
	*/
	public function remove_room($roomid) {
		Eighth::check_admin();
		if(in_array($roomid, $this->data['rooms'])) {
				  unset($this->data['rooms'][array_search($roomid, $this->data['rooms'])]);
			$this->__set('rooms', $this->data['rooms']);
		}
	}

	/**
	* Gets all the available activities.
	*
	* @access public
	* @param int $blockid The block ID.
	*/
	public static function get_all_activities($blockids = NULL, $restricted = FALSE) {
		global $I2_SQL;
		if($blockids == NULL) {
			$activitydata = $I2_SQL->query('SELECT eighth_activities.* FROM eighth_activities ' . ($restricted ? 'WHERE restricted=1 ' : ''))->fetch_all_arrays(Result::ASSOC);
			$activities = array();
			foreach($activitydata as $ad) {
				$activities[] = new EighthActivity($ad['aid'], NULL, "MASSLOAD", $ad);
			}
			usort($activities,'EighthActivity::activity_compare');
			return $activities;
		}
		else {
			if(!is_array($blockids)) {
				settype($blockids, 'array');
			}
			//FIXME: Once sponsor and rooms have been removed from eighth_activities
			//The list of eighth_activity columns can be replaced with eighth_activities.* in the queries below
			//Once the capacity column has been removed from eighth_block_map, the list of eighth_block_map columns
			//can be replaced with eighth_block_map.* in the queries below.
			//FIXME: long-term this should be cached instead of found and merged
			if(count($blockids) == 1) {
				$activitydata = $I2_SQL->query('SELECT eighth_activities.aid,name,description,restricted,presign,oneaday,bothblocks,sticky,special,calendar,eighth_block_map.bid,eighth_block_map.sponsors AS block_sponsors,eighth_block_map.rooms AS block_rooms,attendancetaken,cancelled,roomchanged,comment,advertisement,COUNT(eighth_activity_map.userid) AS member_count FROM eighth_activities LEFT JOIN eighth_block_map ON (eighth_activities.aid=eighth_block_map.activityid) LEFT JOIN eighth_activity_map ON (eighth_activities.aid=eighth_activity_map.aid AND eighth_block_map.bid=eighth_activity_map.bid) WHERE eighth_block_map.bid=%D ' . ($restricted ? 'AND restricted=1 ' : '') . 'GROUP BY aid ORDER BY special DESC', $blockids)->fetch_all_arrays(Result::ASSOC);
			} else {
				$activitydata = $I2_SQL->query('SELECT aid,name,description,restricted,presign,oneaday,bothblocks,sticky,special,calendar,eighth_block_map.bid,eighth_block_map.sponsors AS block_sponsors,eighth_block_map.rooms AS block_rooms,attendancetaken,cancelled,roomchanged,comment,advertisement FROM eighth_activities LEFT JOIN eighth_block_map ON (eighth_activities.aid=eighth_block_map.activityid) WHERE bid IN (%D) ' . ($restricted ? 'AND restricted=1 ' : '') . 'GROUP BY aid ORDER BY special DESC', $blockids)->fetch_all_arrays(Result::ASSOC);
			}

			//FIXME: this should be cached instead of being found and merged
			$roomdata = $I2_SQL->query('SELECT * FROM eighth_rooms')->fetch_all_arrays(Result::ASSOC);
			$rooms = array();
			foreach($roomdata as $rd) {
				$rid = $rd['rid'];
				$rooms[$rid] = $rd;
			}

			$activities = array();
			foreach($activitydata as $ad) {
				$roomnames = array();
				$roomids = explode(',', $ad['block_rooms']);
				$capacity = 0;
				if($ad['cancelled'] || $ad['block_rooms'] == '') {
					$ad['block_rooms_comma'] = '';
					$ad['capacity'] = -1;
				} else {
					foreach($roomids as $rid) {
						$roomnames[] = $rooms[$rid]['name'];
						$capacity += $rooms[$rid]['capacity'];
					}
					$ad['block_rooms_comma'] = implode(',', $roomnames);
					$ad['capacity'] = $capacity;
				}
				$activities[] = new EighthActivity($ad['aid'], NULL, "MASSLOAD", $ad);
			}
			usort($activities,'EighthActivity::activity_compare');
			return $activities;
		}
	}

	/**
	* Helper function to sort eighth period activities.
	* @access public
	* @param EighthActivity $a The first activity to compare.
	* @param EighthActivity $b The second activity to compare.
	*/
	public static function activity_compare($a,$b) {
		if($a->data['special'] != $b->data['special']) {
			return $a->data['special'] < $b->data['special'];
		}
		return strnatcmp($a->data['name'],$b->data['name']);
	}

	/**
	* Helper function to sort eighth period activities better, as this one also uses the favorites correctly.
	* @access public
	* @param EighthActivity $a The first activity to compare.
	* @param EighthActivity $b The second activity to compare.
	*/
	public static function activity_favorite_compare($a,$b) {
		if($a->data['favorite'] && !$b->data['favorite'])
			return -1;
		elseif(!$a->data['favorite'] && $b->data['favorite'])
			return 1;
		if($a->data['special'] != $b->data['special']) {
			return $a->data['special'] < $b->data['special'];
		}
		return strnatcmp($a->data['name'],$b->data['name']);
	}

	/**
	* Change the favoritism status of a club.
	* @access public
	* @param int $aid The activity ID.
	*/
	public static function favorite_change($aid) {
		global $I2_USER,$I2_SQL;
		if(sizeof($I2_SQL->query('SELECT * FROM eighth_favorites WHERE uid=%d and aid=%d', $I2_USER->uid, $aid)->fetch_array(MYSQLI_ASSOC))>1)
			$I2_SQL->query('DELETE FROM eighth_favorites WHERE uid=%d and aid=%d', $I2_USER->uid, $aid);
		else
			$I2_SQL->query('INSERT INTO eighth_favorites (uid,aid) VALUES (%d,%d)', $I2_USER->uid, $aid);
	}

	/**
	* Gets all activities on or after a given date
	*
	* @param string $startdate A date before which no activities will be returned
	* @return array An array of EighthActivity objects representing activities
	*/
	public static function get_all_activities_starting($startdate = NULL) {
		global $I2_SQL;
		return self::id_to_activity($I2_SQL->query('SELECT activityid,eighth_blocks.bid FROM eighth_blocks LEFT JOIN eighth_block_map ON (eighth_block_map.bid = eighth_blocks.bid) LEFT JOIN eighth_activities ON (eighth_activities.aid=eighth_block_map.activityid) WHERE date > %t GROUP BY activityid ORDER BY special DESC, name',$startdate)->fetch_all_arrays(Result::NUM));
	}

	/**
	* Gets all the blocks in which an activity is scheduled
	*/
	public function get_all_blocks($start_date = NULL) {
		global $I2_SQL;
		$activity = new EighthActivity($this->data['aid']);
		if($start_date === NULL) {
			$start_date = Eighth::$default_start_date;
		}

		return EighthActivity::id_to_activity($I2_SQL->query('SELECT activityid,eighth_blocks.bid FROM eighth_blocks LEFT JOIN eighth_block_map ON (eighth_block_map.bid = eighth_blocks.bid) LEFT JOIN eighth_activities ON (eighth_activities.aid=eighth_block_map.activityid) WHERE activityid=%d AND date >= %t ORDER BY date',$this->data['aid'], $start_date)->fetch_all_arrays(Result::NUM));
	}

	/**
	 * Gets the activity IDs of any activities that have been deleted.
	 *
	 * @access public
	 * @return array An array of all deleted aids.
	 */
	public static function get_unused_aids() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT aid FROM eighth_activity_id_holes')->fetch_col('aid');
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
	public static function add_activity($name, $sponsors = [], $rooms = [], $description = '',
			$restricted = FALSE, $sticky = FALSE, $bothblocks = FALSE, $presign = FALSE, $aid = NULL, $special = FALSE) {
		Eighth::check_admin();
		global $I2_SQL;
		if(!is_array($sponsors)) {
			$sponsors = array($sponsors);
		}
		if(!is_array($rooms)) {
			$rooms = array($rooms);
		}
		$result = NULL;
		if ($aid === NULL) {
			$query = "REPLACE INTO eighth_activities (name,sponsors,rooms,description,restricted,sticky,bothblocks,presign,special)
				VALUES (%s,'%D','%D',%s,%d,%d,%d,%d,%d)";
			$queryarg = array($name, $sponsors, $rooms, $description, ($restricted?1:0),($sticky?1:0),($bothblocks?1:0),($presign?1:0),($special?1:0));
			$result = $I2_SQL->query_arr($query,$queryarg);
			$invquery = 'DELETE FROM eighth_activities WHERE aid=%d';
			$newid = $result->get_insert_id();
			$invarg = array($newid);
			Eighth::push_undoable($query,$queryarg,$invquery,$invarg,'Create Activity');
			return $result->get_insert_id();
		} else {
			$old = $I2_SQL->query('SELECT * FROM eighth_activities WHERE aid=%d',$aid)->fetch_array(Result::ASSOC);
			$query = "REPLACE INTO eighth_activities
				(name,sponsors,rooms,description,restricted,sticky,bothblocks,presign,aid,special)
				VALUES (%s,'%D','%D',%s,%d,%d,%d,%d,%d,%d)";
			$queryarg = array($name, $sponsors, $rooms, $description, ($restricted?1:0),($sticky?1:0),($bothblocks?1:0),($presign?1:0),$aid,($special?1:0));
			$result = $I2_SQL->query_arr($query,$queryarg);
			$I2_SQL->query('DELETE FROM eighth_activity_id_holes WHERE aid=%d', $aid);
			$invarg = array($old['name'],$old['sponsors'],$old['rooms'],$old['description'],$old['restricted'],$old['sticky'],$old['bothblocks'],$old['presign'],$old['aid'],$old['special']);
			Eighth::push_undoable($query,$queryarg,$query,$invarg);
			return $aid;
		}
	}

	/**
	* Removes an activity from the list.
	*
	* @access public
	* @param int $activityid The activity ID.
	*/
	public static function remove_activity($activityid) {
		global $I2_SQL;
		Eighth::check_admin();
		$old = $I2_SQL->query('SELECT * FROM eighth_activities WHERE aid=%d', $activityid)->fetch_array(Result::ASSOC);
		$query = 'DELETE FROM eighth_activities WHERE aid=%d';
		$queryarg = array($activityid);
		$I2_SQL->query_arr($query,$queryarg);
		$wasscheduled = $I2_SQL->query('SELECT bid FROM eighth_block_map WHERE activityid=%d',$activityid)->fetch_col('bid');
		$I2_SQL->query('DELETE FROM eighth_block_map WHERE activityid=%d', $activityid);
		$I2_SQL->query('INSERT INTO eighth_activity_id_holes SET aid=%d', $activityid);
		$people = $I2_SQL->query('SELECT bid,userid FROM eighth_activity_map WHERE aid=%d',$activityid);
		$defaid = i2config_get('default_aid',999,'eighth');
		/*
		** Move all affected students into the default activity
		*/
		while ($row = $people->fetch_array(Result::ASSOC)) {
			$query = 'REPLACE INTO eighth_activity_map (aid,bid,userid) VALUES(%d,%d,%d)';
			$queryarg = array($defaid,$row['bid'],$row['userid']);
			$I2_SQL->query_arr($query,$queryarg);
		}
		/*
		** Remove from the calendar.
		*/
		foreach($wasscheduled as $bid) {
			Calendar::remove_event('eighthspecial_'.$bid.'_'.$activityid);
		}
	}

	/**
	* Removes this activity from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_activity($this->data['aid']);
		$this->data = [];
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		global $I2_SQL, $I2_USER;
		if(array_key_exists($name, $this->data)) {
			if($name == 'restricted') {
				return ($this->data['restricted'] && !$this->check_restricted_member());
			}
			return $this->data[$name];
		}
		else if($name == 'members' && $this->data['bid']) {
			return $this->get_members();
		}
		else if($name == 'members_obj' && $this->data['bid']) {
			return User::sort_users($this->get_members());
		}
		else if($name == 'passes' && $this->data['bid']) {
			return $this->get_passes();
		}
		else if($name == 'passes_obj' && $this->data['bid']) {
			return User::sort_users($this->get_passes());
		}
		else if($name == 'absentees' && $this->data['bid']) {
			return EighthSchedule::get_absentees($this->data['bid'], $this->data['aid']);
		}
		switch($name) {
			case 'comment_short':
				if(isset($this->data['comment'])) {
					return substr($this->data['comment'], 0, 15) . (strlen($this->data['comment']) > 15 ? '...' : '');
				}
				return '';
			case 'comment_notsoshort':
				if(isset($this->data['comment'])) {
					return substr($this->data['comment'], 0, 20) . (strlen($this->data['comment']) > 20 ? '...' : '');
				}
				return '';
			case 'name_r':
				return ($this->data['special'] ? 'SPECIAL: ' : '') . $this->data['name'] . ($this->__get('restricted') ? ' (R)' : '') . ($this->data['bothblocks'] ? ' (BB)' : '') . ($this->data['sticky'] ? ' (S)' : '');
			case 'name_full_r':
				$namelen = strlen($this->data['name']);
				// Make it so that all names w/comments are 50ish characters or less w/o truncating the name itself
				if ($namelen >= 70) {
					return $this->data['name'];
				}
				if (isset($this->data['comment'])) {
					$comment = $this->data['comment'];
					$commentlen = strlen($comment);
				} else {
					$commentlen = 0;
					$comment = '';
				}
				return ((isset($this->data['special']) && $this->data['special']) ? 'SPECIAL: ' : '') . $this->data['name'] . ($commentlen ? ' - ' . substr($comment,0,70-$namelen).(70-$namelen<$commentlen?'...':'') : '') . ($this->__get('restricted') ? ' (R)' : '') . ($this->data['bothblocks'] ? ' (BB)' : '') . ($this->data['sticky'] ? ' (S)' : '');
			case 'name_comment_r':
				if (isset($this->data['comment'])) {
					$comment = $this->data['comment'];
				} else {
					$comment = '';
				}
				return ($this->data['special'] ? 'SPECIAL: ' : '') . $this->data['name'] . ($comment ? ' - ' . $comment : '') . ($this->__get('restricted') ? ' (R)' : '') . ($this->data['bothblocks'] ? ' (BB)' : '') . ($this->data['sticky'] ? ' (S)' : '');
			case 'name_friendly':
				$comment = $this->__get('comment_short');
				if (!$comment) {
					return $this->data['name'];
				}
				return $this->data['name'].' - '.$comment;
			case 'sponsors_comma':
				$sponsors = EighthSponsor::id_to_sponsor($this->data['sponsors']);
				$temp_sponsors = [];
				foreach($sponsors as $sponsor) {
					$temp_sponsors[] = $sponsor->name;
				}
				return implode(', ', $temp_sponsors);
			case 'sponsors_lname_comma':
				$sponsors = EighthSponsor::id_to_sponsor($this->data['sponsors']);
				$temp_sponsors = [];
				foreach($sponsors as $sponsor) {
					$temp_sponsors[] = $sponsor->lname;
				}
				return implode(', ', $temp_sponsors);

			case 'block_sponsors_comma':
				if($this->data['cancelled']) {
					return 'CANCELLED';
				}
				$sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
				$temp_sponsors = [];
				foreach($sponsors as $sponsor) {
					$temp_sponsors[] = $sponsor->name_comma;
				}
				return implode(', ', $temp_sponsors);
			case 'block_sponsors_comma_short':
				if($this->data['cancelled']) {
					return 'CANCELLED';
				}
				$sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
				$temp_sponsors = [];
				foreach($sponsors as $sponsor) {
					$temp_sponsors[] =  $sponsor->lname . ($sponsor->fname ? ', ' . substr($sponsor->fname, 0, 1) . '.' : '');
				}
				return implode(', ', $temp_sponsors);
			case 'sponsors_obj':
				return $sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
			case 'pickups_comma':
				$sponsors = EighthSponsor::id_to_sponsor($this->data['block_sponsors']);
				$temp_pickups = [];
				foreach($sponsors as $sponsor) {
					$temp_pickups[] = $sponsor->pickup;
				}
				return implode(', ', array_unique($temp_pickups));
			case 'rooms_comma':
				$rooms = EighthRoom::id_to_room($this->data['rooms']);
				$temp_rooms = [];
				foreach($rooms as $room) {
					$temp_rooms[] = $room->name;
				}
				return implode(', ', $temp_rooms);
			case 'block_rooms_comma':
				if($this->data['cancelled']) {
					return '';
				}
				$rooms = EighthRoom::id_to_room($this->data['block_rooms']);
				$temp_rooms = [];
				foreach($rooms as $room) {
					$temp_rooms[] = $room->name;
				}
				return implode(', ', $temp_rooms);
			case 'restricted_members':
				return $this->get_restricted_members();
			case 'restricted_members_obj':
				return User::sort_users($this->get_restricted_members());
			case 'restricted_members_obj_sorted':
				$members = $this->__get('restricted_members_obj');
				usort($members,array($this,'sort_by_name'));
				return $members;
			case 'capacity':
				if (!isset($this->data['block_rooms']) || count($this->data['block_rooms']) == 0) {
					return -1;
				}
				$this->data['capacity'] = $I2_SQL->query('SELECT SUM(capacity) FROM eighth_rooms WHERE rid IN (%D)',
						  $this->data['block_rooms'])->fetch_single_value();
				return $this->data['capacity'];
		 	case 'member_count':
				$this->data['member_count'] = $I2_SQL->query('SELECT COUNT(userid) FROM eighth_activity_map WHERE bid=%d AND aid=%d',$this->data['bid'],$this->data['aid'])->fetch_single_value();
				return $this->data['member_count'];
			case 'percent_full':
				return (100*$this->__get('member_count'))/($this->__get('capacity'));
		}
	}

	/**
	* get private data.
	* only for use by the api.
	*
	* @access public
	*/
	public function get_data() {
		return $this->data;
	}

	public function sort_by_name($one,$two) {
		return strcasecmp($one->name_comma,$two->name_comma);
	}

	/**
	* The magic __set function.
	*
	* @access public
	* @param string $name The name of the field to set.
	* @param mixed $value The value to assign to the field.
	*/
	public function __set($name, $value) {
		global $I2_SQL,$I2_USER;
		if(!(Eighth::is_admin() || ($this->is_user_sponsor($I2_USER) && $name='attendancetaken')))
			throw new I2Exception('Attempted to perform an unauthorized 8th-period action!');
		if($name == 'name') {
			$oldname = $I2_SQL->query('SELECT name FROM eighth_activities WHERE aid=%d',$this->data['aid'])->fetch_single_value();
			$query = 'UPDATE eighth_activities SET name=%s WHERE aid=%d';
			$queryarg = array($value, $this->data['aid']);
			$result = $I2_SQL->query_arr($query,$queryarg);
			$invarg = array($oldname, $this->data['aid']);
			Eighth::push_undoable($query,$queryarg,$query,$invarg,'Change Activity Name');
			$this->data['name'] = $value;
		}
		else {
			switch($name) {
				case 'rooms':
					unset($this->data['capacity']);
					// No break here on purpose
				case 'sponsors':
					if(!is_array($value)) {
						$value = array($value);
					}
					$oldval = $I2_SQL->query("SELECT $name FROM eighth_activities WHERE aid=%d",$this->data['aid'])->fetch_single_value();
					$query = "UPDATE eighth_activities SET $name='%D' WHERE aid=%d";
					$queryarg = array($value, $this->data['aid']);
					$result = $I2_SQL->query_arr($query,$queryarg);
					$this->data[$name] = $value;
					$invarg = array($oldval,$this->data['aid']);
					$name = ucFirst($name);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,"Change Activity $name");
					return;
				case 'description':
					$oldval = $I2_SQL->query("SELECT $name FROM eighth_activities WHERE aid=%d",$this->data['aid'])->fetch_single_value();
					$query = 'UPDATE eighth_activities SET description=%s WHERE aid=%d';
					$queryarg = array($value, $this->data['aid']);
					$result = $I2_SQL->query_arr($query,$queryarg);
					$this->data['description'] = $value;
					$invarg = array($oldval,$this->data['aid']);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,'Change Activity Description');
					return;
				case 'restricted':
				case 'oneaday':
				case 'bothblocks':
				case 'sticky':
				case 'special':
				case 'calendar':
				case 'presign':
					if ($this->data[$name] == $value) {
						//Nothing to do
						return;
					}
					$query = "UPDATE eighth_activities SET $name=%d WHERE aid=%d";
					$queryarg = array((int)$value, $this->data['aid']);
					$result = $I2_SQL->query_arr($query,$queryarg);
					$this->data[$name] = $value;
					// Invert value - we checked for equality earlier
					$invarg = array($value?0:1,$this->data['aid']);
					$name = ucFirst($name);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,"Change $name Status");
					return;
				case 'block_comment':
				case 'block_comments':
					return $this->block->comments;
				case 'comment':
				case 'advertisement':
					$oldval = $I2_SQL->query("SELECT $name FROM eighth_block_map WHERE bid=%d AND activityid=%d",$this->data['bid'],$this->data['aid'])->fetch_single_value();
					$query = "UPDATE eighth_block_map SET $name=%s WHERE bid=%d AND activityid=%d";
					$queryarg = array($value, $this->data['bid'], $this->data['aid']);
					$result = $I2_SQL->query_arr($query, $queryarg);
					$this->data[$name] = $value;
					$invarg = array($oldval, $this->data['bid'], $this->data['aid']);
					$name = ucFirst($name);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,"Change Activity $name");
					return;
		 		case 'attendancetaken':
				case 'roomchanged':
				case 'cancelled':
					if ($this->data[$name] == $value) {
						return;
					}
					$query = "UPDATE eighth_block_map SET $name=%d WHERE bid=%d AND activityid=%d";
					$queryarg = array((int)$value, $this->data['bid'], $this->data['aid']);
					$result = $I2_SQL->query_arr($query,$queryarg);
					$this->data[$name] = $value;
					$invarg = array($value?0:1,$this->data['bid'],$this->data['aid']);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,"Change $name Bit");
					return;
				case 'capacity':
					if ($this->data[$name] == $value) {
						return;
					}
					$query = "UPDATE eighth_block_map SET $name=%d WHERE bid=%d AND activityid=%d";
					$queryarg = array($value, $this->data['bid'], $this->data['aid']);
					$result = $I2_SQL->query_arr($query,$queryarg);
					$this->data[$name] = $value;
					$invarg = array($value,$this->data['bid'],$this->data['aid']);
					Eighth::push_undoable($query,$queryarg,$query,$invarg,"Change Activity $name");
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
	public static function id_to_activity($activityids, $exceptionsok = TRUE) {
		$ret = [];
		foreach($activityids as $activityid) {
			if(is_array($activityid)) {
				if($exceptionsok || EighthSchedule::is_activity_valid($activityid[0], $activityid[1])||$activityid[0]==-3)
					/* If this is not surrounded by a try-catch,
					 * the whole module will fail when a user is
					 * signed up for a nonexistant activity! */
					try {
						$ret[] = new EighthActivity($activityid[0], $activityid[1]);
					} catch (Exception $e) {
						// Allow the program to continue
						d("Activity with ID {$activityid[0]} does not exist for block {$activityid[1]}.", 4);
					}
				else {
					d("Activity $activityid[0] not scheduled for block $activityid[1], returning EighthActivity object with CANCELLED handler.");
					$ret[] = new EighthActivity($activityid[0], $activityid[1], "CANCELLED");
				}
			}
			else {
				$ret[] = new EighthActivity($activityid);
			}
		}
		return $ret;
	}

	public static function cancel($blockid, $activityid) {
		global $I2_SQL;
		Eighth::check_admin();
		$I2_SQL->query("UPDATE eighth_block_map SET cancelled=1 WHERE bid=%d AND activityid=%d", $blockid, $activityid);
	}

	/**
	* Returns the display class for the pretty activity list
	*
	* @access public
	* @param int $selectedaid The current AID the user has selected for this block
	*/
	public function displayClass($selectedaid) {
		if($this->cancelled)
			return "cancelledAR";

		if($selectedaid == $this->data['aid'])
			return "selectedAR";

		if($this->restricted)
			return "restrictedAR";

		if($this->capacity != -1) {
			if($this->member_count>=$this->capacity)
				return "fullAR";
			if($this->member_count>=$this->capacity*.9)
				return "fillingAR";
		}

		if($this->favorite)
			return "favoriteAR";

		return "generalAR";
	}

	/**
	* Returns the class of the piechart div for this activity
	*
	* @access public
	*/
	public function pieClass() {
		if($this->capacity!=-1 && $this->capacity>0)
			$fill = ceil(10-10*$this->member_count/$this->capacity);
		else if($this->capacity==-1)
			$fill = 10;
		else
			$fill = 0;

		if($fill<=0)	// lol oversubscribed activities...
			$fill = 0;

		if($this->restricted || $this->cancelled)
			return "crossPie pie".$fill." pieIcon";
		else
			return "pieIcon pie".$fill;
	}
}

?>
