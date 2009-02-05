<?php
/**
* The poll class file.
* @author Joshua Cranmer <jcranmer@tjhsst.edu>
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Polls
* @filesource
*/

/**
* The class that represents a poll.
* @package modules
* @subpackage Polls
*/
class Poll {
	private $poll_id;
	private $title;

	private $begin;
	private $end;

	private $blurb;
	private $visibility;

	private $qs = array();
	private $gs = array();

	/**
	 * Vars this can get:
	 * pid, name, visible, startdt, enddt, introduction, groups, questions
	 */
	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'pid':
				return $this->poll_id;
			case 'name':
				return $this->title;
			case 'startdt':
				return $this->begin;
			case 'enddt':
				return $this->end;
			case 'introduction':
				return $this->blurb;
			case 'questions':
				return $this->qs;
			case 'visible':
				return $this->visibility;
			case 'groups':
				return $this->gs;
		}
	}

	/**
	 * Creates a Poll object with the given id.
	 * This does not modify the database in any way; it's just used for
	 * objects.
	 *
	 * @param int $pid The id to grab.
	 * @param boolean $loadq Should we load the questions into the new Poll object?
	 */
	public function __construct($pid, $loadq=TRUE) {
		global $I2_SQL,$I2_LOG;
		$pollinfo = $I2_SQL->query('SELECT name,introduction,startdt,'.
			'enddt, visible FROM polls WHERE pid=%d', $pid)->
			fetch_array(Result::ASSOC);

		$this->poll_id = $pid;
		$this->title = $pollinfo['name'];
		$this->blurb = $pollinfo['introduction'];
		$this->begin = $pollinfo['startdt'];
		$this->end = $pollinfo['enddt'];
		$this->visibility = $pollinfo['visible'] == 1 ? true : false;
		if($loadq)
			$this->load_poll_questions();

		$gs = $I2_SQL->query('SELECT * FROM poll_permissions WHERE '.
			'pid=%d',$pid)->fetch_all_arrays();
		foreach ($gs as $g) {
			$this->gs[$g['gid']] = array($g['vote'], $g['modify'],
				$g['results']);
		}
	}
	
	/**
	 * Loads questions associated with the Poll calling it.
	 */
	function load_poll_questions() {
		global $I2_SQL;

		$qs = $I2_SQL->query('SELECT qid FROM poll_questions WHERE '.
			'pid=%d',$this->pid)->fetch_all_single_values();
		foreach ($qs as $q) {
			$this->qs[$q] = new PollQuestion($this->pid, $q);
		}
	}

	/**
	 * Creates a new poll.
	 *
	 * @param string $name The name of the poll to add
	 * @param string $intro An introduction to the poll
	 * @param string $begin The start datetime
	 * @param string $end The end datetime
	 * @param boolean $visible If it is visible
	 *
	 * @return Poll The new poll
	 */
	public static function add_poll($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;

		$pid = $I2_SQL->query('INSERT INTO polls SET name=%s, '.
			'introduction=%s, startdt=%s, enddt=%s, visible=%d',
			$name, $intro, $begin, $end, $visible)->get_insert_id();

		return new Poll($pid);
	}

	/**
	 * Updates the poll with the new variables.
	 *
	 * @param string $name The name of the poll
	 * @param string $intro An introduction to the poll
	 * @param string $begin The start datetime
	 * @param string $end The end datetime
	 * @param boolean $visible If it is visible
	 */
	public function edit_poll($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;
		$I2_SQL->query('UPDATE polls SET name=%s, introduction=%s,'.
			'startdt=%s, enddt=%s, visible=%d WHERE pid=%d',
			$name, $intro, $begin, $end, $visible, $this->poll_id);
		$this->title = $name;
		$this->blurb = $intro;
		$this->startdt = $begin;
		$this->enddt = $end;
		$this->visibility = $visible;
	}

	/**
	 * Deletes the poll with given pid
	 *
	 * @param int $pid The poll id
	 */
	public static function delete_poll($pid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM polls WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_questions WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_permissions WHERE pid=%d',
			$pid);
		$I2_SQL->query('DELETE FROM poll_responses WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_votes WHERE pid=%d', $pid);
	}

	/**
	 * Returns all the polls
	 *
	 * The format of the array is an unindexed list of polls
	 *
	 * @param boolean $loadq Should we load the questions associated with the Poll object?
	 * @return array Aforementioned array
	 */
	public static function all_polls($loadq=TRUE) {
		global $I2_SQL;

		$pids = $I2_SQL->query('SELECT pid FROM polls ORDER BY pid'.
			' DESC')->fetch_all_single_values();
		$polls = array();
		foreach ($pids as $pid) {
			$polls[] = new Poll($pid,$loadq);
		}
		return $polls;
	}

	/**
	 * Returns all polls that the user can see.
	 *
	 * @param boolean $loadq Should we load the questions associated with the Poll object?
	 * @return array all_polls(), including only what I can see
	 */
	public static function accessible_polls($loadq=TRUE) {
		global $I2_USER, $I2_SQL;

		$polls = Poll::all_polls($loadq);
//		$gs = $I2_SQL->query('SELECT * FROM poll_permissions')->fetch_all_arrays();
		$ugroups = Group::get_user_groups($I2_USER);
		foreach($polls as $p) {
			foreach($ugroups as $g) {
				if(isset($p->gs[$g->gid]))
					$out[] = $p;
			}
		}
		return $out;
	}

	/**
	 * Returns whether or not the current user can see the poll.
	 * Poll admins are, of course, omniscient in this regard. Otherwise, if
	 * the poll is not visible, it returns FALSE. Finally, it will return
	 * true if the user is a member of a group with some sort of permission
	 * on this poll (be it modify,vote, or view results).
	 *
	 * @return boolean TRUE or FALSE according to above
	 */
	public function can_see() {
		global $I2_USER;

		if ($I2_USER->is_group_member('admin_polls'))
			return TRUE;
		if (!$this->visibility)
			return FALSE;

		$ugroups = Group::get_user_groups($I2_USER);
		foreach ($ugroups as $g) {
			if (isset($this->gs[$g->gid]))
				return TRUE;
		}
		return FALSE;
	}

	/**
	 * Adds a question with given semantics to this poll.
	 * This method should be the only method used in the creation of a
	 * question for a poll. The qid parameter is used to be able to request
	 * a given parameter.
	 *
	 * @param string name The text of the question
	 * @param string type The type of the question
	 * @param integer maxvotes The maximum number of votes one can give
	 * for a question.
	 * @param integer qid The qid (if any) that the caller requests.
	 */
	public function add_question($name, $type, $maxvotes, $qid = NULL) {
		global $I2_SQL;
		$q = PollQuestion::new_question($this->poll_id, $name, $type,
			$maxvotes, $qid);
		$this->qs[$q->qid] = $q;
	}

	/**
	 * Deletes the given question.
	 *
	 * @param integer qid The question to delte
	 */
	public function delete_question($qid) {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM poll_questions WHERE pid=%d AND '.
			'qid=%d', $this->poll_id,$qid);
		unset($this->qs[$qid]);
	}

	/**
	 * Adds the group id with the given permissions.
	 *
	 * The permissions is an array of booleans, with the first index the
	 * <kbd>vote</kbd> permission, followed by <kbd>modify</kbd> and
	 * <kbd>results</kbd>.
	 *
	 * @param integer gid The group id
	 * @param array perm An array of permissions stipulated above.
	 */
	public function add_group_id($gid, $perm = array(TRUE,FALSE,FALSE)) {
		global $I2_SQL;

		if ($gid != -1)
			$I2_SQL->query('INSERT INTO poll_permissions SET pid=%d, '.
				'gid=%d, vote=%d, modify=%d, results=%d ',
				$this->poll_id,$gid, $perm[0], $perm[1], $perm[2]);
		$this->gs[$gid] = $perm;
	}

	/**
	 * Edits the group id with the given permissions.
	 *
	 * The permissions is an array of booleans, with the first index the
	 * <kbd>vote</kbd> permission, followed by <kbd>modify</kbd> and
	 * <kbd>results</kbd>.
	 *
	 * @param integer gid The group id
	 * @param array perm An array of permissions stipulated above.
	 */
	public function edit_group_id($gid, $perm) {
		global $I2_SQL;

		$I2_SQL->query('UPDATE poll_permissions SET vote=%d, modify=%d'.
		       ', results=%d WHERE pid=%d AND gid=%d',
			$perm[0], $perm[1], $perm[2], $this->poll_id, $gid);
		$this->gs[$gid] = $perm;
	}

	/**
	 * Removes the group's permissions.
	 *
	 * @param integer gid The group id
	 */
	public function remove_group_id($gid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM poll_permissions WHERE pid=%d AND '.
		       'gid=%d',$this->poll_id,$gid);
		unset($this->gs[$gid]);
	}

	/**
	 * Checks to see if the user can do the said action on said poll.
	 *
	 * Note that members of the group admin_polls are omnipotent in this
	 * regard. Also note that there is no checking of permissions in this
	 * class except through the manual call to this in the poll
	 * initialization.
	 *
	 * As of right now, only omnipotent beings can add polls.
	 *
	 * Method/MySQL database mappings:<ul>
	 * <li><kbd>vote</kbd>:&nbsp;&nbsp;vote</li>
	 * <li><kbd>edit</kbd>,<kbd>delete</kbd>:&nbsp;&nbsp;modify</li>
	 * <li><kbd>results</kbd>,<kbd>export_csv</kbd>:&nbsp;&nbsp;results</li>
	 * </ul>
	 *
	 * @param int $pid The poll id
	 * @param string $action The action (a function of polls)
	 *
	 * @return boolean Whether or not the person can do the action.
	 * @see Polls
	 */
	public static function can_do($pid, $action) {
		global $I2_USER, $I2_SQL;

		if ($I2_USER->is_group_member('admin_polls')) {
			return TRUE; // polls admins are implicitly omnipotent
		}

		switch ($action) {
		case 'home':
			return TRUE; // Anyone can view the home
		case 'add':
			return FALSE; // Mere mortals can't add anything
		case 'edit':
		case 'delete':
			$action = 'modify';
			break;
		case 'export_csv':
			$action = 'results';
			break;
		case 'vote':
		case 'results':
			break;
		default:
			throw new I2_Exception('Illegal action '.
				$action.' for polls permissions!');
		}
		$groups = $I2_SQL->query('SELECT * FROM poll_permissions WHERE '.
			'pid=%d',$pid)->fetch_all_arrays();
		$ugroups = Group::get_user_groups($I2_USER);
		foreach ($groups as $g) {
			if ($g[$action]) {
				if (in_array(new Group($g['gid']),$ugroups))
					return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Returns whether or not the poll is currently going on.
	 */
	public function in_session() {
		return strtotime($this->begin) < time() &&
			strtotime($this->end) > time();
	}

	/**
	 * Caches the grade/gender into the poll.
	 */
	public function cache_ldap() {
		global $I2_SQL, $I2_USER;

		$I2_SQL->query('UPDATE poll_votes SET grade=%s, gender=%s'.
			' WHERE uid=%d AND pid=%d',$I2_USER->grade,
			$I2_USER->gender,$I2_USER->uid,$this->poll_id);
	}
}
?>
