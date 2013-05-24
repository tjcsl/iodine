<?php
/**
* Just contains the definition for the class {@link GroupLDAP}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Group
* @filesource
*/

/**
* The module runs groups using LDAP
* @package modules
* @subpackage Group
*/
class GroupLDAP {

	private $mygid;
	private $myname;
	
	/**
	* groupname to GID mapping
	*/
	private static $gid_map;

	public function __get($var) {
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
		global $I2_LDAP;
		if(self::$gid_map === NULL) {
			self::$gid_map = [];
			$res = $I2_LDAP->search(LDAP::get_group_dn(), '(objectClass=iodineGroup)', array('cn', 'gidNumber'));
			while($row = $res->fetch_array(Result::ASSOC)) {
				self::$gid_map[$row['cn']] = $row['gidNumber'];
			}
		}

		if(is_array($group)) {
			$this->mygid = $group['gid'];
			$this->myname = $group['name'];
			$this->mydescription = $group['description'];
		} else if(is_numeric($group)) {
			@list($name, $description) = $I2_LDAP->search(LDAP::get_group_dn(), '(objectClass=iodineGroup)', array('cn', 'description'))->fetch_array(Result::NUM);
			try {
				if($name) {
					$this->mygid = $group;
					$this->myname = $name;
					$this->mydescription = $description;
				} else if($name = Group::get_special_group($group)) {
					$this->mygid = $group;
					$this->myname = $name;
				} else {
					throw new I2Exception("Nonexistent group {$group} given to the Group module");
				}
			} catch(I2Exception $e) {
				throw new I2Exception("Nonexistent group {$group} given to the Group module");
			}
		} else if(is_object($group)) {
			if(!$group instanceof Group) {
				throw new I2Exception('Group construction attempted with non-Group object!');
			}
			$this->mygid = $group->mygid;
			$this->myname = $group-myname;
		} else {
			if(isset(self::$gid_map[$group])) {
				$this->mygid = self::$gid_map[$group];
				$this->myname = $group;
			} else {
				try {
					$group = Group::get_special_group($group);
					$this->mygid = $group->gid;
					$this->myname = $group;
				} catch(I2Exception $e) {
					throw new I2Exception("Nonexistent group {$group} given to the Group module");
				}
			}
		}
	}

	public function get_members() {
		global $I2_LDAP;
		return flatten($I2_LDAP->search(LDAP::get_group_dn(), '(objectClass=iodineGroup)', array('uniqueMember'))->fetch_all_arrays(Result::NUM));
	}
	
	public static function get_all_groups($module = NULL) {
		global $I2_LDAP;
		$prefix = '*';
		if($module) {
			$prefix = strtolower($module) . '_*';
		}
		$ret = [];
		foreach($I2_LDAP->search(LDAP::get_group_dn(), '(objectClass=iodineGroup)', array('gidNumber')) as $row) {
			$ret[] = new Group($row[0]);
		}
		return $ret;
	}

	public static function get_all_group_names() {
		global $I2_LDAP;
		return $I2_LDAP->search(LDAP::get_group_dn(), '(objectClass=iodineGroup)', array('cn'));
	}

	public function add_user($user) {
		global $I2_LDAP;
		$user = new User($user);
		if($this->special) {
			throw new I2Exception("Attempted to add user {$user->uid} to invalid group {$this->name}");
		}
		return $I2_LDAP->attribute_add(LDAP::get_group_dn($this->name), array('uniqueMember' => LDAP::get_user_dn($user->uid)));
	}

	public function remove_user(User $user) {
		global $I2_LDAP;
		if($this->special) {
			throw new I2Exception("Attempted to remove user {$user->uid} from invalid group {$this->name}");
		}
		return $I2_LDAP->attribute_delete(LDAP::get_group_dn($this->name), array('uniqueMember' => LDAP::get_user_dn($user->uid)));
	}

	public function remove_all_members() {
		global $I2_LDAP;
		if($this->special) {
			throw new I2Exception("Attempted to remove all users from invalid group {$this->name}");
		}
		return $I2_LDAP->delete();
	}

	public function grant_admin(User $user) {
		global $I2_LDAP;
		$user = new User($user);
		if($this->special) {
			throw new I2Exception("Attempted to grant admin to user {$user->uid} in invalid group {$this->name}");
		}
		return $I2_LDAP->attribute_add(LDAP::get_group_dn($this->name), array('owner' => LDAP::get_user_dn($user->uid)));
	}
	
	public function revoke_admin(User $user) {
		global $I2_LDAP;
		if($this->special) {
			throw new I2Exception("Attempted to revoke admin from user {$user->uid} in invalid group {$this->name}");
		}
		return $I2_LDAP->attribute_delete(LDAP::get_group_dn($this->name), array('owner' => LDAP::get_user_dn($user->uid)));
	}

	public function has_member($user=NULL) {
		global $I2_LDAP, $I2_USER;
		if($user === NULL) {
			$user = $I2_USER;
		}
		if(substr($this->name, 0, 6) == 'admin_' && $this->name != 'admin_all' && Group::admin_all()->has_member($user)) {
			return TRUE;
		}
		if($this->special) {
			$specs = Group::get_special_groups($user);
			foreach($specs as $gp) {
				if($gp->gid == $this->mygid) {
					return TRUE;
				}
			}
			return FALSE;
		}
		$res = $I2_LDAP->search(LDAP::get_group_dn(), "(&(cn={$this->name})(uniqueMember=" . LDAP::get_user_dn($user->uid) . "))", array('cn'));
		return ($res->num_rows() > 0);
	}

	public function set_group_name($name) {
	}

	public static function get_user_groups(User $user, $include_special = TRUE, $perms = NULL) {
		global $I2_LDAP;
		$ret = [];
		if($perms && !is_array($perms)) {
			$perms = array($perms);
		}
		if(is_array($perms)) {
			/* TODO: Make this actually support permissions...whatever that means. */
			$res = $I2_LDAP->search(LDAP::get_group_dn(), '(uniqueMember=' . LDAP::get_user_dn($user->uid) . ')', array('cn'));
		} else {
			$res = $I2_LDAP->search(LDAP::get_group_dn(), '(uniqueMember=' . LDAP::get_user_dn($user->uid) . ')', array('cn'));
		}

		foreach($res as $row) {
			$ret[] = new Group($row['cn']);
		}
		if($include_special && $user->grade) {
			$ret[] = new Group("grade_{$user->grade}");
		}
		return $ret;
	}

	public static function get_admin_groups(User $user) {
		$groups = Group::get_user_groups($user, FALSE);
		$ret = [];
		foreach($groups as $group) {
			if($group->is_admin($user)) {
				$ret[] = $group;
			}
		}
		return $ret;
	}
	
	public function delete_group() {
		global $I2_LDAP, $I2_USER;
		if(!Group::admin_groups()->has_member($I2_USER) && !(Group::admin_eighth()->has_member($I2_USER) && substr($this->name, 0, 7) == 'eighth_')) {
			throw new I2Exception('User is not authorized to delete groups.');
		}

		$I2_LDAP->delete(LDAP::get_group_dn($this->name));
	}

	public static function add_group($name) {
		global $I2_LDAP, $I2_AUTH, $I2_USER;
		if(!$I2_AUTH->user_master_password() && !Group::admin_groups()->has_member($I2_USER) && !(Group::admin_eighth()->has_member($I2_USER) && substr($name, 0, 7) == 'eighth_')) {
			throw new I2Exception('User is not authorized to create groups.');
		}
		if($gid === NULL) {
			$I2_LDAP->add(LDAP::get_group_dn($name), array('cn' => $name));
		} else {
			$I2_LDAP->add(LDAP::get_group_dn($name), array('cn' => $name));
		}
	}

	public function is_admin(User $user) {
		global $I2_LDAP;
		$res = $I2_LDAP->search(LDAP::get_group_dn(), "(&(cn={$this->name})(owner=" . LDAP::get_user_dn($user->uid) . "))", array('owner'));
		return ($res->num_rows() > 0);
	}

}
?>
