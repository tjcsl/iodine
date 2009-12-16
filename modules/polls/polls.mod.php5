<?php
/**
 * The polls module file.
 * @author Joshua Cranmer <jcranmer@tjhsst.edu>
 * @copyright 2007 The Intranet 2 Development Team
 * @package modules
 * @subpackage Polls
 * @filesource
 */

/**
 * The polls module itself.
 * After eighth period and news, this is probably the most important module.
 * @package modules
 * @subpackage Polls
 */
class Polls implements Module {

	/** The template to use. */
	private $template;
	/** Arguments for said template. */
	private $template_args = array();

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
				if (!Poll::can_do($I2_ARGS[2], $method)) {
					redirect('polls');
				}
			} else {
				if (!Poll::can_do(0, $method)) {
					redirect('polls');
				}
			}
			$this->$method();
			return 'Polls: ' . ucwords(strtr($method, '_', ' '));
		} else {
			redirect('polls');
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
		$polls = Poll::accessible_polls(FALSE);
		$open = array();
		$time = time();
		foreach($polls as $poll) {
			if(strtotime($poll->startdt) < $time && strtotime($poll->enddt) > $time && $poll->visible)
				$open[] = $poll;
		}
		$this->template_args['open'] = $open;
		$num = count($open);
		return 'Polls: '.$num.' open poll'.($num==1?'':'s');
	}

	/**
	 * Displays the intranet box.
	 */
	function display_box($display) {
		$display->disp('polls_box.tpl',$this->template_args);
	}

	/**
	 * Returns the name.
	 */
	function get_name() {
		return 'I2 Polls';
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
	 * Nothing really special; just a list of all accessible polls.
	 */	
	function home() {
		global $I2_USER;

		$polls = Poll::accessible_polls(FALSE);
		$open = array();
		$finished = array();
		$unstarted = array();
		$time = time();
		foreach ($polls as $poll) {
			if (strtotime($poll->startdt) > $time)
				$unstarted[] = $poll;
			else if (strtotime($poll->enddt) > $time)
				$open[] = $poll;
			else
				$finished[] = $poll;
		}
		$this->template_args['finished'] = $finished;
		$this->template_args['unstarted'] = $unstarted;
		$this->template_args['open'] = $open;
		if ($I2_USER->is_group_member('admin_polls')) {
			$this->template_args['admin'] = 1;
		}
		$this->template = 'polls_home.tpl';
	}

	/**
	 * The method that handles adding new polls.
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

			$p = Poll::add_poll($name, $blurb, $begin, $end, $on);

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
			$this->template = 'polls_add.tpl';
		}
	}

	/**
	 * The method that handles editing of polls.
	 * $I2_ARGS[2] represents the poll to edit (like most of the other
	 * methods), however $I2_ARGS[3] is special. If $I2_ARGS[3] exists, then
	 * it represents a non-javascript environment and performs special
	 * functions (either inserting dummy values or deleting questions/
	 * answers/etc.)
	 */
	function edit() {
		global $I2_USER, $I2_ARGS;
		$poll = new Poll($I2_ARGS[2]);
		if (isset($I2_ARGS[3])) {
			// $I2_ARGS[3] represents a non-javascript environment
			// Therefore, we perform actions based on its value
			switch ($I2_ARGS[3]) {
			case 'addq':
				$poll->add_question('','standard',0);
				break;
			case 'delq':
				$poll->delete_question($I2_ARGS[4]);
				break;
			case 'adda':
				$poll->questions[$I2_ARGS[4]]->add_answer('');
				break;
			case 'dela':
				$poll->questions[$I2_ARGS[4]]->delete_answer(
					$I2_ARGS[5]);
				break;
			case 'addg':
				$groups = Group::get_all_groups();
				$poll->add_group_id(-1);
				break;
			case 'delg':
				// -1 id means no groups actually exist
				if ($I2_ARGS[4] != -1) {
					$poll->remove_group_id($I2_ARGS[4]);
				}
				break;
			}
		}
		if (isset($_POST['poll_edit_form'])) {
			$on = isset($_POST['visible']) &&
				$_POST['visible'] == 'on' ? 1 : 0;
			$poll->edit_poll($_POST['name'],$_POST['intro'],
				$_POST['startdt'],$_POST['enddt'],$on);
			$seen = array();
			foreach($_POST['question'] as $id) {
				$name = $_POST["q_{$id}_name"];
				$type = $_POST["q_{$id}_type"];
				$maxv = $_POST["q_{$id}_lim"];
				if ($maxv == '')
					$maxv = 0;
				if (isset($poll->questions[$id])) {
					$poll->questions[$id]->edit_question(
						$name, $type, $maxv);
				} else {
					$poll->add_question($name, $type, $maxv,
						$id);
				}
				$seen[] = $id;
				if (isset($_POST['a_'.$id])) {
					$a_seen = array();
					$q = $poll->questions[$id];
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
			$qs = $poll->questions;
			foreach ($qs as $q) {
				if (!in_array($q->qid,$seen)) {
					$poll->delete_question($q->qid);
				}
			}

			if (!array_key_exists('groups', $_POST)) {
				// Someone deleted all the groups.
				// We'll just fake that something exists
				$_POST['groups'] = array();
			}
			$seen = array();
			$gs = $poll->groups;
			foreach ($_POST['groups'] as $key => $id) {
				$g = $_POST['group_gids'][$id];
				if (isset($gs[$g])) {
					$poll->edit_group_id($g, array(
						isset($_POST['vote'][$id])?1:0,
						isset($_POST['modify'][$id])?1:0,
						isset($_POST['results'][$id])?1:0
						));
				} else {
					$poll->add_group_id($g,	array(
						isset($_POST['vote'][$id])?1:0,
						isset($_POST['modify'][$id])?1:0,
						isset($_POST['results'][$id])?1:0
						));
				}
				$seen[] = $g;
			}
			foreach ($gs as $gid => $perms) {
				if (!in_array($gid, $seen))
					$poll->remove_group_id($gid);
			}
		}
		$this->template = 'polls_edit.tpl';
		$this->template_args['poll'] = $poll;
		$this->template_args['types']=PollQuestion::get_answer_types();
		$this->template_args['groups'] = Group::get_all_groups();
	}

	/**
	 * The method that handles deletion of polls.
	 */
	function delete() {
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if (! isset($I2_ARGS[2])) {
			$this->home();
		}

		$pid = $I2_ARGS[2];
		$name = $I2_SQL->query('SELECT name FROM polls WHERE pid=%d', $pid)->fetch_single_value();
		$this->template_args['pollname'] = $name;

		if (isset($_REQUEST['polls_delete_form'])) {
			if ($_REQUEST['polls_delete_form'] == 'delete_poll') {
				Poll::delete_poll($pid);
				$this->template_args['deleted'] = TRUE;
			}
		}
		$this->template = 'polls_delete.tpl';
	}

	/**
	 * The method that handles the actual voting in polls.
	 * This method directly makes one MySQL call: finding out if the user
	 * has voted.
	 */
	function vote() {
		global $I2_ARGS, $I2_SQL, $I2_USER;

		if (!isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		
		$poll = new Poll($I2_ARGS[2]);
		if (isset($_POST['polls_vote_form'])) {
			$uid = $I2_USER->uid;
			$qs = $poll->questions;
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
			$poll->cache_ldap();
			$this->template = 'polls_voted.tpl';
			return;
		}
		$this->template_args['poll'] = $poll;
		$this->template_args['avail'] = $poll->in_session();
		$this->template_args['has_voted'] = $I2_SQL->query('SELECT '.
			'COUNT(*) FROM poll_votes WHERE pid=%d AND uid=%d',
			$poll->pid,$I2_USER->uid)->fetch_single_value() > 0;
		$this->template = 'polls_vote.tpl';
	}

	/**
	 * The method that returns the results of the poll.
	 * I know that the code looks ugly, but most of it is due to the fact
	 * that there are so many aggregate data values to compute.
	 */
	function results() {
		global $I2_ARGS;

		if (!isset($I2_ARGS[2])) {
			$this->home();
			return;
		}

		$poll = new Poll($I2_ARGS[2]);

		if (isset($I2_ARGS[3])) {
			$I2_ARGS[3] = substr($I2_ARGS[3],1);
			$question = $poll->questions[$I2_ARGS[3]];

			$this->template_args['poll'] = $poll;
			$this->template_args['question'] = $question->question;
			$this->template_args['votes'] = $question->
				get_all_votes();
			$this->template = 'polls_results_freeresponse.tpl';
			return;
		}
		$qs = array();
		$questions = $poll->questions;
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
		$this->template_args['poll'] = $poll;
		$this->template = 'polls_results.tpl';
	}

	/**
	 * The method that exports a CSV datafile of poll results.
	 * Currently it only accepts standard poll questions and does no
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
		header('Content-Disposition: attachment; filename="Poll_'.
			$I2_ARGS[2].'.csv"');
		Display::stop_display();

		$poll = new Poll($I2_ARGS[2]);
		$questions = $poll->questions;
		$users = $I2_SQL->query('SELECT DISTINCT uid FROM poll_votes '.
			'WHERE pid = %d', $I2_ARGS[2])->
			fetch_all_single_values();

		$list = array();
		foreach ($poll->questions as $q) {
			switch($q->answertype) {
				case 'free_response':
				case 'standard':
				case 'approval':
				case 'split_approval':
					// Escape the quotes, they break csv file format
					$list[$q->qid] = '"'.str_replace('"','“',$q->question).'"';
			}
		}

		echo implode(',',$list)."\r\n"; // Print out the header
		$newlist = array();
		foreach ($list as $qid=>$text) {
			$q = $poll->questions[$qid];
			$newlist[$qid] = array($q, $q->answers);
		}
		$list = $newlist;

		foreach ($users as $user) {
			$responses = array();
			foreach ($list as $qid => $info) {
				$answer = $info[0]->get_response($user);
				switch($info[0]->answertype) {
					case 'free_response':
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
