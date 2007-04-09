<?php
/**
* Just contains the definition for the class {@link Polls}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Polls
* @filesource
*/

/**
* The module that runs polls and voting.
* @package modules
* @subpackage Polls
*/
class Polls implements Module {

	/**
	* The display object to use
	*/
	private $display;

	/**
	* Template for the specified action
	*/
	private $template;

	private $template_args = array();

	private static $permitted = array("home", "vote");

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1) {
			$I2_ARGS[1] = 'home';
		}

		$method = $I2_ARGS[1];
		if (! $I2_USER->is_group_member('admin_polls') && !in_array($method, Polls::$permitted)) {
			$method = "home";
		}
		if (method_exists($this, $method)) {
			$this->$method();
			$this->template_args['method'] = $method;
			return 'Polls: ' . ucwords(strtr($method, '_', ' '));
		}
		else {
			redirect('polls');
		}
		return array('Error', 'Error');
	}

	/**
	 * The main page (default).
	 * This page displays the list of polls to which the user has access.
	*/
	function home() {
		global $I2_USER;

		$this->template = 'polls_pane.tpl';
		//$this->template_args['polls'] = Poll::get_user_polls($I2_USER);
		$polls = Poll::all_polls();
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
	}

	/**
	* Where a user votes
	*/
	function vote() {
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if (! isset($I2_ARGS[2])) {
			$this->home();
			return;
		}

		$this->template_args['errors'] = array();

		$poll = new Poll($I2_ARGS[2]);
		
		$this->template_args['has_voted'] = Poll::has_voted($poll, $I2_USER);
		$this->template_args['avail'] = $poll->user_can_access($I2_USER) ? TRUE : FALSE;

		if (isset($_REQUEST['polls_vote_form'])) {
			foreach ($poll->questions as $question) {
				$answer = 0;
				//if ($question->maxvotes == 1) {
				if ($question->answertype == 'radio') {
					if (isSet($_REQUEST[$question->qid])) {
						$question->record_vote($_REQUEST[$question->qid]);
					}
				}
				else if ($question->answertype == 'freeresponse') {
					$aid = "{$question->qid}001";
					$question->delete_vote($aid);
					if (isSet($_REQUEST[$question->qid]) && $_REQUEST[$question->qid] != "") {
						$question->record_vote($aid, $_REQUEST[$question->qid]);
					}
				}
				else {
					// Just kidding //Perform deletions before addition to ensure we stay below maxvotes if possible
					$add = array();
					$delete = array();
					if(count($question->answers) != 0) {
						foreach ($question->answers as $aid => $ans) {
							$vote = isSet($_REQUEST[$aid])?$_REQUEST[$aid]:FALSE;
							if ($vote) {
								$add[$aid] = $vote;
							}
							$delete[] = $aid;
							//$question->delete_vote($aid);
						}
					}

					if ($question->maxvotes != 0 && count($add) > $question->maxvotes) {
						$this->template_args['errors'][] = "Oops! You tried to vote for too many options on question #{$question->readable_qid}!";
						continue;
					}
					//search and destroy
					if (count($add) == 0 && Poll::has_voted($poll, $I2_USER)) {
						$res = $I2_SQL->query("SELECT aid FROM poll_votes WHERE uid='%d' AND aid LIKE '%d%'", $I2_USER->uid, $question->qid);
						foreach($res as $row) {
							$delete[] = $row['aid'];
							//warn($row['aid']);
						}
					}
					foreach ($delete as $delete_me) {
						$question->delete_vote($delete_me);	
					}
					foreach ($add as $aid=>$vote) {
						if ($question->answertype != 'freeresponse') {
							// Don't record text for voting questions
							$vote = NULL;
						}
						$question->record_vote($aid, $vote);
					}
				}
			}
			$this->template = 'polls_voted.tpl';
		}
		else {
		$this->template = 'polls_vote.tpl';
		}
		$this->template_args['poll'] = $poll;
	}
	/**
	* Poll results viewing
	*/
	function results() {
		global $I2_USER, $I2_ARGS, $I2_SQL;
		/*if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}*/

		if (! isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		
		$poll = new Poll($I2_ARGS[2]);
		$this->template_args['pollname'] = $poll->name;
		
		//Display freeresponse results
		if ( isset($I2_ARGS[3])) {
			$votes = array();
			$res = $I2_SQL->query("SELECT * FROM poll_votes WHERE aid=%s AND answer IS NOT NULL", "{$I2_ARGS[3]}001");
			$question = $I2_SQL->query("SELECT question FROM poll_questions WHERE qid='%d'", $I2_ARGS[3])->fetch_single_value();
			foreach($res as $row) { 
				$vote = array();
				$user = new User($row['uid']);
				$vote['uid'] = $user->name; 
				$vote['vote'] = $row['answer'];
				if ($user->grade === 'staff') {
					$vote['grade'] = 'Teacher';
				} else {
					$vote['grade'] = $user->grade . 'th grader';
				}
				$vote['numgrade'] = $user->grade;
				$votes[] = $vote;
			}
			$this->template_args['votes'] = $votes;
			$this->template_args['question'] = $question;
			$this->template = 'polls_results_freeresponse.tpl';
			return;
		}
		$this->template_args['questions'] = array();

		foreach ($poll->questions as $q) {
			$question = array();
			$question['qid'] = $q->qid; 
			$question['text'] = $q->question;
			//$question[$q->type] = TRUE;
			$question['answertype'] = $q->answertype;
			$question['r_qid'] = $q->r_qid;
			if($q->answers == NULL) {
				if($q->answertype == 'freeresponse') {
					$this->template_args['questions'][] = $question;
				}
				continue;
			}

			$question['voters'] = $q->num_voters();

			$question['total']['T'] = 0;
			$question['total']['9M'] = 0;
			$question['total']['9F'] = 0;
			$question['total']['9T'] = 0;
			$question['total']['10M'] = 0;
			$question['total']['10F'] = 0;
			$question['total']['10T'] = 0;
			$question['total']['11M'] = 0;
			$question['total']['11F'] = 0;
			$question['total']['11T'] = 0;
			$question['total']['12M'] = 0;
			$question['total']['12F'] = 0;
			$question['total']['12T'] = 0;
			$question['total']['staffT'] = 0;
			$question['total']['M'] = 0;
			$question['total']['F'] = 0;
			$question['answers'] = array();

				
			foreach ($q->answers as $aid => $text) {
				$answer = array('text' => $text);
				$answer['votes']['T'] = 0;
				$num = PollQuestion::get_num_votes($aid);
				$whoans = PollQuestion::users_who_answered($aid);
				$question['total']['T'] += $num;
				//t:total;; 9,10,11,12:grade;; m,f:gender
				//Do the supertotals
				$answer['votes']['T'] += $num;
				d($answer['votes']['T'].' votes for aid '.$aid,1);
				if ($question['voters'] != 0) {
					$answer['percent']['T'] = sprintf("%.2f",$answer['votes']['T'] / $question['voters'] * 100);
				} else {
					$answer['percent']['T'] = 'NA';
				}
				//Now do ALL the categoricals
				$answer['votes']['9M'] = 0;
				$answer['votes']['9F'] = 0;
				$answer['votes']['9T'] = 0;
				$answer['votes']['10M'] = 0;
				$answer['votes']['10F'] = 0;
				$answer['votes']['10T'] = 0;
				$answer['votes']['11M'] = 0;
				$answer['votes']['11F'] = 0;
				$answer['votes']['11T'] = 0;
				$answer['votes']['12M'] = 0;
				$answer['votes']['12F'] = 0;
				$answer['votes']['12T'] = 0;
				$answer['votes']['staffT'] = 0;
				$answer['votes']['M'] = 0;
				$answer['votes']['F'] = 0;
				foreach($whoans as $u)
				{
					$gr = $u->grade;
					$gen = $u->gender;
					$question['total']["{$gr}T"]++;
					$answer['votes']["{$gr}T"]++;
					if(empty($gen))
						continue; //staff has no gender on file
					$question['total']["{$gr}{$gen}"]++;
					$question['total']["{$gen}"]++;
					$answer['votes']["{$gr}{$gen}"]++;
					$answer['votes']["{$gen}"]++;
				}
				$question['answers'][] = $answer;
			
			}
			$this->template_args['questions'][] = $question;
		}
		$this->template = 'polls_results.tpl';
	}

	/**
	* The main admin interface
	*/
	function admin() {
		global $I2_USER, $I2_ARGS;

		/*if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

		$this->template = 'polls_admin.tpl';
		$this->template_args['polls'] = Poll::all_polls();*/
		$this->home();
	}
	
	/**
	* The interface to add poll and questions
	*/
	function add() {
		global $I2_USER, $I2_ARGS;

		/*if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}*/

		$this->template_args['groups'] = Group::get_all_groups();

		if (isset($_REQUEST['poll_add_form'])) {
			if ($_REQUEST['poll_add_form'] == 'poll') {
				$poll = Poll::add_poll($_REQUEST['name'], $_REQUEST['intro']);
				if (isset($_REQUEST['visible'])) {
					$poll->set_visibility(TRUE);
				}
				$poll->set_groups(Group::generate($_REQUEST['add_groups']));
				$poll->set_start_datetime($_REQUEST['startdt']);
				$poll->set_end_datetime($_REQUEST['enddt']);
				$pid = $poll->pid;
				redirect("polls/edit/$pid");
			}
			elseif ($_REQUEST['poll_add_form'] == 'question') {
				$poll = new Poll($I2_ARGS[2]);
				if ($_REQUEST['answertype'] == 'freeresponse') {
					$maxvotes = 1;
				} else {
					$maxvotes = 0;
				}
				$poll->add_question($_REQUEST['question'], $_REQUEST['answertype'], $maxvotes, NULL);
				//$poll->add_question($_REQUEST['question'], $_REQUEST['maxvotes'], explode("\r\n\r\n", 		$pid = $I2_ARGS[2]));
				redirect("polls/edit/{$I2_ARGS[2]}");
			}

		}
		if (isSet($I2_ARGS[3])) {
			if (isSet($_REQUEST['answer'])) {
					  PollQuestion::add_answer_to_question($I2_ARGS[3],$_REQUEST['answer']);
					  redirect('polls/edit/'.$I2_ARGS[2].'/'.$I2_ARGS[3]);
			}
			$this->template = 'polls_add_answer.tpl';
			$this->template_args['pid'] = $I2_ARGS[2];
			$this->template_args['qid'] = $I2_ARGS[3];
			$this->template_args['question'] = new PollQuestion($this->template_args['qid']);
		} elseif (isset($I2_ARGS[2])) {
			$this->template = 'polls_add_question.tpl';
			$this->template_args['pid'] = $I2_ARGS[2];
		}
		else {
			$this->template = 'polls_add.tpl';
		}
	}

	/**
	* The poll editing interface
	*/
	function edit() {
		global $I2_USER, $I2_ARGS;

		$this->template_args['groups'] = Group::get_all_groups();

		/*if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}*/

		if (! isset($I2_ARGS[2])) {
			$this->admin();
			return;
		}
		$this->template_args['pid'] = $I2_ARGS[2];

		if (isSet($I2_ARGS[3])) {
				  $this->template_args['qid'] = $I2_ARGS[3];
		}

		if (isset($_REQUEST['poll_edit_form'])) {
			if ($_REQUEST['poll_edit_form'] == 'poll') {
				$poll = new Poll($I2_ARGS[2]);
				$poll->set_name($_REQUEST['name']);
				if (isset($_REQUEST['visible'])) {
					$poll->set_visibility(1);
				}
				else {
					$poll->set_visibility(0);
				}
				$poll->set_introduction($_REQUEST['intro']);
				$poll->set_groups(Group::generate($_REQUEST['add_groups']));
				$poll->set_start_datetime($_REQUEST['startdt']);
				$poll->set_end_datetime($_REQUEST['enddt']);
			}
			if ($_REQUEST['poll_edit_form'] == 'question') {
				//$question = new PollQuestion($I2_ARGS[2], $_REQUEST['qid']);
				$question = new PollQuestion($_REQUEST['qid']);
				$question->set_question($_REQUEST['question']);
				if($question->answertype == 'checkbox') {
					$question->set_maxvotes($_REQUEST['maxvotes']);
				}
				//$question->set_answertype($_REQUEST['answertype']);
				//$question->set_answers(explode("\r\n\r\n", trim($_REQUEST['answers'])));
			}
		}
		if (isset($I2_ARGS[4])) {
		   if (isSet($_REQUEST['answer'])) {
					  PollQuestion::change_answer($I2_ARGS[4],$_REQUEST['answer']);
					  redirect('polls/edit/'.$I2_ARGS[2].'/'.$I2_ARGS[3]);
			}
			$this->template_args['aid'] = $I2_ARGS[4];
			$this->template = 'polls_edit_answer.tpl';
			$this->template_args['question'] = new PollQuestion($I2_ARGS[3]);
			$this->template_args['answer'] = PollQuestion::get_answer_text($this->template_args['aid']);
		} elseif (isset($I2_ARGS[3])) {
			$this->template = 'polls_edit_question.tpl';
			$this->template_args['question'] = new PollQuestion($I2_ARGS[3]);
		}
		else {
			$this->template = 'polls_edit.tpl';
			$this->template_args['poll'] = new Poll($I2_ARGS[2]);
		}
	}

	/**
	* The poll deleting interface
	*/
	function delete() {
		global $I2_USER, $I2_ARGS;

		/*if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}*/

		if (! isset($I2_ARGS[2])) {
			$this->admin();
		}

		if (isset($_REQUEST['polls_delete_form'])) {
			if ($_REQUEST['polls_delete_form'] == 'delete_poll') {
				Poll::delete_poll($I2_ARGS[2]);
				$this->template_args['deleted'] = TRUE;
			}
			if ($_REQUEST['polls_delete_form'] == 'delete_question') {
				$poll = new Poll($I2_ARGS[2]);
				$poll->delete_question($I2_ARGS[3]);
				$this->template_args['deleted'] = TRUE;
			}
			if ($_REQUEST['polls_delete_form'] == 'delete_answer') {
				PollQuestion::delete_answer($I2_ARGS[4]);
				$this->template_args['deleted'] = TRUE;
			}
		}
		if (isset($I2_ARGS[4])) {
			$this->template = 'polls_delete_answer.tpl';
		}
		elseif (isset($I2_ARGS[3])) {
			$this->template = 'polls_delete_question.tpl';
		}
		else {
			$this->template = 'polls_delete.tpl';
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($display) {
		global $I2_SQL, $I2_USER, $I2_ARGS;
		if (isset($I2_ARGS[2])) {
			$this->template_args['pid'] = $I2_ARGS[2];
		}
		if (isset($I2_ARGS[3])) {
			$this->template_args['qid'] = $I2_ARGS[3];
		}
		if (isset($I2_ARGS[4])) {
			$this->template_args['aid'] = $I2_ARGS[4];
		}
		$display->disp($this->template, $this->template_args);
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		return FALSE;
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($display) {
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'Voting Booth';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function is_intrabox() {
		return FALSE;
	}
}

?>
