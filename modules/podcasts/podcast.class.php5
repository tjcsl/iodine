<?php
/**
* The podcast class file.
* @author Derek Morris
* @copyright 2007 The Intranet 2 Development Team
* @package modules
* @subpackage Podcast
* @filesource
*/

/**
* The class that represents a podcast.
* @package modules
* @subpackage Podcasts
*/
class Podcast {
	private $podcast_id;
	private $title;

	private $begin;
	private $end;

	private $blurb;
	private $visibility;

	private $qs = [];
	private $gs = [];

	private $movie;

	/**
	 * Vars this can get:
	 * pid, name, visible, startdt, enddt, introduction, groups, questions, movie(url)
	 */
	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'pid':
				return $this->podcast_id;
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
			case 'movie':
				return $this->movie;
		}
	}

	/**
	 * Creates a Podcast object with the given id.
	 * This does not modify the database in any way; it's just used for
	 * objects.
	 *
	 * @param int $pid The id to grab.
	 * @param boolean $loadq Should we load the questions into the new Podcast object?
	 */
	public function __construct($pid, $loadq=TRUE) {
		global $I2_SQL,$I2_LOG;

		$podcastinfo = $I2_SQL->query('SELECT name,introduction,startdt,'.
			'enddt, visible FROM podcasts WHERE pid=%d', $pid)->
			fetch_array(Result::ASSOC);

		$this->podcast_id = $pid;
		$this->title = $podcastinfo['name'];
		$this->blurb = $podcastinfo['introduction'];
		$this->begin = $podcastinfo['startdt'];
		$this->end = $podcastinfo['enddt'];
		$this->visibility = $podcastinfo['visible'] == 1 ? true : false;

		if($loadq) {
			$this->load_podcast_questions();
		}

		$gs = $I2_SQL->query('SELECT * FROM podcast_permissions WHERE '.
			'pid=%d',$pid)->fetch_all_arrays();
		foreach ($gs as $g) {
			$this->gs[$g['gid']] = array($g['vote'], $g['modify'],
				$g['results']);
		}
	}

	/**
	 * Loads questions associated with the Podcast calling it.
	 */
	function load_podcast_questions() {
		global $I2_SQL;

		$qs = $I2_SQL->query('SELECT qid FROM podcast_questions WHERE '.
			'pid=%d',$this->pid)->fetch_all_single_values();
		foreach ($qs as $q) {
			$this->qs[$q] = new PodcastQuestion($this->pid, $q);
		}
	 }

	/**
	 * Creates a new podcast.
	 *
	 * @param string $name The name of the podcast to add
	 * @param string $intro An introduction to the podcast
	 * @param string $begin The start datetime
	 * @param string $end The end datetime
	 * @param boolean $visible If it is visible
	 *
	 * @return Podcast The new podcast
	 */
	public static function add_podcast($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;

		$pid = $I2_SQL->query('INSERT INTO podcasts SET name=%s, '.
			'introduction=%s, startdt=%s, enddt=%s, visible=%d',
			$name, $intro, $begin, $end, $visible)->get_insert_id();

		return new Podcast($pid);
	}

	/**
	 * Updates the podcast with the new variables.
	 *
	 * @param string $name The name of the podcast
	 * @param string $intro An introduction to the podcast
	 * @param string $begin The start datetime
	 * @param string $end The end datetime
	 * @param boolean $visible If it is visible
	 */
	public function edit_podcast($name, $intro, $begin, $end, $visible) {
		global $I2_SQL;
		$I2_SQL->query('UPDATE podcasts SET name=%s, introduction=%s,'.
			'startdt=%s, enddt=%s, visible=%d WHERE pid=%d',
			$name, $intro, $begin, $end, $visible, $this->podcast_id);
		$this->title = $name;
		$this->blurb = $intro;
		$this->startdt = $begin;
		$this->enddt = $end;
		$this->visibility = $visible;
	}

	/**
	 * Deletes the podcast with given pid
	 *
	 * @param int $pid The podcast id
	 */
	public static function delete_podcast($pid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM podcasts WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM podcast_questions WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM podcast_permissions WHERE pid=%d',
			$pid);
		$I2_SQL->query('DELETE FROM podcast_responses WHERE pid=%d', $pid);
		$I2_SQL->query('DELETE FROM podcast_votes WHERE pid=%d', $pid);
	}

	/**
	 * Returns all the podcasts
	 *
	 * The format of the array is an unindexed list of podcasts
	 *
	 * @param boolean $loadq Should we load the questions associated with the Podcast objects?
	 * @return array Aforementioned array
	 */
	public static function all_podcasts($loadq=TRUE) {
		global $I2_SQL;

		$pids = $I2_SQL->query('SELECT pid FROM podcasts ORDER BY pid'.
			' DESC')->fetch_all_single_values();
		$podcasts = [];
		foreach ($pids as $pid) {
			$podcasts[] = new Podcast($pid,$loadq);
		}
		return $podcasts;
	}

	/**
	 * Returns all podcasts that the user can see.
	 *
	 * @param boolean $loadq Should we load the questions associated with the Podcast objects?
	 * @return array all_podcasts(), including only what I can see
	 */
	public static function accessible_podcasts($loadq=TRUE) {
		global $I2_USER, $I2_SQL;

		$podcasts = Podcast::all_podcasts($loadq);
//		$gs = $I2_SQL->query('SELECT * FROM podcast_permissions')->fetch_all_arrays();
		if($I2_USER->is_group_member('admin_podcasts'))
			return $podcasts;
		$ugroups = Group::get_user_groups($I2_USER);
		foreach($podcasts as $p) {
			foreach($ugroups as $g) {
				if(isset($p->gs[$g->gid])) {
					$out[] = $p;
					break;
				}
			}
		}
		return $out;
	}

	/**
	 * Returns whether or not the current user can see the podcast.
	 * Podcast admins are, of course, omniscient in this regard. Otherwise, if
	 * the podcast is not visible, it returns FALSE. Finally, it will return
	 * true if the user is a member of a group with some sort of permission
	 * on this podcast (be it modify,vote, or view results).
	 *
	 * @return boolean TRUE or FALSE according to above
	 */
	public function can_see() {
		global $I2_USER;

		if ($I2_USER->is_group_member('admin_podcasts'))
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
	 * Adds a question with given semantics to this podcast.
	 * This method should be the only method used in the creation of a
	 * question for a podcast. The qid parameter is used to be able to request
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
		$q = PodcastQuestion::new_question($this->podcast_id, $name, $type,
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
		$I2_SQL->query('DELETE FROM podcast_questions WHERE pid=%d AND '.
			'qid=%d', $this->podcast_id,$qid);
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
			$I2_SQL->query('INSERT INTO podcast_permissions SET pid=%d, '.
				'gid=%d, vote=%d, modify=%d, results=%d ',
				$this->podcast_id,$gid, $perm[0], $perm[1], $perm[2]);
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

		$I2_SQL->query('UPDATE podcast_permissions SET vote=%d, modify=%d'.
		       ', results=%d WHERE pid=%d AND gid=%d',
			$perm[0], $perm[1], $perm[2], $this->podcast_id, $gid);
		$this->gs[$gid] = $perm;
	}

	/**
	 * Removes the group's permissions.
	 *
	 * @param integer gid The group id
	 */
	public function remove_group_id($gid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM podcast_permissions WHERE pid=%d AND '.
		       'gid=%d',$this->podcast_id,$gid);
		unset($this->gs[$gid]);
	}

	/**
	 * Checks to see if the user can do the said action on said podcast.
	 *
	 * Note that members of the group admin_podcasts are omnipotent in this
	 * regard. Also note that there is no checking of permissions in this
	 * class except through the manual call to this in the podcast
	 * initialization.
	 *
	 * As of right now, only omnipotent beings can add podcasts.
	 *
	 * Method/MySQL database mappings:<ul>
	 * <li><kbd>vote</kbd>:&nbsp;&nbsp;vote</li>
	 * <li><kbd>edit</kbd>,<kbd>delete</kbd>:&nbsp;&nbsp;modify</li>
	 * <li><kbd>results</kbd>,<kbd>export_csv</kbd>:&nbsp;&nbsp;results</li>
	 * </ul>
	 *
	 * @param int $pid The podcast id
	 * @param string $action The action (a function of podcasts)
	 *
	 * @return boolean Whether or not the person can do the action.
	 * @see Podcasts
	 */
	public static function can_do($pid, $action) {
		global $I2_USER, $I2_SQL;

		if ($I2_USER->is_group_member('admin_podcasts')) {
			return TRUE; // podcasts admins are implicitly omnipotent
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
				$action.' for podcasts permissions!');
		}
		$groups = $I2_SQL->query('SELECT * FROM podcast_permissions WHERE '.
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
	 * Returns whether or not the podcast is currently going on.
	 */
	public function in_session() {
		return strtotime($this->begin) < time() &&
			strtotime($this->end) > time();
	}

	/**
	 * Caches the grade/gender into the podcast.
	 */
	public function cache_ldap() {
		global $I2_SQL, $I2_USER;

		$I2_SQL->query('UPDATE podcast_votes SET grade=%s, gender=%s'.
			' WHERE uid=%d AND pid=%d',$I2_USER->grade,
			$I2_USER->gender,$I2_USER->uid,$this->podcast_id);
	}
}
?>
