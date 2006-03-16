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
* The module runs groups using MySQL
* @package modules
* @subpackage Group
*/
class GroupSQL extends Group {

	private $mygid;
	private $myname;
	
	/**
	* groupname to GID mapping
	*/
	private static $gid_map;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'gid':
				return $this->mygid;
			case 'name':
				return $this->myname;
			case 'special':
				return ($this->mygid < 0);
			case 'members':
				return $this->get_members();
			case 'members_obj':
				return User::id_to_user($this->get_members());
		}
	}

	public function __construct($group) {
		global $I2_SQL;

		if (self::$gid_map === NULL) {
			 self::$gid_map = array();
			 $res = $I2_SQL->query('SELECT name,gid FROM groups');
			 while ($row = $res->fetch_array(Result::ASSOC)) {
			 	self::$gid_map[$row['name']] = $row['gid'];
			 }
		}

		if(is_numeric($group)) {
		// Numeric $group passed; figure out group name
			$name = $I2_SQL->query('SELECT name FROM groups WHERE gid=%d', $group)->fetch_single_value();
			try {
				if($name) {
					$this->mygid = $group;
					$this->myname = $name;
				}
				elseif($name = Group::get_special_group($group)) {
					$this->mygid = $group;
					$this->myname = $name;
				}
				else {
					throw new I2Exception("Nonexistent group id $group given to the Group module");
				}
			} catch(I2Exception $e) {
				throw new I2Exception("Nonexistent group id $group given to the Group module");
			}
		}
		else {
		// Non-numeric $group passed; figure out GID
			if(isSet(self::$gid_map[$group])) {
				$this->mygid = self::$gid_map[$group];
				$this->myname = $group;
			}
			else {
				try {
					$this->mygid = Group::get_special_group($group);
					$this->myname = $group;
				} catch (I2Exception $e) {
					throw new I2Exception("Nonexistent group $group given to the Group module");
				}
			}
		}
	}

	public function get_members() {
		global $I2_SQL;

		return flatten($I2_SQL->query('SELECT uid FROM group_user_map WHERE gid=%d',$this->mygid)->fetch_all_arrays(Result::NUM));
	}
	
	public static function get_all_groups($module = NULL) {
		global $I2_SQL;
		$prefix = '%';
		if($module) {
			$prefix = strtolower($module) . '_%';
		}
		$ret = array();
		foreach($I2_SQL->query('SELECT gid FROM groups WHERE name LIKE %s', $prefix) as $row) {
			$ret[] = new Group($row[0]);
		}
		return $ret;
	}

	public static function get_all_group_names() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT name FROM groups');
	}

	public function add_user($user) {
		global $I2_SQL;

		if(is_numeric($user)) {
			$user = new User($user);
		}
		if($this->special) {
			throw I2Exception("Attempted to add user {$user->uid} to invalid group {$this->mygid}");
		}
		return $I2_SQL->query('INSERT INTO group_user_map (gid,uid) VALUES (%d,%d)',$this->mygid,$user->uid);
	}

	public function remove_user(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to remove user {$user->uid} from invalid group {$this->mygid}");
		}
		
		return $I2_SQL->query('DELETE FROM group_user_map WHERE uid=%d AND gid=%d',$user->uid,$this->mygid);
	}

	public function remove_all_members() {
		global $I2_SQL;

		if($this->special) {
			throw new I2Exception("Attempted to remove all users from invalid group {$this->mygid}");
		}

		return $I2_SQL->query('DELETE FROM group_user_map WHERE gid=%d', $this->mygid);
	}

	public function grant_permission(User $user, $perm) {
		global $I2_SQL;

		if($this->special) {
			throw new I2Exception("Attempted to grant privileges to user {$user->uid} for invalid group {$this->mygid}");
		}
		
		return $I2_SQL->query('INSERT INTO groups_perms (uid,gid,permission) VALUES (%d,%d,%s)', $user->uid, $this->mygid, $perm);
	}
	
	public function revoke_permission(User $user, $perm) {
		global $I2_SQL;

		if($this->special) {
			throw new I2Exception("Attempted to revoke privileges from user {$user->uid} for invalid group {$this->mygid}");
		}

		return $I2_SQL->query('DELETE FROM groups_perms WHERE uid=%d AND gid=%d AND permission=%s', $user->uid, $this->mygid, $perm);
	}

	public function get_permissions(User $user) {
		global $I2_SQL;
		
		if($this->special) {
			throw new I2Exception("Attempted to list privileges for user {$user->uid} in invalid group {$this->mygid}");
		}

		return $I2_SQL->query('SELECT permission FROM groups_perms WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
	}

	public function has_member($user=NULL) {
		global $I2_SQL;

		if($user===NULL) {
			$user = $GLOBALS['I2_USER'];
		}

		// If the user is in admin_all, they're also admin_anything
		if (substr($this->name, 0, 6) == 'admin_'  && $this->name != 'admin_all' && Group::admin_all()->has_member($user)) {
			return TRUE;
		}

		// Check for 'special' groups
		if( $this->special ) {
			$specs = Group::get_special_groups($user);
			return in_array($this->mygid, $specs);
		}
		
		// Standard DB check
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
		if( $res->num_rows() > 0) {
			return TRUE;
		}

		return FALSE;
	}

	public function set_group_name($name) {
		global $I2_SQL;
		return $I2_SQL->query('UPDATE groups SET name=%s WHERE gid=%d',$name,$this->mygid);
	}

	public static function get_user_groups(User $user, $include_special = TRUE) {
		global $I2_SQL;
		$ret = array();
		
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d',$user->uid);
		
		foreach($res as $row) {
			$ret[] = new Group($row['gid']);
		}
		if($include_special && $user->grade) {
			$ret[] = new Group("grade_{$user->grade}");
		}
		return $ret;
	}

	public static function get_admin_groups(User $user) {
		$groups = Group::get_user_groups($user, FALSE);
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
		
		if(!Group::admin_groups()->has_member($GLOBALS['I2_USER']) && !(Group::admin_eighth()->has_member($GLOBAL['I2_USER']) && substr($this->name, 0, 7) == "eighth_")) {
			throw new I2Exception('User is not authorized to delete groups.');
		}

		$I2_SQL->query('DELETE FROM group_user_map WHERE gid=%d;', $this->mygid);
		$I2_SQL->query('DELETE FROM groups WHERE gid=%d;', $this->mygid);
	}

	public static function add_group($name) {
		global $I2_SQL;

		if(!Group::admin_groups()->has_member($GLOBALS['I2_USER']) && !(Group::admin_eighth()->has_member($GLOBALS['I2_USER']) && substr($name, 0, 7) == 'eighth_')) {
			throw new I2Exception('User is not authorized to create groups.');
		}

		$res = $I2_SQL->query('SELECT gid FROM groups WHERE name=%s;',$name);
		if($res->num_rows() > 0) {
			throw new I2Exception("Tried to create group with name `$name`, which already exists as gid `{$res->fetch_single_value()}`");
		}

		$I2_SQL->query('INSERT INTO groups (name) VALUES (%s);',$name);
	}

	public function is_admin(User $user) {
		global $I2_SQL;

		$res = $I2_SQL->query('SELECT * FROM groups_perms WHERE uid=%d AND gid=%d AND permission=%s;',$user->uid,$this->mygid, 'I2_ADMIN');
		if($res->num_rows() >= 1) {
			return TRUE;
		}

		// admin_all members get admin access to all groups, I believe
		if(Group::admin_all()->has_member($user)) {
			return TRUE;
		}
		// They're not even a member of the group, so... not an admin
		return FALSE;
	}

}
?>
