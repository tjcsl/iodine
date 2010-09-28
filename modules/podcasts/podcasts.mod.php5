<?php
/**
 * The podcastss module file.
 * @author Derek Morris
 * @copyright 2007 The Intranet 2 Development Team
 * @package modules
 * @subpackage Podcasts
 * @filesource
 */

/**
 * The podcasts module itself.
 * Used to distribute the administration's podcasts.
 * @package modules
 * @subpackage Podcasts
 */
class Podcasts implements Module {

	/** The template to use. */
	private $template;
	/** Arguments for said template. */
	private $template_args = array();

	/**
	* Unused; Not supported for this module.
	* Should be implemented, many will access this module from a mobile device.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	* Should be implemented, many will access this module from a mobile device.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	 * Initalizes the pane.
	 * Steps in order:
	 * @ No arguments -> home
	 * @ Not accessible -> home
	 * @ Calls the method dynamically.
	 */
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1)
			$I2_ARGS[1] = 'home';

		$method = $I2_ARGS[1];

		if (method_exists($this, $method)) {
			if (isset($I2_ARGS[2])) {
				if (!Podcast::can_do($I2_ARGS[2], $method)) {
					redirect('podcasts');
				}
			} else {
				if (!Podcast::can_do(0, $method)) {
					redirect('podcasts');
				}
			}
			$this->$method();
			return 'Podcasts: ' . ucwords(strtr($method, '_', ' '));
		} else {
			redirect('podcasts');
		}
	}

	/**
	 * Displays the pane.
	 */
	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	 * Initializes the intranet box.
	 */
	function init_box() {
		$podcasts = Podcast::accessible_podcasts(FALSE);
		$open = array();
		$time = time();
		foreach($podcasts as $podcast) {
			if(strtotime($podcast->startdt) < $time && strtotime($podcast->enddt) > $time && $podcast->visible)
				$open[] = $podcast;
		}
		$this->template_args['open'] = $open;
		$num = count($open);
		return 'Podcasts: '.$num.' active podcast'.($num==1?'':'s');
	}

	/**
	 * Displays the intranet box.
	 */
	function display_box($display) {
		$display->disp('podcasts_box.tpl',$this->template_args);
	}

	/**
	 * Returns the name.
	 */
	function get_name() {
		return 'I2 Podcasts';
	}

	/**
	 * Returns false.
	 */
	function is_intrabox() {
		return false;
	}

	/////////////
	// METHODS //
	/////////////

	/**
	 * The method that loads the home page.
	 * Nothing really special; just a list of all accessible podcasts.
	 */	
	function home() {
		global $I2_USER;

		$podcasts = Podcast::accessible_podcasts(FALSE);
		$open = array();
		$finished = array();
		$unstarted = array();
		$time = time();
		foreach ($podcasts as $podcast) {
			if (strtotime($podcast->startdt) > $time)
				$unstarted[] = $podcast;
			else if (strtotime($podcast->enddt) > $time)
				$open[] = $podcast;
			else
				$finished[] = $podcast;
		}
		$this->template_args['finished'] = $finished;
		$this->template_args['unstarted'] = $unstarted;
		$this->template_args['open'] = $open;
		if ($I2_USER->is_group_member('admin_podcasts')) {
			$this->template_args['admin'] = 1;
		}
		$this->template = 'podcasts_home.tpl';
	}

	/**
	 * The method that handles adding new podcasts.
	 * It does basic stuff and then passes the baton to the edit page.
	 */
	function add() {
		global $I2_USER, $I2_ARGS;
	
		$this->template_args['groups'] = Group::get_all_groups();
		if (count($_POST) > 0) {
			$name = $_POST['name'];
			$begin = $_POST['startdt'];
			$end = $_POST['enddt'];
			$blurb = $_POST['intro'];
			$on = isset($_POST['visible']) &&
				$_POST['visible'] == 'on' ? 1 : 0;

			$p = Podcast::add_podcast($name, $blurb, $begin, $end, $on);

			$_POST['groups'] = array_unique($_POST['groups']);
                        foreach ($_POST['groups'] as $key => $id) {
				$g = $_POST['group_gids'][$id];
				$p->add_group_id($g, array(
					isset($_POST['vote'][$id])?1:0,
					isset($_POST['modify'][$id])?1:0,
					isset($_POST['results'][$id])?1:0
				));
			}
			$_POST = array(); // Unset post vars
			$I2_ARGS[2] = $p->pid;
			$this->edit();
		} else {
			$this->template = 'podcasts_add.tpl';
		}
	}

	/**
	 * The method that handles editing of podcasts.
	 * $I2_ARGS[2] represents the podcast to edit (like most of the other
	 * methods), however $I2_ARGS[3] is special. If $I2_ARGS[3] exists, then
	 * it represents a non-javascript environment and performs special
	 * functions (either inserting dummy values or deleting questions/
	 * answers/etc.)
	 */
	function edit() {
		global $I2_USER, $I2_ARGS;
		$podcast = new Podcast($I2_ARGS[2]);
		if (isset($I2_ARGS[3])) {
			// $I2_ARGS[3] represents a non-javascript environment
			// Therefore, we perform actions based on its value
			switch ($I2_ARGS[3]) {
			case 'addq':
				$podcast->add_question('','standard',0);
				break;
			case 'delq':
				$podcast->delete_question($I2_ARGS[4]);
				break;
			case 'adda':
				$podcast->questions[$I2_ARGS[4]]->add_answer('');
				break;
			case 'dela':
				$podcast->questions[$I2_ARGS[4]]->delete_answer(
					$I2_ARGS[5]);
				break;
			case 'addg':
				$groups = Group::get_all_groups();
				$podcast->add_group_id(-1);
				break;
			case 'delg':
				// -1 id means no groups actually exist
				if ($I2_ARGS[4] != -1) {
					$podcast->remove_group_id($I2_ARGS[4]);
				}
				break;
			}
		}
		if (isset($_POST['podcast_edit_form'])) {
			$on = isset($_POST['visible']) &&
				$_POST['visible'] == 'on' ? 1 : 0;
			$podcast->edit_podcast($_POST['name'],$_POST['intro'],
				$_POST['startdt'],$_POST['enddt'],$on);
			$seen = array();
			foreach($_POST['question'] as $id) {
				$name = $_POST["q_{$id}_name"];
				$type = $_POST["q_{$id}_type"];
				$maxv = $_POST["q_{$id}_lim"];
				if ($maxv == '')
					$maxv = 0;
				if (isset($podcast->questions[$id])) {
					$podcast->questions[$id]->edit_question(
						$name, $type, $maxv);
				} else {
					$podcast->add_question($name, $type, $maxv,
						$id);
				}
				$seen[] = $id;
				if (isset($_POST['a_'.$id])) {
					$a_seen = array();
					$q = $podcast->questions[$id];
					foreach($_POST['a_'.$id] as $aid) {
						$v = $_POST["a_{$id}_{$aid}"];
						if (isset($q->answers[$aid])) {
						       $q->edit_answer($v,$aid);
						} else {
						       $q->add_answer($v,$aid);
						}
						$a_seen[] = $aid;
					}
					$as = $q->answers;
					foreach ($as as $a => $dummy) {
						if (!in_array($a,$a_seen))
							$q->delete_answer($a);
					}
				}
			}
			$qs = $podcast->questions;
			foreach ($qs as $q) {
				if (!in_array($q->qid,$seen)) {
					$podcast->delete_question($q->qid);
				}
			}

			if (!array_key_exists('groups', $_POST)) {
				// Someone deleted all the groups.
				// We'll just fake that something exists
				$_POST['groups'] = array();
			}
			$seen = array();
			$gs = $podcast->groups;
			foreach ($_POST['groups'] as $key => $id) {
				$g = $_POST['group_gids'][$id];
				if (isset($gs[$g])) {
					$podcast->edit_group_id($g, array(
						isset($_POST['vote'][$id])?1:0,
						isset($_POST['modify'][$id])?1:0,
						isset($_POST['results'][$id])?1:0
						));
				} else {
					$podcast->add_group_id($g,	array(
						isset($_POST['vote'][$id])?1:0,
						isset($_POST['modify'][$id])?1:0,
						isset($_POST['results'][$id])?1:0
						));
				}
				$seen[] = $g;
			}
			foreach ($gs as $gid => $perms) {
				if (!in_array($gid, $seen))
					$podcast->remove_group_id($gid);
			}
		}
		$this->template = 'podcasts_edit.tpl';
		$this->template_args['podcast'] = $podcast;
		$this->template_args['types'] = PodcastQuestion::get_answer_types();
		$this->template_args['groups'] = Group::get_all_groups();
	}

	/**
	 * The method that handles deletion of podcasts.
	 */
	function delete() {
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if (! isset($I2_ARGS[2])) {
			$this->home();
		}

		$pid = $I2_ARGS[2];
		$name = $I2_SQL->query('SELECT name FROM podcasts WHERE pid=%d', $pid)->fetch_single_value();
		$this->template_args['podcastname'] = $name;

		if (isset($_REQUEST['podcasts_delete_form'])) {
			if ($_REQUEST['podcasts_delete_form'] == 'delete_podcast') {
				Podcast::delete_podcast($pid);
				$this->template_args['deleted'] = TRUE;
			}
		}
		$this->template = 'podcasts_delete.tpl';
	}

	/**
	 * The method that handles the actual voting in podcasts.
	 * This method directly makes one MySQL call: finding out if the user
	 * has voted.
	 */
	function vote() {
		global $I2_ARGS, $I2_SQL, $I2_USER;

		if (!isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		
		$podcast = new Podcast($I2_ARGS[2]);
		if (isset($_POST['podcasts_vote_form'])) {
			$uid = $I2_USER->uid;
			$qs = $podcast->questions;
			foreach ($qs as $q) {
				$q->delete_vote($uid);
				if (isset($_POST[$q->qid])) {
					if ($q->maxvotes > 0 && count($_POST[$q->qid]) > $q->maxvotes) {
						$i = 0;
						$arr = array();
						foreach ($ans as $key=>$val) {
							if ($i == $q->maxvotes)
								break;
							$arr[$key] = $val;
							$i += 1;
						}
						$_POST[$q->qid] = $ans;
					}
					if ($q->answertype == 'free_response' &&
						strlen($_POST[$q->qid]) == 0)
						continue;
					$q->vote($_POST[$q->qid],$uid);
				}
			}
			$podcast->cache_ldap();
			$this->template = 'podcasts_voted.tpl';
			return;
		}
		$this->template_args['podcast'] = $podcast;
		$this->template_args['avail'] = $podcast->in_session();
		$this->template_args['has_voted'] = $I2_SQL->query('SELECT '.
			'COUNT(*) FROM podcast_votes WHERE pid=%d AND uid=%d',
			$podcast->pid,$I2_USER->uid)->fetch_single_value() > 0;
		$this->template = 'podcasts_vote.tpl';
	}

	/**
	 * The method that returns the results of the podcast.
	 * I know that the code looks ugly, but most of it is due to the fact
	 * that there are so many aggregate data values to compute.
	 */
	function results() {
		global $I2_ARGS;

		if (!isset($I2_ARGS[2])) {
			$this->home();
			return;
		}

		$podcast = new Podcast($I2_ARGS[2]);

		if (isset($I2_ARGS[3])) {
			$I2_ARGS[3] = substr($I2_ARGS[3],1);
			$question = $podcast->questions[$I2_ARGS[3]];

			$this->template_args['podcast'] = $podcast;
			$this->template_args['question'] = $question->question;
			$this->template_args['votes'] = $question->
				get_all_votes();
			$this->template = 'podcasts_results_freeresponse.tpl';
			return;
		}
		$qs = array();
		$questions = $podcast->questions;
		foreach($questions as $question) {
			$q = array();
			$q['answertype'] = $question->answertype;
			$q['text'] = $question->question;
			$q['qid'] = $question->qid;

			$q['total'] = array();
			$q['total']['T'] = 0;
			$q['total']['M'] = 0;
			$q['total']['F'] = 0;
			foreach (array(9,10,11,12) as $g) {
				$q['total'][$g.'T'] = 0;
				$q['total'][$g.'M'] = 0;
				$q['total'][$g.'F'] = 0;
			}
			$q['total']['staffT'] = 0;
			$q['answers'] = array();
			$as = $question->answers;
			foreach ($as as $aid => $text) {
				$ans = array();
				$ans['text'] = $text;

				$ans['votes'] = array();
				$ans['votes']['T'] = 0;
				$ans['votes']['M'] = 0;
				$ans['votes']['F'] = 0;
				foreach (array(9,10,11,12) as $g) {
					$ans['votes'][$g.'M'] = $question->
						num_who_vote($aid,$g,'M');
					$ans['votes'][$g.'F'] = $question->
						num_who_vote($aid,$g,'F');
					$ans['votes'][$g.'T'] = $ans['votes'][
						$g.'M']+$ans['votes'][$g.'F'];
					$ans['votes']['T'] += $ans['votes'][
						$g.'T'];
					$ans['votes']['M'] += $ans['votes'][
						$g.'M'];
					$ans['votes']['F'] += $ans['votes'][
						$g.'F'];
					$q['total'][$g.'T'] += $ans['votes'][
						$g.'T'];
					$q['total'][$g.'M'] += $ans['votes'][
						$g.'M'];
					$q['total'][$g.'F'] += $ans['votes'][
						$g.'F'];
				}
				$ans['votes']['staffT'] = $question->
					num_who_vote($aid,'STAFF',NULL);
				$ans['votes']['T'] += $ans['votes']['staffT'];
				$ans['percent'] = "NA";
				$q['total']['staffT'] +=$ans['votes']['staffT'];
				$q['total']['M'] += $ans['votes']['M'];
				$q['total']['F'] += $ans['votes']['F'];
				$q['total']['T'] += $ans['votes']['T'];
				$q['answers'][] = $ans;
			}
			$q['voters'] = $question->num_who_voted();
			foreach ($q['answers'] as $key => $response) {
				if ($q['voters'] == 0)
					if ($q['answertype'] == 'standard')
						$q['answers'][$key]['percent'] =
							"NA";
					else
						$q['answers'][$key]['percent'] =
							"0.00";
				else 
					$q['answers'][$key]['percent'] =
						sprintf("%.2f",$response[
						'votes']['T'] / $q['voters'] *
						100);
			}
			$qs[] = $q;
		}
		$this->template_args['questions'] = $qs;
		$this->template_args['podcast'] = $podcast;
		$this->template = 'podcasts_results.tpl';
	}

	/**
	 * The method that exports a CSV datafile of podcast results.
	 * Currently it only accepts standard podcast questions and does no
	 * advanced CSV matching (i.e. double quoting of quote marks). This
	 * also contains a hacky MySQL call.
	 */
	function export_csv() {
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if (! isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		header('Pragma: ');
		header('Content-type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="Podcast_'.
			$I2_ARGS[2].'.csv"');
		Display::stop_display();

		$podcast = new Podcast($I2_ARGS[2]);
		$questions = $podcast->questions;
		$users = $I2_SQL->query('SELECT DISTINCT uid FROM podcast_votes '.
			'WHERE pid = %d', $I2_ARGS[2])->
			fetch_all_single_values();

		$list = array();
		foreach ($podcast->questions as $q) {
			switch($q->answertype) {
				case 'free_response':
				case 'standard':
				case 'approval':
				case 'split_approval':
				case 'short_response':
					// Escape the quotes, they break csv file format
					$list[$q->qid] = '"'.str_replace('"','“',$q->question).'"';
			}
		}

		echo implode(',',$list)."\r\n"; // Print out the header
		$newlist = array();
		foreach ($list as $qid=>$text) {
			$q = $podcast->questions[$qid];
			$newlist[$qid] = array($q, $q->answers);
		}
		$list = $newlist;

		foreach ($users as $user) {
			$responses = array();
			foreach ($list as $qid => $info) {
				$answer = $info[0]->get_response($user);
				switch($info[0]->answertype) {
					case 'free_response':
					case 'short_response':
						// Don't break the csv file format with quotes!!!
						$responses[] = '"'.str_replace('"','“',$answer).'"';
						break;
					case 'standard':
						$responses[] = '"'.str_replace('"','“',$info[1][$answer]).'"';
						break;
					case 'approval':
					case 'split_approval':
						$returnstring = "";
						foreach($answer as $a)
							$returnstring .= $a;
						$responses[] = '"'.$returnstring.'"';
						break;
				}
			}
			echo implode(',',$responses)."\r\n";
		}
	}
}
?>
