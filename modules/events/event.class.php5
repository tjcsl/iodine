<?php
/**
* Just contains the definition for the class {@link Event}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Events
* @filesource
*/

/**
* Contains helper methods for the {@link Event} module
* @package modules
* @subpackage Events
*/
class Event {

	/**
	 * Cache for list of all events
	 */
	private $all_events;

	/**
	 * The id number for this event.
	 */
	private $myeid;

	private $info = [];

	/**
	 * The php magical __get method.
	 *
	 * @access public
	 * @param mixed $var The field for which to get data.
	 * @return mixed The requested data.
	 */
	public function __get($var) {
		global $I2_SQL;

		if(isset($this->info[$var])) {
			return $this->info[$var];
		}

		switch($var) {
			case 'id':
			case 'eid':
				return $this->myeid;
			case 'title':
			case 'description':
			case 'amount':
			case 'public':
			case 'startdt':
			case 'enddt':
				$this->info[$var] = $I2_SQL->query('SELECT %c FROM events WHERE id=%d', $var, $this->myeid)->fetch_single_value();
				break;
			case 'admingroups':
			case 'admins':
				$ids = $I2_SQL->query('SELECT gidoruid,id,permissions FROM event_permissions WHERE eid=%d',$this->myeid)->fetch_all_arrays(Result::ASSOC);
				$this->info['admins']=[];
				$this->info['admingroups']=[];
				foreach ($ids as $id) {
					if(($id['permissions']&1)!=0) {
						if($id['gidoruid']==0) { // gid
							$this->info['admingroups'][]=new Group($id['id']);
						} else { // uid
							$this->info['admins'][]=new User($id['id']);
						}
					}
				}
				$this->info[$var] = [];
				foreach ($uids as $uid) {
					$this->info[$var][] = new User($uid);
				}
				break;
			case 'verifiers':
				$uids = $I2_SQL->query('SELECT uid FROM event_verifiers WHERE eid=%d', $this->myeid)->fetch_col('uid');
				$this->info[$var] = [];
				foreach ($uids as $uid) {
					try {
						$verifier =  new User($uid);
					} catch(I2Exception $e) {
						$name = $I2_SQL->query('SELECT name FROM event_verifiers WHERE eid=%d AND uid=%d',$this->myeid,$uid)->fetch_single_value();
						$verifier = new FakeUser($uid,$name);
					}
					$this->info[$var][] = $verifier;
				}
				usort($this->info[$var], array('User', 'commaname_cmp'));
				break;
		}

		if(!isset($this->info[$var])) {
			throw new I2Exception('Invalid attribute passed to Event::__get(): '.$var);
		}

		return $this->info[$var];
	}

	/**
	 * The PHP magical __set method
	 *
	 * @access public
	 * @param mixed $var The field for which to set data
	 * 	Legal values are title or name, description
	 * @param mixed $val The data
	 */
	public function __set($var, $val) {
		global $I2_SQL, $I2_USER;

		if (! $this->user_is_admin($I2_USER)) {
			throw new I2Exception('You are not authorized to modify this event!');
		}

		switch ($var) {
		case 'title':
		case 'description':
		case 'amount':
		case 'public':
		case 'startdt':
		case 'enddt':
			$this->info[$var] = $val;
			$I2_SQL->query('UPDATE events SET %c=%s WHERE id=%d', $var, $val, $this->myeid);
			break;
		case 'blocks':
			$this->info[$var] = $val;
			$I2_SQL->query('DELETE FROM event_block_map WHERE eid=%d', $this->myeid);
			foreach ($val as $block) {
				$I2_SQL->query('INSERT INTO event_block_map SET eid=%d, bid=%d', $this->myeid, $block->bid);
			}
			break;
		case 'admins':
			$this->info[$var] = $val;
			$I2_SQL->query('DELETE FROM event_admins WHERE eid=%d', $this->myeid);
			foreach ($val as $admin) {
				$I2_SQL->query('INSERT INTO event_admins SET eid=%d, uid=%d', $this->myeid, $admin->uid);
			}
			break;
		case 'verifiers':
			$this->info[$var] = $val;
			$I2_SQL->query('DELETE FROM event_verifiers WHERE eid=%d', $this->myeid);
			foreach ($val as $verifier) {
				$I2_SQL->query('INSERT INTO event_verifiers SET eid=%d, uid=%d', $this->myeid, $verifier->uid);
			}
			break;
		}
	}

	/**
	 * The constructor
	 */
	public function __construct($id) {
		if (! self::event_exists($id)) {
			throw new I2Exception('Nonexistant event #'.$id.' attempted to be created');
		}
		$this->myeid = $id;
	}

	/**
	 * Add an admin for this event
	 *
	 * @param User $admin
	 */
	public function add_admin(User $admin) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if ($this->user_is_admin($admin)) {
			warn('User '.$admin.' is already an admin for this event!');
			return;
		}

		if (isset($this->info['admins'])) {
			$this->info['admins'][] = $admin;
		}

		$I2_SQL->query('INSERT INTO event_admins SET eid=%d, uid=%d', $this->myeid, $admin->uid);
	}

	/**
	 * Remove an admin for this event
	 *
	 * @param User $admin
	 */
	public function del_admin(User $admin) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if (! $this->user_is_admin($admin)) {
			warn('User '.$admin.' is not an admin for this event!');
			return;
		}

		if (isset($this->info['admins'])) {
			for ($n = 0; $n < count($this->info['admins']); $n++) {
				if ($admin->uid == $this->info['admins'][$n]->uid) {
					array_splice($this->info['admins'], $n, 0);
					break;
				}
			}
		}

		$I2_SQL->query('DELETE FROM event_admins WHERE eid=%d AND uid=%d', $this->myeid, $admin->uid);
	}

	/**
	 * Test if the user is an admin for this event
	 *
	 * @param User $user The user to test; defaults to the current user
	 */
	public function user_is_admin(User $user = NULL) {
		global $I2_SQL, $I2_USER;

		if ($user == NULL) {
			$user = $I2_USER;
		}

		if ($this->user_can_create($user)) {
			return true;
		}

		return $I2_SQL->query('SELECT COUNT(*) FROM event_admins WHERE eid=%d AND uid=%d', $this->myeid, $user->uid)->fetch_single_value() == 1;
	}

	/**
	 * Set whether any authorized person can verify payment, or if the user
	 * must select one person.
	 *
	 * NOT IMPLEMENTED
	 *
	 * @param boolean
	 */
	public function set_choose_verifier($bool) {
	}

	/**
	 * Add a verifier
	 *
	 * @param User $verifier
	 */
	public function add_verifier(User $verifier) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if ($this->user_is_verifier($verifier)) {
			warn('User '.$verifier.' is already a verifier for this event!');
			return;
		}

		if (isset($this->info['verifiers'])) {
			$this->info['verifiers'][] = $verifier;
		}

		$I2_SQL->query('INSERT INTO event_verifiers SET eid=%d, uid=%d', $this->myeid, $verifier->uid);
	}

	/**
	 * Remove a verifier
	 *
	 * @param User $verifier
	 */
	public function del_verifier(User $verifier) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if (! $this->user_is_verifier($verifier)) {
			warn('User '.$verifier.' is not a verifier for this event!');
			return;
		}

		if (isset($this->info['verifiers'])) {
			for ($n = 0; $n < count($this->info['verifiers']); $n++) {
				if ($verifier->uid == $this->info['verifiers'][$n]->uid) {
					array_splice($this->info['verifiers'], $n, 0);
					break;
				}
			}
		}

		$I2_SQL->query('DELETE FROM event_verifiers WHERE eid=%d AND uid=%d', $this->myeid, $verifier->uid);
	}

	/**
	 * Test if a user is a verifier
	 *
	 * @param User $verifier The user to test for; defaults to the current user
	 * @param User $payer Test if the verifier can verify this person's payment; if
	 * 	not given, just test to see if the person can verify at all
	 */
	public function user_is_verifier(User $verifier = NULL, User $payer = NULL) {
		global $I2_SQL, $I2_USER;

		if ($verifier == NULL) {
			$verifier = $I2_USER;
		}

		$is_verifier = $I2_SQL->query('SELECT COUNT(*) FROM event_verifiers WHERE eid=%d AND uid=%d', $this->myeid, $verifier->uid)->fetch_single_value() == 1;

		if ($payer == NULL) {
			return $is_verifier;
		} else {
			$is_user_verifier = ($I2_SQL->query('SELECT vid FROM event_signups WHERE eid=%d AND uid=%d', $this->myeid, $payer->uid)->fetch_single_value() == $verifier->uid);
			return $is_verifier && $is_user_verifier;
		}
	}

	/**
	 * Get the users a verifier has yet to verify payment for
	 *
	 * @param User $verifier Optional; defaults to the current user
	 * @return array An array of User objects
	 */
	public function verifier_users($verifier = NULL) {
		global $I2_USER, $I2_SQL;

		if ($verifier == NULL) {
			$verifier = $I2_USER;
		}

		$ret = [];
		$res = $I2_SQL->query('SELECT uid FROM event_signups WHERE vid=%d AND eid=%d AND paid=0', $verifier->uid, $this->myeid);
		foreach ($res->fetch_col('uid') as $uid) {
			$ret[] = new User($uid);
		}
		return $ret;
	}

	/**
	 * Verify payment
	 *
	 * @param User $payer The person paying for the event
	 */
	public function verify_payment(User $payer) {
		global $I2_SQL;

		if (! $this->user_is_verifier($I2_USER, $payer)) {
			throw new I2Exception("You are not authorized to mark {$payer->name} as paid for event #{$this->myeid}");
		}

		$I2_SQL->query('UPDATE event_signups SET paid=%d WHERE eid=%d AND uid=%d', 1, $this->myeid, $payer->uid);
	}

	/**
	 * Determine if a user has paid yet
	 *
	 * @TODO make the permissions on this more restrictive?
	 *
	 * @param User $payer
	 * @return boolean
	 */
	public function user_has_paid(User $payer) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT paid FROM event_signups WHERE eid=%d AND uid=%d', 1, $this->myeid, $payer->uid)->fetch_single_value() == 1;
	}

	/**
	 * Add a block offered
	 *
	 * @param EventBlock $block
	 */
	public function add_block(EventBlock $block) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if ($this->has_block($block)) {
			warn('This event will already be occurring during block #'.$block->bid);
			return;
		}

		if (isset($this->info['blocks'])) {
			$this->info['blocks'][] = $block;
		}

		$I2_SQL->query('INSERT INTO event_block_map SET eid=%d, bid=%d', $this->myeid, $block->bid);
	}

	/**
	 * Remove an offered block
	 *
	 * @param EventBlock $block
	 */
	public function del_block(EventBlock $block) {
		global $I2_SQL;

		if (! $this->user_is_admin()) {
			throw new I2Exception('You are not authorized to modify event #'.$this->myeid);
		}

		if (! $this->has_block($block)) {
			warn('This event is not scheduled block #'.$block->bid);
			return;
		}

		if (isset($this->info['blocks'])) {
			for ($n = 0; $n < count($this->info['blocks']); $n++) {
				if ($block->bid == $this->info['blocks'][$n]->bid) {
					array_splice($this->info['blocks'], $n, 0);
					break;
				}
			}
		}

		$I2_SQL->query('DELETE FROM event_block_map WHERE eid=%d AND bid=%d', $this->myeid, $block->bid);
	}

	/**
	 * Determine if the event will occur during a certain block
	 *
	 * @param EventBlock $block
	 * @return boolean
	 */
	public function has_block(EventBlock $block) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT COUNT(*) FROM event_block_map WHERE eid=%d AND bid=%d', $this->myeid, $block->bid)->fetch_single_value() == 1;
	}

	/**
	 * Test to see if a user can sign up for the event
	 *
	 * @TODO make this do more than check and see if they're already signed up
	 *
	 * @param User $user The user to test; defaults to the current user
	 * @param EventBlock $block Optional; the block to check
	 */
	public function user_can_sign_up(User $user = NULL) {
		global $I2_SQL, $I2_USER;

		if ($user == NULL) {
			$user = $I2_USER;
		}
		else {
			if ($user->uid != $I2_USER->uid && ! $this->user_is_admin($I2_USER)) {
				throw new I2Exception('You are not allowed to sign '.$user.' up for event #'.$this->myeid);
			}
		}

		if ($this->user_signed_up($user)) {
			return FALSE;
		}

		return $this->has_permission('signup',$user);
	}

	/**
	 * Sign up for an event
	 *
	 * @param EventBlock $block The block the person is signing up for
	 * @param User $verifier The person who is allowed to verify payment
	 * @param User $user The person being signed up (defaults to the current user)
	 */
	public function sign_up(User $verifier, User $user=NULL) {
		global $I2_SQL, $I2_USER;

		if ($user == NULL) {
			$user = $I2_USER;
		}

		if (! $this->user_can_sign_up($user)) {
			throw new I2Exception('You are not allowed to sign up for event #'.$this->myeid);
		}

		$I2_SQL->query('INSERT INTO event_signups SET eid=%d, bid=%d, uid=%d, vid=%d, paid=0, vname=%s', $this->myeid, $block->bid, $user->uid, $verifier->uid, $verifier->name_comma);
	}

	/**
	 * Determine if a user is signed up for an event
	 *
	 * @param User $user The user; defaults to the current user
	 * @return mixed FALSE if the user is not signed up, or the EventBlock
	 * 	the user is signed up for
	 */
	public function user_signed_up(User $user = NULL) {
		global $I2_SQL, $I2_USER;
		
		if ($user == NULL) {
			$user = $I2_USER;
		}

		$res = $I2_SQL->query('SELECT bid FROM event_signups WHERE eid=%d AND uid=%d', $this->eid, $user->uid);
		if ($res->num_rows()) {
			return new EventBlock($res->fetch_single_value());
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Get all users signed up for this event
	 *
	 * @param EventBlock $block Optional; get only users signed up for this block
	 * @return array An array of User objects 
	 */
	public function users_signed_up(EventBlock $block = NULL) {
		global $I2_SQL;

		if ($block == NULL) {
			$uids = $I2_SQL->query('SELECT uid FROM event_signups WHERE eid=%d', $this->myeid)->fetch_col('uid');
		}
		else {
			$uids = $I2_SQL->query('SELECT uid FROM event_signups WHERE eid=%d AND bid=%d', $this->myeid, $block->bid)->fetch_col('uid');
		}

		$users = [];
		foreach ($uids as $uid) {
			$users[] = new User($uid);
		}
		return $users;
	}

	/**
	 * Delete the event
	 */
	public function del_event() {
		global $I2_SQL, $I2_USER;

		if (! self::user_can_create($I2_USER)) {
			throw new I2Exception('You are not authorized to delete event #'.$this->myeid);
		}

		$I2_SQL->query('DELETE FROM events WHERE id=%d', $this->myeid);
		$I2_SQL->query('DELETE FROM event_permissions WHERE eid=%d', $this->myeid);
		$I2_SQL->query('DELETE FROM event_signups WHERE eid=%d', $this->myeid);
	}

	/**
	 * Test if a user can create an event
	 *
	 * @param User $user Defaults to the current user
	 * @return boolean
	 */
	public static function user_can_create($user = NULL) {
		global $I2_USER;
		if ($user == NULL) {
			$user = $I2_USER;
		}
		return $user->is_group_member('admin_events');
	}

	/**
	 * Create a new event
	 *
	 * Takes needed information from the associative array passed as an argument; any key/value
	 * pairs accepted by __set work.
	 * 
	 * @param string $title The title or name of the event
	 * @param array $info An associative array containing the initial description of the event
	 * @return Event An object representing the new event
	 */
	public static function create_event($title, $info = []) {
		global $I2_SQL, $I2_USER;

		if (! self::user_can_create($I2_USER)) {
			throw new I2Exception('You are not authorized to create new events!');
		}

		$id = $I2_SQL->query('INSERT INTO events SET title=%s', $title)->get_insert_id();
		$event = new Event($id);

		foreach ($info as $key => $value) {
			$event->__set($key, $value);
		}

		return $event;
	}

	/**
	 * Test if an event with the given ID number exists
	 *
	 * @param integer $eid
	 * @return boolean
	 */
	public static function event_exists($eid) {
		global $I2_SQL;
		return $I2_SQL->query('SELECT COUNT(*) FROM events WHERE id=%d', $eid)->fetch_single_value() == 1;
	}

	/**
	 * Get all available events
	 *
	 * @return array An array of all Event objects
	 */
	public static function all_events() {
		global $I2_SQL;
		$ret = [];
		$res = $I2_SQL->query('SELECT id FROM events');
		foreach ($res->fetch_col('id') as $id) {
			$ret[] = new Event($id);
		}
		return $ret;
	}

	/**
	 * Get all available events a User can see or sign up for
	 *
	 * @return array An array of all Event objects that fulfill conditions.
	 */
	public static function user_events($user=NULL) {
		global $I2_USER;
		if($user==NULL)
			$user=$I2_USER;
		$events=Event::all_events();
		$ret=[];
		foreach($events as $event) {
			if($event->has_permission('view') || $event->has_permission('signup')) {
				$ret[]=$event;
			}
		}
		return $ret;
	}

	/**
	 * Check if a user has a permission for an event
	 *
	 * @param String $perm The permission to check for
	 * @param User $user The user to check permissions for
	 * @return Boolean True if the user has the permission, False otherwise
	 */
	public function has_permission($perm,$user=NULL) {
		global $I2_USER,$I2_SQL;
		if($user==NULL)
			$user=$I2_USER;
		if(!isset($perm))
			return FALSE;
		if($user->is_group_member('admin_events'))
			return TRUE;

		$permlist=$I2_SQL->query('SELECT * FROM event_permissions WHERE eid=%d',$this->id)->fetch_all_arrays(Resut::ASSOC);
		foreach($permlist as $entry) {
			if($entry['gidoruid']==0) { //gid
				$entryhasperm=FALSE;
				switch($perm) {
					case 'admin':
						$entryhasperm=(1&$entry['permissions'])!=0;
						break;
					case 'view':
						$entryhasperm=(2&$entry['permissions'])!=0;
						break;
					case 'signup':
						$entryhasperm=(4&$entry['permissions'])!=0;
						break;
				}
				if($entryhasperm) {// If the group has the permission
					if($user->is_group_member($entry['id'])){
						return TRUE;
					}
				}
			} else { //uid
				if($entry['id']==$user->uid) {
					$entryhasperm=FALSE;
					switch($perm) {
						case 'admin':
							$entryhasperm=(1&$entry['permissions'])!=0;
							break;
						case 'view':
							$entryhasperm=(2&$entry['permissions'])!=0;
							break;
						case 'signup':
							$entryhasperm=(4&$entry['permissions'])!=0;
							break;
					}
					if($entryhasperm)
						return TRUE;
				}
			}
		}
		return FALSE;
	}
}
?>
