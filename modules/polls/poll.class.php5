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

	public function __construct($pid) {
		global $I2_SQL,$I2_LOG;

		$pollinfo = $I2_SQL->query('SELECT name, introduction, startdt, enddt, visible FROM polls WHERE pid=%d', $pid)->fetch_array(Result::ASSOC);

		$this->poll_id = $pid;
		$this->title = $pollinfo['name'];
		$this->blurb = $pollinfo['introduction'];
		$this->begin = $pollinfo['startdt'];
		$this->end = $pollinfo['enddt'];
		$this->visibility = $pollinfo['visible'] == 1 ? true : false;

		$qs = $I2_SQL->query('SELECT qid FROM poll_questions WHERE pid=%d',$pid)->fetch_all_single_values();
		foreach ($qs as $q) {
			$this->qs[$q] = new PollQuestion($pid, $q);
		}

		$gs = $I2_SQL->query('SELECT * FROM poll_permissions WHERE pid=%d',$pid)->fetch_all_arrays();
		foreach ($gs as $g) {
			$this->gs[$g['gid']] = array($g['vote'],$g['modify'],$g['results']);
		}
	}

	/**
	* Creates a new poll.
	*
	* @param string $name The name of the poll to add
	* @param string $intro An introduction to the poll
	*
	* @return Poll The new poll
	*/
	public static function add_poll($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;

		$pid = $I2_SQL->query('INSERT INTO polls SET name=%s, introduction=%s, startdt=%s, enddt=%s, visible=%d',
			$name, $intro, $begin, $end, $visible)->get_insert_id();

		return new Poll($pid);
	}

	public function edit_poll($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;
		$I2_SQL->query('UPDATE polls SET name=%s, introduction=%s, startdt=%s, enddt=%s, visible=%d WHERE pid=%d',
			$name, $intro, $begin, $end, $visible, $this->poll_id);
		$this->title = $name;
		$this->blurb = $intro;
		$this->startdt = $begin;
		$this->enddt = $end;
		$this->visibility = $visible;
	}

	public static function delete_poll($pid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM polls WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_questions WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_permissions WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_responses WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM poll_votes WHERE pid=%d', $pid);
	}

	public static function all_polls() {
		global $I2_SQL;

		$pids = $I2_SQL->query('SELECT pid FROM polls ORDER BY pid DESC')->fetch_all_single_values();
		$polls = array();
		foreach ($pids as $pid) {
			$polls[] = new Poll($pid);
		}
		return $polls;
	}

	public static function accessible_polls() {
		global $I2_USER;

		$polls = Poll::all_polls();
		foreach ($polls as $p) {
			if ($p->can_see())
				$out[] = $p;
		}
		return $out;
	}

	public function can_see() {
		global $I2_USER;

		$ugroups = Group::get_user_groups($I2_USER);
		foreach ($ugroups as $g) {
			if (isset($this->gs[$g->gid]))
				return TRUE;
		}
		return FALSE;
	}

	public function add_question($name, $type, $maxvotes, $qid = NULL) {
		global $I2_SQL;
		$q = PollQuestion::new_question($this->poll_id,$name,$type,$maxvotes,$qid);
		$this->qs[$q->qid] = $q;
	}

	public function delete_question($qid) {
		global $I2_SQL;
		$I2_SQL->query('DELETE FROM poll_questions WHERE pid=%d AND qid=%d', $this->poll_id,$qid);
		unset($this->qs[$qid]);
	}

	public function add_group_id($gid, $perm) {
		global $I2_SQL;

		$I2_SQL->query('INSERT INTO poll_permissions SET pid=%d,gid=%d', $this->poll_id,$gid);
		$this->gs[$gid] = array(TRUE,FALSE,FALSE);
	}

	public function remove_group_id($gid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM poll_permissions WHERE pid=%d AND gid=%d',$this->poll_id,$gid);
		unset($this->gs[$gid]);
	}

	public static function can_do($pid, $action) {
		global $I2_USER, $I2_SQL;

		if ($I2_USER->is_group_member('admin_polls')) {
			return TRUE; // polls admins are implicitly omnipotent
		}

		switch ($action) {
		case 'home':
			return TRUE; // Anyone can view the home
		case 'add':
			return FALSE; // Non-omnipotent beings can't add anything
		case 'vote':
			$action = 0;
			break;
		case 'edit':
		case 'delete':
			$action = 1;
			break;
		case 'results':
		case 'export_csv':
			$action = 2;
			break;
		default:
			throw new I2_Exception("Illegal action $action for polls permissions!");
		}
		$groups = $I2_SQL->query('SELECT * FROM poll_permissions WHERE pid=%d',$pid)->fetch_all_arrays();
		$ugroups = Group::get_user_groups($I2_USER);
		foreach ($groups as $g) {
			if ($g[$action]) {
				if (in_array(new Group($g['gid']),$ugroups))
					return TRUE;
			}
		}
		return FALSE;
	}

	public function in_session() {
		return strtotime($this->begin) < time() && strtotime($this->end) > time();
	}
}
?>
