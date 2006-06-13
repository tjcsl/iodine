<?php
/**
* Just contains the definition for the class {@link GroupSQL}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Group
* @filesource
*/

/**
* The module runs groups using MySQL as a backend.
* @package modules
* @subpackage Group
*/
class GroupSQL extends Group {

	const PERM_ADMIN = 'GROUP_ADMIN';
	const PERM_ADD = 'GROUP_ADD';
	const PERM_REMOVE = 'GROUP_REMOVE';
	const PERM_JOIN = 'GROUP_JOIN';

	/**
	* groupname to GID mapping
	*/
	private static $gid_map;

	/**
	* GID to groupname mapping
	*/
	private static $name_map;

	/**
	* Group info cache
	*/
	private $info = array();

	/**
	* The constructor.
	*
	* One of three things can be passed as an argument to __construct for GroupSQL. A GID or group name will cause Group to lookup the information in the database based on the specified info. Passing a Group object will cause this object to be a copy or reference to that group object, utilizing the same information cache.
	*
	* @param mixed $group Either a group name, a GID, or a Group object, as outlined above.
	*/
	public function __construct($group) {
		global $I2_SQL;

		// Generate the GID/name maps if they do not exist
		if (self::$gid_map === NULL) {
			 self::$gid_map = array();
			 self::$name_map = array();
			 $res = $I2_SQL->query('SELECT name,gid FROM groups_name');
			 while ($row = $res->fetch_array(Result::ASSOC)) {
			 	self::$gid_map[$row['name']] = $row['gid'];
				self::$name_map[$row['gid']] = $row['name'];
			 }
		}

		if(is_numeric($group)) {
			// Passed GID, check existence
			if(isset(self::$name_map[$group])) {
				$name = self::$name_map[$group];
				$this->info['gid'] = $group;
				$this->info['name'] = $name;
			} else {
				throw new I2Exeption("Nonexistent GID $group given to the GroupSQL constructor");
			}
		} elseif (is_object($group)) {
			// If passed a group object, make it a copy of that group
			if (!$group instanceof Group) {
				throw new I2Exception('Group construction attempted with non-Group object!');
			}
			$this->info = &$group->info;
		} elseif(isset(self::$gid_map[$group])) {
			// Passed group name, get info
			$gid = self::$gid_map[$group];
			$this->info['gid'] = $gid;
			$this->info['name'] = $group;
		} else {
			throw new I2Exception("Nonexistent group $group given to the GroupSQL constructor");
		}
	}

	/**
	* The magical PHP __get method.
	*
	* Fields supported:
	* <ul>
	* <li>gid: Returns the GID of this group.</li>
	* <li>name: Returns the group's name.</li>
	* <li>description: Returns the group's description.</li>
	* <li>members: An array of all of the members in the group. See get_static_members().</li>
	* <li>members_obj: An array of {@link User} objects for all of the members of this group.</li>
	* <li>members_obj_sorted: An alphabatized array of {@link User} objects for all of the members of this group.</li>
	* </ul>
	*/
	public function __get($var) {
		global $I2_SQL;
		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		switch($var) {
			case 'description':
				return $I2_SQL->query('SELECT description FROM groups_name WHERE gid=%d', $this->__get('gid'))->fetch_single_value();
			case 'members':
				$this->info[$var] = $this->get_static_members();
				break;
			case 'members_obj':
				return User::id_to_user($this->__get('members'));
		   	case 'members_obj_sorted':
				$members = $this->__get('members_obj');
				usort($members,array('GroupSQL','sort_by_name'));
				return $members;
		}

		if(!isset($this->info[$var])) {
			throw new I2Exception('Invalid attribute passed to GroupSQL::__get(): '.$var.', or invalid GID: '.$this->__get('gid'));
		}

		return $this->info[$var];
	}

	private static function sort_by_name($one,$two) {
			  return strcasecmp($one->name_comma,$two->name_comma);
	}

	public function get_static_members() {
		global $I2_SQL;
		return flatten($I2_SQL->query('SELECT uid FROM groups_static WHERE gid=%d',$this->gid)->fetch_all_arrays(Result::NUM));
	}

	public static function get_all_groups($module = NULL) {
		global $I2_SQL;
		$prefix = '%';
		if($module) {
			$prefix = strtolower($module) . '_%';
		}
		$ret = array();
		foreach($I2_SQL->query('SELECT gid FROM groups_name WHERE name LIKE %s ORDER BY name', $prefix) as $row) {
			$ret[] = new Group($row[0]);
		}
		return $ret;
	}

	public static function get_all_group_names() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT name FROM groups_name');
	}

	public function add_user(User $user) {
		global $I2_SQL, $I2_USER;

		if($this->has_member($user)) {
			// Meh? they're already a member
			return FALSE;
		}

		if (	// Admins can add anyone to a group
			self::admin_all()->has_member($I2_USER) ||

			// People can add themselves if they have the GroupSQL::PERM_JOIN permission
			($I2_USER->uid == $user->uid && $this->has_permission($user, self::PERM_JOIN)) ||

			// People with the GroupSQL::PERM_ADD permission can add people
			$this->has_permission($I2_USER,self::PERM_ADD)
		) {

			// Only insert member into the cache if the cache has been fetched
			// Otherwise, the member would be the _only_ member in the cache if it hadn't been fetched, giving incorrect results from __get
			if(isset($this->info['members'])) {
				$this->info['members'][] = $user->uid;
			}
			return $I2_SQL->query('INSERT INTO groups_static (gid,uid) VALUES (%d,%d)',$this->gid,$user->uid);
		} else {
			throw new I2Exception('You are not authorized to add users into this group!');
		}
	}

	public function remove_user(User $user) {
		global $I2_SQL, $I2_USER;

		if(!$this->has_member($user)) {
			// Meh? they're already not a member
			return FALSE;
		}

		if (	// Admins can remove anyone from a group
			self::admin_all()->has_member($I2_USER) ||

			// People can remove themselves if they can add themselves, as well
			($I2_USER->uid == $user->uid && $this->has_permission($user, self::PERM_JOIN)) ||

			// People with the GroupSQL::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER,self::PERM_REMOVE)
		) {

			// Delete user from member cache if the cache has been fetched
			if(isset($this->info['members']) && ($key = array_search($user->uid,$this->info['members']))) {
				unset($this->info['members'][$key]);
			}
			return $I2_SQL->query('DELETE FROM groups_static WHERE gid=%d AND uid=%d',$this->gid,$user->uid);
		} else {
			throw new I2Exception('You are not authorized to remove users from this group!');
		}
	}

	public function remove_static_members() {
		global $I2_SQL,$I2_USER;

		if (	// Admins can remove anyone from a group
			self::admin_all()->has_member($I2_USER) ||

			// People with the GroupSQL::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER,self::PERM_REMOVE)
		) {
			
			// Delete member cache if it has been fetched
			if(isset($this->info['members'])) {
				unset($this->info['members']);
			}
			return $I2_SQL->query('DELETE FROM groups_static WHERE gid=%d', $this->gid);
		} else {
			throw new I2Exception('You are not authorized to remove users from groups!');
		}

	}

	public function grant_permission($subject, $perm) {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if(	// Admins can add permissions to anyone
			!self::admin_all()->has_member($I2_USER) &&
			!$this->has_permission($I2_USER,self::PERM_ADMIN)
		) {
			throw new I2Exception('You are not authorized to grant permissions in this group!');
		}

		// Check permission validity
		if( ($pid = self::get_pid($perm)) === FALSE) {
			throw new I2Exception("Invalid permission $perm passed to ".__METHOD__);
		}

		if($subject instanceof User) {
			return $I2_SQL->query('INSERT INTO groups_user_perms (uid,gid,pid) VALUES (%d,%d,%d)',$subject->uid, $this->gid, $pid);
		} elseif ($subject instanceof Group) {
			return $I2_SQL->query('INSERT INTO groups_group_perms (usergroup,gid,pid) VALUES (%d,%d%d)',$subject->gid, $this->gid, $pid);
		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}
	}
	
	public function revoke_permission($subject, $perm) {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if(	// Admins can revoke permissions from anyone
			!self::admin_all()->has_member($I2_USER) &&
			!$this->has_permission($I2_USER,self::PERM_ADMIN)
		) {
			throw new I2Exception('You are not authorized to revoke permissions in this group!');
		}

		// Check permission validity
		if( ($pid = self::get_pid($perm)) === FALSE) {
			throw new I2Exception("Invalid permission $perm passed to ".__METHOD__);
		}

		if($subject instanceof User) {
			return $I2_SQL->query('DELETE FROM groups_user_perms WHERE uid=%d AND gid=%d AND pid=%d', $subject->uid, $this->gid, $pid);
		} elseif ($subject instanceof Group) {
			return $I2_SQL->query('DELETE FROM groups_group_perms WHERE usergroup=%d AND gid=%d AND pid=%d', $subject->gid, $this->gid, $pid);
		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}
	}

	public function get_permissions($subject) {
		global $I2_SQL;

		if($subject instanceof User) {
			return flatten($I2_SQL->query('SELECT pid FROM groups_user_perms WHERE uid=%d AND gid=%d', $subject->uid, $this->gid)->fetch_all_arrays(Result::NUM));
		} elseif($subject instanceof Group) {
			return flatten($I2_SQL->query('SELECT pid FROM groups_group_perms WHERE usergroup=%d AND gid=%d', $subject->gid, $this->gid)->fetch_all_arrays(Result::NUM));
		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}
	}

	public function has_permission($subject, $perm) {
		global $I2_SQL;

		// Check permission validity
		if( ($pid = self::get_pid($perm)) === FALSE) {
			throw new I2Exception("Invalid permission $perm passed to ".__METHOD__);
		}

		if($subject instanceof User) {
			// admin_all has all permissions
			if (Group::admin_all()->has_member($subject)) {
				return TRUE;
			}
			$res = $I2_SQL->query('SELECT count(*) FROM groups_user_perms WHERE uid=%d AND gid=%d AND pid=%d', $subject->uid, $this->gid, $pid);
			if($res->fetch_single_value() > 0) {
				// User is listed in groups_user_perms as having that permission
				return TRUE;
			}

		} elseif($subject instanceof Group) {
			$res = $I2_SQL->query('SELECT count(*) FROM groups_group_perms WHERE usergroup=%d AND gid=%d AND pid=%d', $subject->gid, $this->gid, $pid);
			if($res->fetch_single_value() > 0) {
				// Group is listed in groups_group_perm has having that permission
				return TRUE;
			}

		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}

		// At this point, $subject does not have that permission listed
		// Check to see if a group that $subject is in has the permission

		foreach($this->groups_with_perm($pid) as $group) {
			if($group->has_member($subject)) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public static function list_permissions($pid = NULL) {
		global $I2_SQL;
		
		if($pid === NULL) {
			return $I2_SQL->query('SELECT pid,name,description FROM permissions');
		}
		return $I2_SQL->query('SELECT pid,name,description FROM permissions WHERE pid=%d',$pid)->fetch_array();
	}

	public function has_member($subject = NULL) {
		global $I2_SQL;

		if($subject === NULL) {
			$subject = $GLOBALS['I2_USER'];
		}

		// If the user is in admin_all, they're also admin_anything
		if (substr($this->name, 0, 6) == 'admin_'  && $this->name != 'admin_all' && Group::admin_all()->has_member($subject)) {
			return TRUE;
		}

		if($subject instanceof User) {
			// Check static member list
			if(in_array($subject->uid, $this->members)) {
				return TRUE;
			}

			// Check dynamic groups
			$dynamic = $I2_SQL->query('SELECT dbtype,query FROM groups_dynamic WHERE gid=%d', $this->gid);
			foreach($dynamic as $group) {
				if(self::is_dynamic_member($group['dbtype'], $group['query'], $subject)) {
					return TRUE;
				}
			}

		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}

		// Check dynamic join groups
		$dynamic = $I2_SQL->query('SELECT optype,group1,group2 FROM groups_join WHERE gid=%d', $this->gid);
		foreach($dynamic as $group) {
			if(self::is_join_member($group['optype'], $group['group1'], $group['group2'], $subject)) {
				return TRUE;
			}
		}
	}

	public function set_group_name($name) {
		global $I2_SQL, $I2_USER;
		
		if (!self::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to change group names');
		}
		
		return $I2_SQL->query('UPDATE groups_name SET name=%s WHERE gid=%d',$name,$this->gid);
	}

	public function groups_with_perm($pid = NULL) {
		global $I2_SQL;

		if($pid) {
			$res = $I2_SQL->query('SELECT usergroup FROM groups_group_perms WHERE gid=%d AND pid=%d', $this->gid, $pid);
		} else {
			$res = $I2_SQL->query('SELECT usergroup FROM groups_group_perms WHERE gid=%d', $this->gid);
		}

		$ret = array();
		foreach($res as $row) {
			$ret[] = new Group($row[0]);
		}
		return $ret;
	}

	public function users_with_perm($pid = NULL) {
		global $I2_SQL;

		if($pid) {
			$res = $I2_SQL->query('SELECT uid FROM groups_user_perms WHERE gid=%d AND pid=%d', $this->gid, $pid);
		} else {
			$res = $I2_SQL->query('SELECT uid FROM groups_user_perms WHERE gid=%d', $this->gid);
		}

		$ret = array();
		foreach($res as $row) {
			$ret[] = new User($row[0]);
		}
		return $ret;
	}

	public static function get_static_groups(User $user, $perms = NULL) {
		global $I2_SQL, $I2_USER;
		$ret = array();

		if ($user->uid != $I2_USER->uid && Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to view this user\'s group membership');
		}

		if($perms && !is_array($perms)) {
			$perms = array($perms);
		}

		if($perms) {
			foreach($perms as $i=>$perm) {
				$perms[$i] = self::get_pid($perm);
				if($perms[$i] === FALSE) {
					throw new I2Exception("Invalid permission $perm passed to".__METHOD__);
				}
			}
		}
		
		// If only one permission was specified, do the permission restricting in the database
		if(is_array($perms) && count($perms) == 1) {
			$res = $I2_SQL->query('SELECT grp.gid FROM groups_static AS grp LEFT JOIN (groups_user_perms AS perm) ON (grp.uid=perm.uid AND grp.gid=perm.gid) WHERE grp.uid=%d AND perm.pid=%d',$user->uid, $perms[0]);
		} else {
			$res = $I2_SQL->query('SELECT gid FROM groups_static WHERE uid=%d',$user->uid);
		}
		
		$ret = array();
		foreach($res as $row) {
			$grp = new Group($row[0]);
			$ret[] = $grp;
			if(is_array($perms) && count($perms) > 1) {
				// If multiple permissions were passed, make sure the user has each of those permissions in the group before including that group in the return array
				foreach($perms as $perm) {
					if(!$grp->has_permission($user, $perm)) {
						// User does not have all of the specified permissions in that group, so don't include that group
						array_pop($ret);
						break;
					}
				}
			}
		}
		return $ret;
	}

	public static function get_admin_groups(User $user) {
		$groups = Group::get_static_groups($user);
		$ret = array();
		
		foreach($groups as $group) {
			if($group->is_admin($user)) {
				$ret[] = $group;
			}
		}
		return $ret;
	}
	
	public function delete_group() {
		global $I2_SQL;
		
		if(!Group::admin_all()->has_member($GLOBALS['I2_USER'])) {
			throw new I2Exception('User is not authorized to delete groups.');
		}

		$I2_SQL->query('DELETE FROM groups_static WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_dynamic WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_group_perms WHERE gid=%d OR usergroup=%d', $this->gid, $this->gid);
		$I2_SQL->query('DELETE FROM groups_join WHERE gid=%d OR group1=%d OR group2=%d', $this->gid, $this->gid, $this->gid);
		$I2_SQL->query('DELETE FROM groups_user_perms WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_name WHERE gid=%d', $this->gid);
	}

	public static function add_group($name,$description='No description available',$gid=NULL) {
		global $I2_SQL,$I2_AUTH;

		/*
		** Any user with the admin password is allowed to create groups; this allows bootstrapping
		** and has minimal ill side effects (ideally, no one knows/uses the admin password except 8th pd.)
		*/
		if(		!$I2_AUTH->used_master_password()
				&& !Group::admin_all()->has_member($GLOBALS['I2_USER'])) {
			throw new I2Exception('User is not authorized to create groups.');
		}

		/*$res = $I2_SQL->query('SELECT gid FROM groups_name WHERE name=%s;',$name);
		if($res->num_rows() > 0) {
			throw new I2Exception("Tried to create group with name `$name`, which already exists as gid `{$res->fetch_single_value()}`");
		}*/

		if ($gid === NULL) {
			$res = $I2_SQL->query('INSERT INTO groups_name (name,description) VALUES (%s,%s)',$name,$description);
		} else {
			$res = $I2_SQL->query('INSERT INTO groups_name (name,description,gid) VALUES (%s,%s,%d)',$name,$description,$gid);
		}
		return $res->get_insert_id();
	}

	public function is_admin(User $user) {
		global $I2_SQL;

		if($this->has_permission($user, self::PERM_ADMIN)) {
			return TRUE;
		}

		// admin_all members get admin access to all groups
		if(Group::admin_all()->has_member($user)) {
			return TRUE;
		}

		// They're not an admin of this group nor are they a member of admin_all
		return FALSE;
	}

	public static function generate($gids) {
		global $I2_SQL;
		if(is_array($gids)) {
			$ret = array();
			foreach($gids as $gid) {
				$ret[] = new Group($gid);
			}
		} else {
			return new Group($gids);
		}
		return $ret;
	}

	public static function get_pid($perm) {
		global $I2_SQL;
		if(is_numeric($perm)) {
			// We were passed a PID, just check its existence
			$res = $I2_SQL->query('SELECT pid FROM permissions WHERE pid=%d',$perm);
		} else {
			// We were passed a permission name, get the PID
			$res = $I2_SQL->query('SELECT pid FROM permissions WHERE name=%s',$perm);
		}

		// If permission does not exist, return false
		if($res->num_rows() < 1) {
			return FALSE;
		}

		return $res->fetch_single_value();
	}

	/**
	* Determines if a user is a member of a dynamic group.
	*
	* @param string $dbtype The type of database to consult for membership ('PHP','LDAP', or 'MYSQL')
	* @param string $query The dbtype-specific query to perform to determine membership.
	* @param User $user The user whose membership we are determining.
	* @return bool TRUE if the user is a member of the dynamic group, FALSE otherwise.
	*/
	private static function is_dynamic_member($dbtype, $query, User $user) {
		global $I2_LDAP, $I2_SQL;

		switch ($dbtype) {
			case 'PHP':
				return eval($query);
			case 'LDAP':
				$res = $I2_LDAP->search(LDAP::get_user_dn($user->uid),$query,array('iodineUidNumber'));
			case 'MYSQL':
				$res = $I2_SQL->query($query);
		}

		if($res->num_rows() < 1) {
			// Nobody is in that group, so... this person certainly is not
			return FALSE;
		}

		while($row = $res->fetch_array(Result::NUM)) {
			if($row[0] == $user->uid) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Determines if a user is a member of a dynamic join group
	*
	* @param string $optype The logical operation to perform for this join group ('AND','AND NOT','OR' or 'OR NOT')
	* @param int $grp1 The GID of the first group.
	* @param int $grp2 The GID of the second group.
	* @param User $subject The user whose membership we are determining.
	* @return bool TRUE if the user is a member of the dynamic join group, FALSE otherwise.
	*/
	private static function is_join_member($optype, $grp1, $grp2, User $subject) {
		$grp1 = new Group($grp1);
		$grp2 = new Group($grp2);
		switch($optype) {
			case 'AND':
				return $grp1->has_member($subject) && $grp2->has_member($subject);
			case 'AND NOT':
				return $grp1->has_member($subject) && !$grp2->has_member($subject);
			case 'OR':
				return $grp1->has_member($subject) || $grp2->has_member($subject);
			case 'OR NOT':
				return $grp1->has_member($subject) || !$grp2->has_member($subject);
		}
		throw new I2Exception('Invalid $optype passed to '.__METHOD__.": $optype");
	}
}
?>
