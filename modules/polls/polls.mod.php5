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

	function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}

	/**
	 * Initializes the intranet box.
	 * Currently is not used.
	 */
	function init_box() {
		return FALSE;
	}

	/**
	 * Displays the intranet box.
	 * Currently is not used.
	 */
	function display_box($display) {
	}

	/**
	 * Returns the name.
	 */
	function get_name() {
		return 'I2 Polls';
	}

	function is_intrabox() {
		return false;
	}

	/////////////
	// METHODS //
	/////////////
	
	function home() {
		global $I2_USER;

		$polls = Poll::accessible_polls();
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

	function add() {
		global $I2_USER, $I2_ARGS;
	
		$this->template_args['groups'] = Group::get_all_groups();
		if (count($_POST) > 0) {
			print_r($_POST);
			$name = $_POST['name'];
			$begin = $_POST['startdt'];
			$end = $_POST['enddt'];
			$blurb = $_POST['intro'];
			$on = isset($_POST['visible']) && $_POST['visible'] == 'on' ? 1 : 0;

			$poll = Poll::add_poll($name, $blurb, $begin, $end, $on);

			$_POST['groups'] = array_unique($_POST['groups']);
			foreach ($_POST['groups'] as $group) {
				$poll->add_group_id($group);
			}
			$_POST = array(); // Unset post vars
			$I2_ARGS[2] = $poll->pid;
			$this->edit();
		} else {
			$this->template = 'polls_add.tpl';
		}
	}

	/**
	 * The method that handles editing of polls.
	 * $I2_ARGS[2] represents the poll to edit (like most of the other methods),
	 * however $I2_ARGS[3] is special. If $I2_ARGS[3] exists, then it represents a
	 * non-javascript environment and performs special functions (either inserting
	 * dummy values or deleting questions/answers/etc.)
	 */
	function edit() {
		global $I2_USER, $I2_ARGS;
		$poll = new Poll($I2_ARGS[2]);
		if (isset($I2_ARGS[3])) {
			// $I2_ARGS[3] represents a non-javascript environment
			// Therefore, we perform actions based on its value
		}
		if (isset($_POST['poll_edit_form'])) {
			// Firefox doesn't seem to send the value if a checkbox exists.
			$on = isset($_POST['visible']) && $_POST['visible'] == 'on' ? 1 : 0;
			$poll->edit_poll($_POST['name'],$_POST['intro'], $_POST['startdt'],$_POST['enddt'],$on);
			$seen = array();
			foreach($_POST['question'] as $id) {
				$name = $_POST["q_{$id}_name"];
				$type = $_POST["q_{$id}_type"];
				$maxv = $_POST["q_{$id}_lim"];
				if ($maxv == '')
					$maxv = 0;
				if (isset($poll->questions[$id])) {
					$poll->questions[$id]->edit_question($name,$type,$maxv);
				} else {
					$poll->add_question($name,$type,$maxv, $id);
				}
				$seen[] = $id;
				if (isset($_POST['a_'.$id])) {
					$a_seen = array();
					$q = $poll->questions[$id];
					foreach($_POST['a_'.$id] as $aid) {
						if (isset($q->answers[$aid])) {
							$q->edit_answer($_POST['a_'.$id.'_'.$aid], $aid);
						} else {
							$q->add_answer($_POST["a_{$id}_{$aid}"], $aid);
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
			$seen = array();
			$_POST['groups'] = array_unique($_POST['groups']);
			foreach ($_POST['groups'] as $g) {
				if (!isset($poll->groups[$g])) {
					$poll->add_group_id($g,array(TRUE,FALSE,FALSE));
				}
				$seen[] = $g;
			}
			$gs = $poll->groups;
			foreach ($gs as $gid => $perms) {
				if (!in_array($gid, $seen)) {
					$poll->remove_group_id($gid);
				}
			}
		}
		$this->template = 'polls_edit.tpl';
		$this->template_args['poll'] = $poll;
		$this->template_args['types'] = PollQuestion::get_answer_types();
		$this->template_args['groups'] = Group::get_all_groups();
	}

	/**
	 * The method that handles deletion of polls.
	 */
	function delete() {
		global $I2_USER, $I2_ARGS;

		if (! isset($I2_ARGS[2])) {
			$this->home();
		}

		if (isset($_REQUEST['polls_delete_form'])) {
			if ($_REQUEST['polls_delete_form'] == 'delete_poll') {
				Poll::delete_poll($I2_ARGS[2]);
				$this->template_args['deleted'] = TRUE;
			}
		}
		$this->template = 'polls_delete.tpl';
	}

	/**
	 * The method that handles the actual voting in polls.
	 * This method directly makes one MySQL call: the caching of the grade
	 * and gender so it isn't repeated in every question.
	 */
	function vote() {
		global $I2_ARGS, $I2_USER, $I2_SQL;
		if (!isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		
		$poll = new Poll($I2_ARGS[2]);
		if (isset($_POST['polls_vote_form'])) {
			$grade = $I2_USER->grade;
			$gender = $I2_USER->gender;
			$uid = $I2_USER->uid;
			$qs = $poll->questions;
			foreach ($qs as $q) {
				$q->delete_vote($uid);
				if (isset($_POST[$q->qid])) {
					if ($q->answertype == 'free_response' &&
						strlen($_POST[$q->qid]) == 0)
						continue;
					$q->vote($_POST[$q->qid],$uid);
				}
			}
			$I2_SQL->query('UPDATE poll_votes SET grade=%s,gender=%s WHERE uid=%d',$grade,$gender,$uid);
			$this->template = 'polls_voted.tpl';
			return;
		}
		$this->template_args['poll'] = $poll;
		$this->template_args['avail'] = $poll->in_session();
		$this->template_args['has_voted'] = $I2_SQL->query('SELECT COUNT(*) FROM poll_votes WHERE pid=%d AND uid=%d',$poll->pid,$I2_USER->uid)->fetch_single_value() > 0;
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
			$this->template_args['votes'] = $question->get_all_votes();
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
					$ans['votes'][$g.'M'] = $question->num_who_vote($aid,$g,'M');
					$ans['votes'][$g.'F'] = $question->num_who_vote($aid,$g,'F');
					$ans['votes'][$g.'T'] = $ans['votes'][$g.'M']+$ans['votes'][$g.'F'];
					$ans['votes']['T'] += $ans['votes'][$g.'T'];
					$ans['votes']['M'] += $ans['votes'][$g.'M'];
					$ans['votes']['F'] += $ans['votes'][$g.'F'];
					$q['total'][$g.'T'] += $ans['votes'][$g.'T'];
					$q['total'][$g.'M'] += $ans['votes'][$g.'M'];
					$q['total'][$g.'F'] += $ans['votes'][$g.'F'];
				}
				$ans['votes']['staffT'] = $question->num_who_vote($aid,'STAFF',NULL);
				$ans['votes']['T'] += $ans['votes']['staffT'];
				$ans['percent'] = "NA";
				$q['total']['staffT'] += $ans['votes']['staffT'];
				$q['total']['M'] += $ans['votes']['M'];
				$q['total']['F'] += $ans['votes']['F'];
				$q['total']['T'] += $ans['votes']['T'];
				$q['answers'][] = $ans;
			}
			$q['voters'] = $question->num_who_voted();
			foreach ($q['answers'] as $key => $response) {
				if ($q['voters'] == 0)
					if ($q['answertype'] == 'standard')
						$q['answers'][$key]['percent'] = "NA";
					else
						$q['answers'][$key]['percent'] == "0.00";
				else 
					$q['answers'][$key]['percent'] = sprintf("%.2f",$response['votes']['T'] / $q['voters'] * 100);
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
		header("Content-Disposition: attachment; filename=\"Poll_{$I2_ARGS[2]}.csv\"");
		Display::stop_display();

		$poll = new Poll($I2_ARGS[2]);
		$questions = $poll->questions;
		$users = $I2_SQL->query('SELECT DISTINCT uid FROM poll_votes WHERE pid = %d',
			$I2_ARGS[2])->fetch_all_single_values();

		$list = array();
		foreach ($poll->questions as $q) {
			switch($q->answertype) {
			case 'standard':
				$list[$q->qid] = '"'.$q->question.'"';
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
				$responses[] = '"'.$info[1][$answer].'"';
			}
			echo implode(',',$responses)."\r\n";
		}
	}
}
?>
