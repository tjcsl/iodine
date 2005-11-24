<?php
/**
* Just contains the definition for the class {@link Groups}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package core
* @subpackage Group
* @filesource
*/

/**
* The module that runs groups
* @package core
* @subpackage Group
*/
class Group {

	/**
	* Commonly accessed administrative groups.
	*/
	private static $admin_groups = NULL;
	private static $admin_all = NULL;

	private $mygid;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'gid':
				return $this->mygid;
			case 'name':
				return $I2_SQL->query('SELECT name FROM groups WHERE gid=%d',$this->mygid)->fetch_single_value();
			case 'special':
				return ($this->mygid < 0);
		}
	}

	public function __construct($group=NULL) {
		// Ensure that admin groups are initialized
//		if( (self::$admin_groups === NULL || self::$admin_all === NULL) && $group != 'admin_groups' && $group != 'admin_all') { // these last two clauses are important; without them a infinitely-recursive constructor loop occurs, and apache segfaults
//			self::$admin_groups = new Group('admin_groups');
//			self::$admin_all = new Group('admin_all');
//		}

		if($group != NULL) {
			$this->mygid = self::get_group_id($group);
		}
	}

	private static function get_group_id($group) {
		global $I2_SQL;

		if(is_numeric($group)) {
			if( $I2_SQL->query('SELECT gid FROM groups WHERE gid=%d', $group)->num_rows() > 0 ) {
				return $group;
			}
			else {
				throw new I2Exception("Nonexistent group id $group given to the Group module");
			}
		}
		else {
			$gid = $I2_SQL->query('SELECT gid FROM groups WHERE name=%s',$group)->fetch_single_value();
			if($gid) {
				return $gid;
			}
			else {
				try {
					return self::get_special_group($group);
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
	
	/**
	* Gets all groups.
	*
	* @return Result A Result containing gids of all groups.
	*/
	public static function get_all_groups() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT gid FROM groups');
	}

	/**
	* Gets the name of every group
	*
	* @return Result A Result containing each group's name.
	*/
	public static function get_all_group_names() {
		global $I2_SQL;
		return $I2_SQL->query('SELECT name FROM groups');
	
	}

	public function add_user(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to add user {$user->uid} to invalid group {$this->mygid}");
		}
		return $I2_SQL->query('INSERT INTO group_user_map (gid,uid) VALUES(%d,%d)',$this->mygid,$user->uid);
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
			throw I2Exception("Attempted to remove all users from invalid group {$this->mygid}");
		}

		return $I2_SQL->query('DELETE FROM group_user_map WHERE gid=%d', $this->mygid);
	}

	public function grant_admin(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to grant admin privileges to user {$user->uid} for invalid group {$this->mygid}");
		}
		
		return $I2_SQL->query('UPDATE group_user_map SET is_admin=1 WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
	}
	
	public function revoke_admin(User $user) {
		global $I2_SQL;

		if($this->special) {
			throw I2Exception("Attempted to revoke admin privileges from user {$user->uid} for invalid group {$this->mygid}");
		}

		return $I2_SQL->query('UPDATE group_user_map SET is_admin=0 WHERE uid=%d AND gid=%d', $uid, $gid);
	}

	public function has_member(User $user) {
		global $I2_SQL;

		// If the user is in admin_all, they're also admin_anything
		if (substr($this->name,6) == 'admin_'  && $this->name != 'admin_all' && self::$admin_all->has_member($user)) {
			return TRUE;
		}

		// Check for 'special' groups
		if( $this->special ) {
			$specs = self::get_special_groups($user);
			return in_array($this->mygid, $specs);
		}
		
		// Standard DB check
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d AND gid=%d', $user->uid, $this->mygid);
		if( $res->num_rows() > 0) {
			return TRUE;
		}

		return FALSE;
	}

	public function is_group_admin($user) {
		global $I2_SQL;

		if( $this->special ) {
			throw I2Exception("is_group_admin() called on invalid group {$this->mygid} for user {$user->uid}");
		}
		
		if ($I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d', $user->uid, $group)->fetch_single_value()) {
			return TRUE;
		}
		return FALSE;
	}

	public function set_group_name($name) {
		global $I2_SQL;
		return $I2_SQL->query('UPDATE groups SET name=%s WHERE gid=%d',$name,$this->mygid);
	}

	/**
	* Gets the groups of which a user is a member.
	*
	*
	* @param int $uid The userID of which to fetch the groups.
	* @return array The group IDs of groups for the given user.
	*/
	public static function get_user_groups(User $user, $include_special = TRUE) {
		global $I2_SQL;
		$ret = array();
		
		$res = $I2_SQL->query('SELECT gid FROM group_user_map WHERE uid=%d',$user->uid);
		$groups = flatten($res->fetch_all_arrays(Result::NUM));
		
		foreach($groups as $gid) {
			$ret[] = new Group($gid);
		}
		if($include_special && $user->grade) {
			$ret[] = new Group("grade_{$user->grade}");
		}
		return $ret;
	}

	/**
	* @todo Write this; it's not just dropping a group but also cleaning up.
	*/
	public static function delete_group($gid) {
	}

	/**
	* Gets special group information.
	*
	* Given a special group name, returns the negative GID. Given a negative
	* GID, returns the group name.
	*
	* @param $group mixed Either a string, the group name, or an int, the
	*		GID.
	* @return mixed Either a string, the group name, or an int, the GID.
	*/
	private static function get_special_group($group) {
		switch($group) {
			case -9:
			case '-9':
				return 'grade_9';
			case -10:
			case '-10':
				return 'grade_10';
			case -11:
			case '-11':
				return 'grade_11';
			case -12:
			case '-12':
				return 'grade_12';
			case 'staff':
				return 'grade_staff';
			case 'grade_9':
				return -9;
			case 'grade_10':
				return -10;
			case 'grade_11':
				return -11;
			case 'grade_12':
				return -12;
			case 'grade_staff':
				return -8;
			case -8:
			case '-8':
				return 'grade_staff';
		}
		throw new I2Exception('Invalid special group '.$group.' passed to get_special_group');
	}

	public function is_admin(User $user) {
		global $I2_SQL;

		$res = $I2_SQL->query('SELECT is_admin FROM group_user_map WHERE uid=%d AND gid=%d;',$user->uid,$this->gid);
		if($res->num_rows() < 1) {
			// admin_all members get admin access to all groups, I believe
			if(self::$admin_all->has_member($user)) {
				return TRUE;
			}
			// They're not even a member of the group, so... not an admin
			return FALSE;
		}
		return $res->fetch_single_value();
	}

	public function get_name() {
		return 'Group';
	}
}
?>
