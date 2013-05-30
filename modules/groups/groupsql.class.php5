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
	* Dynamic rules mapping
	*/
	private static $rules_map = NULL;

	/**
	* Membership list cache
	*/
	private static $members_cache = NULL;

	/**
	* Group info cache
	*/
	private $info = [];

	/**
	* The constructor.
	*
	* One of three things can be passed as an argument to __construct for GroupSQL. A GID or group name will cause Group to lookup the information in the database based on the specified info. Passing a Group object will cause this object to be a copy or reference to that group object, utilizing the same information cache.
	*
	* @param mixed $group Either a group name, a GID, or a Group object, as outlined above.
	*/
	public function __construct($group) {
		global $I2_SQL, $I2_CACHE;
		// Generate the GID/name maps if they do not exist
		if(self::$gid_map === NULL)
			self::$gid_map = unserialize($I2_CACHE->read($this,'gid_map'));
		if(self::$name_map === NULL)
			self::$name_map = unserialize($I2_CACHE->read($this,'name_map'));
		if(self::$gid_map === FALSE || self::$name_map === FALSE) {
			$res = $I2_SQL->query('SELECT name,gid FROM groups_name')->fetch_all_arrays(Result::ASSOC);
			foreach($res as $row) {
				self::$gid_map[$row['name']] = $row['gid'];
				self::$name_map[$row['gid']] = $row['name'];
			}
			$I2_CACHE->store($this,'gid_map',serialize(self::$gid_map));
			$I2_CACHE->store($this,'name_map',serialize(self::$name_map));
		}

		if(self::$rules_map === NULL)
			self::$rules_map = $I2_SQL->query('SELECT dbtype, query, gid FROM groups_dynamic')->fetch_all_arrays_keyed_list('gid',Result::ASSOC);

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
				$this->info[$var] = array_unique(array_merge($this->get_static_members(), $this->get_dynamic_members()));
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
		if(!isset(self::$members_cache[$this->gid]))
			self::$members_cache[$this->gid]=flatten($I2_SQL->query('SELECT uid FROM groups_static WHERE gid=%d',$this->gid)->fetch_all_arrays(Result::NUM));
		return self::$members_cache[$this->gid];
	}

	public function get_dynamic_members() {
		global $I2_SQL, $I2_LDAP;
		//$res = $I2_SQL->query('SELECT dbtype, query FROM groups_dynamic WHERE gid=%d', $this->gid);
		if(!isset(self::$rules_map[$this->gid]))
			return [];
		$list = self::$rules_map[$this->gid];
		$members = [];
		foreach ($list as $row) {
			switch ($row['dbtype']) {
			case 'LDAP':
				$rulemembers = $I2_LDAP->search(LDAP::get_user_dn(),$row['query'],array('iodineUidNumber'))->fetch_col('iodineUidNumber');
				$newmembers = array_diff($rulemembers, $members);
				$members = array_merge($members, $newmembers);
				break;
			case 'MYSQL':
				$rulemembers = $I2_SQL->query($row['query'])->fetch_col(0);
				$newmembers = array_diff($rulemembers, $members);
				$members = array_merge($members, $newmembers);
				break;
			}
		}
		return $members;
	}

	public static function get_all_groups($module = NULL) {
		global $I2_SQL;
		$prefix = '%';
		if($module) {
			$prefix = strtolower($module) . '_%';
		}
		$ret = [];
		foreach($I2_SQL->query('SELECT gid FROM groups_name WHERE name LIKE %s ORDER BY name', $prefix) as $row) {
			$ret[] = new Group($row[0]);
		}
		return $ret;
	}

	public static function get_all_user_group_info() {
		global $I2_USER,$I2_SQL;
		$groups=$I2_SQL->query('SELECT groups_static.gid,groups_name.name FROM groups_static,groups_name WHERE (groups_static.gid=groups_name.gid) AND groups_static.uid=%d',$I2_USER->uid)->fetch_all_arrays_keyed('gid',MYSQLI_ASSOC);
		print_r($groups);
		$perms=$I2_SQL->query('SELECT groups_static.gid,groups_user_perms.pid FROM groups_static,groups_user_perms WHERE (groups_static.gid=groups_user_perms.gid) AND (groups_static.uid=groups_user_perms.uid) AND groups_static.uid=%d',$I2_USER->uid)->fetch_all_arrays_keyed_list('gid',MYSQLI_ASSOC);
		print_r($perms);
	}

	public static function get_all_group_names() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT name FROM groups_name');
	}

	public function add_user(User $user) {
		global $I2_SQL, $I2_USER, $I2_CACHE;

		if($this->has_member($user)) {
			// Meh? they're already a member
			return FALSE;
		}

		if (	// Admins can add anyone to a group
			self::admin_all()->has_member($I2_USER) ||

			// People can add themselves if they have the Group::PERM_JOIN permission
			($I2_USER->uid == $user->uid && $this->has_permission($user, Permission::getPermission(Group::PERM_JOIN))) ||

			// People with the Group::PERM_ADD permission can add people
			$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_ADD))
		) {

			// Only insert member into the cache if the cache has been fetched
			// Otherwise, the member would be the _only_ member in the cache if it hadn't been fetched, giving incorrect results from __get
			if(isset($this->info['members'])) {
				$this->info['members'][] = $user->uid;
			}
			$I2_CACHE->remove($this,'groups_static_'.$user->uid);
			return $I2_SQL->query('INSERT INTO groups_static (gid,uid) VALUES (%d,%d)',$this->gid,$user->uid);
		} else {
			throw new I2Exception('You are not authorized to add users into this group!');
		}
	}

	public function remove_user(User $user) {
		global $I2_SQL, $I2_USER, $I2_CACHE;

		if(!$this->has_member($user)) {
			// Meh? they're already not a member
			return FALSE;
		}

		if (	// Admins can remove anyone from a group
			self::admin_all()->has_member($I2_USER) ||

			// People can remove themselves if they can add themselves, as well
			($I2_USER->uid == $user->uid && $this->has_permission($user, Permission::getPermission(Group::PERM_JOIN))) ||

			// People with the Group::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_REMOVE))
		) {

			// Delete user from member cache if the cache has been fetched
			if(isset($this->info['members']) && ($key = array_search($user->uid,$this->info['members']))) {
				unset($this->info['members'][$key]);
			}
			$I2_CACHE->remove($this,'groups_static_'.$user->uid);
			return $I2_SQL->query('DELETE FROM groups_static WHERE gid=%d AND uid=%d',$this->gid,$user->uid);
		} else {
			throw new I2Exception('You are not authorized to remove users from this group!');
		}
	}

	public function remove_static_members() {
		global $I2_SQL, $I2_USER, $I2_CACHE;

		if (	// Admins can remove anyone from a group
			self::admin_all()->has_member($I2_USER) ||

			// People with the Group::PERM_REMOVE permission can remove people
			$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_REMOVE))
		) {
			
			// Delete member cache if it has been fetched
			if(isset($this->info['members'])) {
				unset($this->info['members']);
			}
			$I2_CACHE->remove($this,'groups_static_'.$user->uid);
			return $I2_SQL->query('DELETE FROM groups_static WHERE gid=%d', $this->gid);
		} else {
			throw new I2Exception('You are not authorized to remove users from groups!');
		}

	}

	public function remove_dynamic_rules() {
		global $I2_SQL, $I2_USER;
		if ( 	self::admin_all()->has_member($I2_USER) ||
			$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_REMOVE))
		) {
			return $I2_SQL->query('DELETE FROM groups_dynamic WHERE gid=%d', $this->gid);
		} else {
			throw new I2Exception('You are not authorized to remove users from groups!');
		}
	}

	public function grant_permission($subject, Permission $perm) {
		global $I2_SQL, $I2_USER;

		// Check authorization
		if(	// Admins can add permissions to anyone
			!self::admin_all()->has_member($I2_USER) &&
			!$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_ADMIN))
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
			!$this->has_permission($I2_USER, Permission::getPermission(Group::PERM_ADMIN))
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

		$ret = [];
		foreach ($pids as $pid) {
			$ret[] = Permission::getPermission($pid);
		}
		return $ret;
	}

	public function has_permission($subject, $perm) {
		global $I2_SQL;

		if(is_string($perm)) {
			if($perm=="join") {
				$perm=Permission::getPermission('GROUP_JOIN');
			}
		}

		$pid = $perm->pid;

		$adminperm = Permission::getPermission(Group::PERM_ADMIN);
		$adminpid = $adminperm->pid;

		if($subject instanceof User) {
			// admin_all has all permissions
			if (Group::admin_all()->has_member($subject)) {
				return TRUE;
			}

			// prefix admins have all permissions
			if (Group::prefix($this->name) && Permission::perm_exists(Group::PERM_ADMIN_PREFIX . Group::prefix($this->name))) {
				$perm = Permission::getPermission(Group::PERM_ADMIN_PREFIX . Group::prefix($this->name));
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
		global $I2_SQL, $I2_USER;

		if($subject === NULL) {
			$subject = $I2_USER;
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
			if(isset(self::$rules_map[$this->gid])) {
				$list = self::$rules_map[$this->gid];
				//$dynamic = $I2_SQL->query('SELECT dbtype,query FROM groups_dynamic WHERE gid=%d', $this->gid);
				foreach($list as $group) {
					if(self::is_dynamic_member($group['dbtype'], $group['query'], $subject)) {
						return TRUE;
					}
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

		$adminperm = Permission::getPermission(Group::PERM_ADMIN);
		$adminpid = $adminperm->pid;

		if($pid) {
			$res = $I2_SQL->query('SELECT usergroup FROM groups_group_perms WHERE gid=%d AND (pid=%d OR pid=%d)', $this->gid, $pid, $adminpid);
		} else {
			$res = $I2_SQL->query('SELECT DISTINCT usergroup FROM groups_group_perms WHERE gid=%d', $this->gid);
		}

		$ret = [];
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

		$ret = [];
		foreach($res as $row) {
			$ret[] = new User($row[0]);
		}
		return $ret;
	}

	public function list_dynamic_rules() {
		global $I2_SQL;

		$ret = [];
		if(!isset(self::$rules_map[$this->gid]))
			return [];
		$list = self::$rules_map[$this->gid];
		//foreach($I2_SQL->query('SELECT dbtype,query FROM groups_dynamic WHERE gid=%d', $this->gid) as $row) {
		foreach($list as $row) {
			$arr['type'] = $row['dbtype'];
			$arr['query'] = $row['query'];

			$ret[] = $arr;
		}

		return $ret;
	}

	public function delete_dynamic_rules() {
		global $I2_SQL,$I2_USER;

		if(!Group::admin_all()->has_member($I2_USER)) {
			redirect('groups');
		}
		$I2_SQL->query('DELETE FROM groups_dynamic WHERE gid=%d',$this->gid);
		return TRUE;
	}

	public function add_dynamic_rule($type,$query) {
		global $I2_SQL, $I2_USER;
		if(!Group::admin_all()->has_member($I2_USER)) {
			redirect('groups');
		}
		$I2_SQL->query('INSERT INTO groups_dynamic VALUES (%d,%s,%s)',$this->gid,$type,$query);
		return TRUE;
	}

	public static function get_dynamic_groups(User $user, $perms = NULL) {
		global $I2_SQL, $I2_USER;
		$ret = [];

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

		//$res = $I2_SQL->query('SELECT gid, dbtype, query FROM groups_dynamic');
		if(!isset(self::$rules_map)) {
			 self::$rules_map = [];
			 $res = $I2_SQL->query('SELECT dbtype, query, gid FROM groups_dynamic');
			 while ($row = $res->fetch_array(Result::ASSOC)) {
			 	if(!isset(self::$rules_map[$row['gid']])) {
					self::$rules_map[$row['gid']]=[];
				}
			 	self::$rules_map[$row['gid']][]= array('dbtype'=>$row['dbtype'],'query'=>$row['query']);
			 }
		}
		foreach (self::$rules_map as $gid=>$rowset) {
			foreach($rowset as $row) {
				if (self::is_dynamic_member($row['dbtype'], $row['query'], $user)) {
					$grp = new Group($gid);
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
		}

		return $ret;
	}

	public static function get_static_groups(User $user, $perms = NULL) {
		global $I2_SQL, $I2_USER, $I2_CACHE;
		$ret = [];

		if ($user->uid != $I2_USER->uid && !self::admin_all()->has_member($I2_USER)) {
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
		$res = unserialize($I2_CACHE->read(get_class(),'groups_static_'.$user->uid));
		if ($res === FALSE) {
			$res = $I2_SQL->query('SELECT gid FROM groups_static WHERE uid=%d',$user->uid)->fetch_all_single_values();
			$I2_CACHE->store(get_class(),'groups_static_'.$user->uid,serialize($res));
		}
		
		$ret = [];
		foreach($res as $row) {
			$grp = new Group($row);
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
		$ret = [];

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
		$ret = [];
		
		foreach($groups as $group) {
			if($group->is_admin($user)) {
				$ret[] = $group;
			}
		}
		usort($ret, array('Group', 'name_cmp'));
		return $ret;
	}

	public static function get_userperm_groups(User $user, Permission $perm) {
		$allgrps = Group::get_all_groups();
		$ret = [];
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
		global $I2_SQL, $I2_AUTH, $I2_CACHE;

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
			$I2_CACHE->store(get_class(),'gid_map',serialize(self::$gid_map));
			$I2_CACHE->store(get_class(),'name_map',serialize(self::$name_map));
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

		$res;
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

		//Sends up a warning
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
