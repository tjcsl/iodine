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

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_USER, $I2_ARGS;

		if (count($I2_ARGS) <= 1) {
			$I2_ARGS[1] = 'home';
		}

		$method = $I2_ARGS[1];
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
	* The main page
	*/
	function home() {
		global $I2_USER;

		$this->template = 'polls_pane.tpl';
		$this->template_args['polls'] = Poll::get_user_polls($I2_USER);
		if ($I2_USER->is_group_member('admin_polls')) {
			$this->template_args['admin'] = 1;
		}
	}

	/**
	* Where a user votes
	*/
	function vote() {
		global $I2_USER, $I2_ARGS;

		if (! isset($I2_ARGS[2])) {
			$this->home();
			return;
		}

		$poll = new Poll($I2_ARGS[2]);
		if (isset($_REQUEST['polls_vote_form'])) {
			foreach ($poll->questions as $question) {
				$answer = 0;
				if ($question->maxvotes == 1) {
					$answer = 1 << $_REQUEST[$question->qid];
				}
				else {
					foreach ($question->answers as $aid => $ans) {
						if (isset($_REQUEST[$question->qid.'_'.$aid])) {
							$answer = $answer | (1 << $aid);
						}
					}
				}
				$question->record_vote($answer, $I2_USER);
			}
		}
		
		$this->template = 'polls_vote.tpl';
		$this->template_args['poll'] = $poll;
	}

	/**
	* Poll results viewing
	*/
	function results() {
		global $I2_USER, $I2_ARGS;

		if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

		if (! isset($I2_ARGS[2])) {
			$this->home();
			return;
		}
		
		$poll = new Poll($I2_ARGS[2]);
		$this->template_args['pollname'] = $poll->name;
		$this->template_args['questions'] = array();
		foreach ($poll->questions as $q) {
			$question = array();
			$question['text'] = $q->question;
			if ($q->maxvotes != 1) {
				$question['approval'] = TRUE;
			}

			$voters = $q->users_who_voted();
			$question['voters'] = count($voters);

			$question['total'] = 0;
			$question['answers'] = array();

			foreach ($q->answers as $aid => $text) {
				$answer = array('text' => $text, 'votes' => 0);
				foreach ($voters as $voter) {
					if ($q->user_voted_for($aid, $voter)) {
						$question['total']++;
						$answer['votes']++;
					}
				}
				$answer['percent'] = $answer['votes'] / $question['voters'] * 100;
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

		if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

		$this->template = 'polls_admin.tpl';
		$this->template_args['polls'] = Poll::all_polls();
	}
	
	/**
	* The interface to add poll and questions
	*/
	function add() {
		global $I2_USER, $I2_ARGS;

		if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

		if (isset($_REQUEST['poll_add_form'])) {
			if ($_REQUEST['poll_add_form'] == 'poll') {
				$poll = Poll::add_poll($_REQUEST['name'], $_REQUEST['intro']);
				if (isset($_REQUEST['visible'])) {
					$poll->set_visibility(TRUE);
				}
				$poll->set_groups(explode(',', trim($_REQUEST['groups'])));
				$poll->set_start_datetime($_REQUEST['startdt']);
				$poll->set_end_datetime($_REQUEST['enddt']);
				$pid = $poll->pid;
				redirect("polls/edit/$pid");
			}
			elseif ($_REQUEST['poll_add_form'] == 'question') {
				$poll = new Poll($I2_ARGS[2]);
				$poll->add_question($_REQUEST['question'], $_REQUEST['maxvotes'], explode("\r\n\r\n", trim($_REQUEST['answers'])));
				$pid = $I2_ARGS[2];
				redirect("polls/edit/$pid");
			}

		}
		
		if (isset($I2_ARGS[2])) {
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

		if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

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
				$poll->set_groups(explode(',', $_REQUEST['groups']));
				$poll->set_start_datetime($_REQUEST['startdt']);
				$poll->set_end_datetime($_REQUEST['enddt']);
			}
			if ($_REQUEST['poll_edit_form'] == 'question') {
				$question = new PollQuestion($I2_ARGS[2], $_REQUEST['qid']);
				$question->set_question($_REQUEST['question']);
				$question->set_maxvotes($_REQUEST['maxvotes']);
				$question->set_answers(explode("\r\n\r\n", trim($_REQUEST['answers'])));
			}
		}
		if (isset($I2_ARGS[4])) {
			$this->template_args['aid'] = $I2_ARGS[4];
			$this->template = 'polls_edit_answer.tpl';
			$this->template_args['question'] = new PollQuestion($I2_ARGS[2], $I2_ARGS[3]);
		} elseif (isset($I2_ARGS[3])) {
			$this->template = 'polls_edit_question.tpl';
			$this->template_args['question'] = new PollQuestion($I2_ARGS[2], $I2_ARGS[3]);
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

		if (! $I2_USER->is_group_member('admin_polls')) {
			$this->home();
			return;
		}

		if (! isset($I2_ARGS[2])) {
			$this->admin();
		}

		if (isset($_REQUEST['polls_delete_form'])) {
	1		if ($_REQUEST['polls_delete_form'] == 'delete_poll') {
				Poll::delete_poll($I2_ARGS[2]);
				$this->template_args['deleted'] = TRUE;
			}
			if ($_REQUEST['polls_delete_form'] == 'delete_question') {
				$poll = new Poll($I2_ARGS[2]);
				$poll->delete_question($I2_ARGS[3]);
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
