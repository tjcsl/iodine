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

	/**
	* groupname to GID mapping
	*/
	private static $gid_map = NULL;

	/**
	* GID to groupname mapping
	*/
	private static $name_map = NULL;

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
				throw new I2Exception("Nonexistent GID $group given to the GroupSQL constructor");
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
				usort($members,array($this,'sort_by_name'));
				return $members;
		}

		if(!isset($this->info[$var])) {
			throw new I2Exception('Invalid attribute passed to GroupSQL::__get(): '.$var.', or invalid GID: '.$this->__get('gid'));
		}

		return $this->info[$var];
	}

	private function sort_by_name($one,$two) {
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

			// People can add themselves if they have the Group::PERM_JOIN permission
			($I2_USER->uid == $user->uid && $this->has_permission($user, new Permission(Group::PERM_JOIN))) ||

			// People with the Group::PERM_ADD permission can add people
			$this->has_permission($I2_USER, new Permission(Group::PERM_ADD))
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
			($I2_USER->uid == $user->uid && $this->has_permission($user, new Permission(Group::PERM_JOIN))) ||

			// People with the Group::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER, new Permission(Group::PERM_REMOVE))
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

			// People with the Group::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER, new Permission(Group::PERM_REMOVE))
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

	public function grant_permission($subject, Permission $perm) {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if(	// Admins can add permissions to anyone
			!self::admin_all()->has_member($I2_USER) &&
			!$this->has_permission($I2_USER, new Permission(Group::PERM_ADMIN))
		) {
			throw new I2Exception('You are not authorized to grant permissions in this group!');
		}

		$pid = $perm->pid;

		if($subject instanceof User) {
			return $I2_SQL->query('INSERT INTO groups_user_perms (uid,gid,pid) VALUES (%d,%d,%d)',$subject->uid, $this->gid, $pid);
		} elseif ($subject instanceof Group) {
			return $I2_SQL->query('INSERT INTO groups_group_perms (usergroup,gid,pid) VALUES (%d,%d,%d)',$subject->gid, $this->gid, $pid);
		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}
	}
	
	public function revoke_permission($subject, $perm) {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if(	// Admins can revoke permissions from anyone
			!self::admin_all()->has_member($I2_USER) &&
			!$this->has_permission($I2_USER, new Permission(Group::PERM_ADMIN))
		) {
			throw new I2Exception('You are not authorized to revoke permissions in this group!');
		}

		$pid = $perm->pid;

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
			$pids = flatten($I2_SQL->query('SELECT pid FROM groups_user_perms WHERE uid=%d AND gid=%d', $subject->uid, $this->gid)->fetch_all_arrays(Result::NUM));
		} elseif($subject instanceof Group) {
			$pids = flatten($I2_SQL->query('SELECT pid FROM groups_group_perms WHERE usergroup=%d AND gid=%d', $subject->gid, $this->gid)->fetch_all_arrays(Result::NUM));
		} else {
			throw new I2Exception('Invalid object passed as $subject to '.__METHOD__);
		}

		$ret = array();
		foreach ($pids as $pid) {
			$ret[] = new Permission($pid);
		}
		return $ret;
	}

	public function has_permission($subject, Permission $perm) {
		global $I2_SQL;

		$pid = $perm->pid;

		$adminperm = new Permission(Group::PERM_ADMIN);
		$adminpid = $adminperm->pid;

		if($subject instanceof User) {
			// admin_all has all permissions
			if (Group::admin_all()->has_member($subject)) {
				return TRUE;
			}

			// prefix admins have all permissions
			if (Group::prefix($this->name) && Permission::perm_exists(Group::PERM_ADMIN_PREFIX . Group::prefix($this->name))) {
				$perm = new Permission(Group::PERM_ADMIN_PREFIX . Group::prefix($this->name));
				$res = $I2_SQL->query('SELECT count(*) FROM groups_user_perms WHERE uid=%d AND gid=%d AND pid=%d', $subject->uid, Group::all()->gid, $perm->pid);
				if ($res->fetch_single_value() > 0) {
					return TRUE;
				}
			}

			$res = $I2_SQL->query('SELECT count(*) FROM groups_user_perms WHERE uid=%d AND gid=%d AND (pid=%d OR pid=%d)', $subject->uid, $this->gid, $pid, $adminpid);
			if($res->fetch_single_value() > 0) {
				// User is listed in groups_user_perms as having that permission
				return TRUE;
			}

		} elseif($subject instanceof Group) {
			$res = $I2_SQL->query('SELECT count(*) FROM groups_group_perms WHERE usergroup=%d AND gid=%d AND (pid=%d OR pid=%d)', $subject->gid, $this->gid, $pid, $adminpid);
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

		$adminperm = new Permission(Group::PERM_ADMIN);
		$adminpid = $adminperm->pid;

		if($pid) {
			$res = $I2_SQL->query('SELECT usergroup FROM groups_group_perms WHERE gid=%d AND (pid=%d OR pid=%d)', $this->gid, $pid, $adminpid);
		} else {
			$res = $I2_SQL->query('SELECT DISTINCT usergroup FROM groups_group_perms WHERE gid=%d', $this->gid);
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
			$res = $I2_SQL->query('SELECT DISTINCT uid FROM groups_user_perms WHERE gid=%d', $this->gid);
		}

		$ret = array();
		foreach($res as $row) {
			$ret[] = new User($row[0]);
		}
		return $ret;
	}

	public function list_dynamic_rules() {
		global $I2_SQL;

		$ret = array();
		foreach($I2_SQL->query('SELECT dbtype,query FROM groups_dynamic WHERE gid=%d', $this->gid) as $row) {
			$arr['type'] = $row['dbtype'];
			$arr['query'] = $row['query'];

			$ret[] = $arr;
		}

		return $ret;
	}

	public static function get_dynamic_groups(User $user, $perms = NULL) {
		global $I2_SQL, $I2_USER;
		$ret = array();

		if ($user->uid != $I2_USER->uid && !Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to view this user\'s group membership');
		}

		if ($perms && !is_array($perms)) {
			$perms = array($perms);
		}

		if($perms) {
			foreach($perms as $perm) {
				if (! $perm instanceof Permission) {
					throw new I2Exception("Invalid permission $perm passed to".__METHOD__);
				}
			}
		}

		$res = $I2_SQL->query('SELECT gid, dbtype, query FROM groups_dynamic');
		foreach ($res as $row) {
			if (self::is_dynamic_member($row['dbtype'], $row['query'], $user)) {
				$grp = new Group($row['gid']);
				$ret[] = $grp;
				if (is_array($perms)) {
					foreach ($perms as $perm) {
						if (!$grp->has_permission($user, $perm)) {
							array_pop($ret);
							break;
						}
					}
				}
			}
		}

		return $ret;
	}

	public static function get_static_groups(User $user, $perms = NULL) {
		global $I2_SQL, $I2_USER;
		$ret = array();

		if ($user->uid != $I2_USER->uid && !Group::admin_all()->has_member($I2_USER)) {
			throw new I2Exception('You are not authorized to view this user\'s group membership');
		}

		if($perms && !is_array($perms)) {
			$perms = array($perms);
		}

		if($perms) {
			foreach($perms as $perm) {
				if (! $perm instanceof Permission) {
					throw new I2Exception("Invalid permission $perm passed to".__METHOD__);
				}
			}
		}
		
		// If only one permission was specified, do the permission restricting in the database
		/*if(is_array($perms) && count($perms) == 1) {
			$res = $I2_SQL->query('SELECT grp.gid FROM groups_static AS grp LEFT JOIN (groups_user_perms AS perm) ON (grp.uid=perm.uid AND grp.gid=perm.gid) WHERE grp.uid=%d AND perm.pid=%d',$user->uid, $perms[0]->pid);
		} else {
			$res = $I2_SQL->query('SELECT gid FROM groups_static WHERE uid=%d',$user->uid);
		}*/
		// Can't do permission restricting in the database because of permission inheritance
		// (ADMIN_GROUP has all other permissions, etc.)
		$res = $I2_SQL->query('SELECT gid FROM groups_static WHERE uid=%d',$user->uid);
		
		$ret = array();
		foreach($res as $row) {
			$grp = new Group($row[0]);
			$ret[] = $grp;
			if(is_array($perms)) {
				// Make sure the user has each of those permissions in the group before including that group in the return array
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

	public static function user_admin_prefixes(User $user) {
		global $I2_SQL;
		$ret = array();

		$all = Group::all();
		$perms = $all->get_permissions($user);

		foreach ($perms as $perm) {
			$prefix = preg_replace('/^ADMIN_/', '', $perm->name);
			if ($prefix != $perm->name) {
				// $permname started with ADMIN_
				$ret[] = $prefix;
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

	public static function get_userperm_groups(User $user, Permission $perm) {
		$allgrps = Group::get_all_groups();
		$ret = array();
		foreach($allgrps as $i)
			if($i->has_permission($user, $perm))
				$ret[] = $i;
		return $ret;
	}
	
	public function delete_group() {
		global $I2_SQL;
		
		$name = $this->name;
		if (!Group::user_can_create($name)) {
			throw new I2Exception("User is not authorized to delete group $name.");
		}

		$I2_SQL->query('DELETE FROM groups_static WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_dynamic WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_group_perms WHERE gid=%d OR usergroup=%d', $this->gid, $this->gid);
		$I2_SQL->query('DELETE FROM groups_user_perms WHERE gid=%d', $this->gid);
		$I2_SQL->query('DELETE FROM groups_name WHERE gid=%d', $this->gid);
	}

	public static function add_group($name,$description='No description available',$gid=NULL) {
		global $I2_SQL,$I2_AUTH;

		if (!Group::user_can_create($name)) {
			throw new I2Exception("User is not authorized to create group $name.");
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
		$gid = $res->get_insert_id();
		if(self::$gid_map !== NULL) {
			self::$gid_map[$name] = $gid;
			self::$name_map[$gid] = $name;
		}
		return $gid;
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
				break;
			case 'MYSQL':
				$res = $I2_SQL->query($query);
				break;
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
}
?>
