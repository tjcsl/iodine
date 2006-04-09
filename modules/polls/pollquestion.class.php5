<?php
/**
* Just contains the definition for the class {@link PollQuestion}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Polls
* @filesource
*/

/**
* The class that keeps track of one question in a poll
* @package modules
* @subpackage Polls
*/
class PollQuestion {

	private $mypid;
	private $myqid;
	
	private $mymaxvotes;

	private $myquestion;

	private $myanswers;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'pid':
				return $this->mypid;
			case 'qid':
				return $this->myqid;
			case 'maxvotes':
				return $this->mymaxvotes;
			case 'question':
				return $this->myquestion;
			case 'answers':
				return $this->myanswers;
			default:
				throw new I2Exception("Invalid variable $var attempted to be accessed in PollQuestion");
		}
	}

	public function __construct($pid, $qid) {
		global $I2_SQL;

		if (! self::question_exists($pid, $qid)) {
			throw new I2Exception("Invalid PollQuestion attempted to be created with pid $pid, qid $qid");
		}

		$info = $I2_SQL->query('SELECT maxvotes, question FROM poll_questions WHERE pid=%d AND qid=%d', $pid, $qid)->fetch_array();

		$this->mypid = $pid;
		$this->myqid = $qid;

		$this->mymaxvotes = $info['maxvotes'];
		$this->myquestion = $info['question'];
		
		$res = $I2_SQL->query('SELECT aid, answer FROM poll_answers WHERE pid=%d AND qid=%d ORDER BY aid ASC', $pid, $qid)->fetch_all_arrays();
		foreach ($res as $row) {
			$this->myanswers[$row['aid']] = $row['answer'];
		}
	}

	/**
	* Set the question
	*
	* @param string $q The question
	*/
	public function set_question($q) {
		global $I2_SQL;

		$I2_SQL->query('UPDATE poll_questions SET question=%s WHERE pid=%d AND qid=%d', $q, $this->pid, $this->qid);
	}

	/**
	* Set the max number of votes
	*
	* @param int $maxvotes The maximum number of allowed votes
	*/
	public function set_maxvotes($maxvotes) {
		global $I2_SQL;

		$I2_SQL->query('UPDATE poll_questions SET maxvotes=%d WHERE pid=%d AND qid=%d', $maxvotes, $this->pid, $this->qid);
	}

	/**
	* Set the answers
	*
	* @param array $answers The options from which a voter can choose
	*/
	public function set_answers($answers) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM poll_answers WHERE pid=%d AND qid=%d', $this->pid, $this->qid);
		$this->answers = array();
		
		$aid = 1;
		foreach ($answers as $answer) {
			$I2_SQL->query('INSERT INTO poll_answers SET pid=%d, qid=%d, aid=%d, answer=%s', $this->pid, $this->qid, $aid, $answer);
			$this->answers[$aid] = $answer;
			$aid++;
		}
	}

	/**
	* Vote for the question
	*
	* @param int $answer The user's vote; a bitwise OR of all options the user selected
	* @param User $user The user voting; defaults to the current user
	*/
	public function record_vote($answer, $user=NULL) {
		global $I2_USER, $I2_SQL;

		if ($user === NULL) {
			$user = $I2_USER;
		}

		if (! is_numeric($answer)) {
			throw new I2Exception("Invalid answer value $answer attempted to be recorded in PollQuestion");
		}

		if ($I2_SQL->query('SELECT COUNT(*) FROM poll_votes WHERE pid=%d AND qid=%d AND uid=%d', $this->mypid, $this->myqid, $user->uid)->fetch_single_value() == 0) {
			// user has not yet voted; create a new row
			$I2_SQL->query('INSERT INTO poll_votes SET answer=%d, pid=%d, qid=%d, uid=%d', $answer, $this->mypid, $this->myqid, $user->uid);
		}
		else {
			// user has votedd; change vote
			$I2_SQL->query('UPDATE poll_votes SET answer=%d WHERE pid=%d AND qid=%d AND uid=%d', $answer, $this->mypid, $this->myqid, $user->uid);
		}
	}

	/**
	* Determine if a user voted for a particular answer
	*
	* @param int $aid The ID number of the answer
	* @param User $user The user (defaults to the current user)
	*/
	public function user_voted_for($aid, $user=NULL) {
		global $I2_USER, $I2_SQL;

		if ($user === NULL) {
			$user = $I2_USER;
		}

		$res = $I2_SQL->query('SELECT answer FROM poll_votes WHERE pid=%d AND qid=%d AND uid=%d', $this->pid, $this->qid, $user->uid);

		if ($res->more_rows()) {
			// the user has voted
			return ((1 << $aid) & $res->fetch_single_value()) != 0;
		}
		else {
			// the user has not voted; they are treated as if they voted for "Clear Vote" (aid 0)
			return $aid == 0;
		}
	}
	
	/**
	* Determines what users voted for the question
	*
	* @return array The {@link User}s who voted
	*/
	public function users_who_voted() {
		global $I2_SQL;

		$ret = array();

		$res = $I2_SQL->query('SELECT uid FROM poll_votes WHERE pid=%d AND qid=%d', $this->pid, $this->qid);
		foreach ($res->fetch_col('uid') as $uid) {
			$ret[] = new User($uid);
		}

		return $ret;
	}

	/**
	* Determine whether a poll question exists
	*
	* @param int $pid The ID number of the poll
	* @param int $qid The ID number of the question
	*
	* @return boolean True if the question exists
	*/
	public static function question_exists($pid, $qid) {
		global $I2_SQL;

		if (! (is_numeric($pid) && is_numeric($qid))) {
			throw new I2Exception("Invalid poll question tested for existance: pid $pid qid $qid");
		}

		if ($I2_SQL->query('SELECT COUNT(*) FROM poll_questions WHERE pid=%d AND qid=%d', $pid, $qid)->fetch_single_value() == 0) {
			return false;
		}

		return true;
	}

	/**
	* Create a new question in the database
	* 
	* This inserts a question into the database; it does NOT add it to any pre-existing {@link Poll}
	* objects. Usually the {@link Poll} add_question method, which calls this, should be used instead.
	*
	* @param int $pid The ID number of the poll
	* @param string $question The question text
	* @param int $maxvotes The maximum number of options for which a user may vote
	* @param array $answers The answers a user may choose
	*/
	public static function new_question($pid, $question, $maxvotes, $answers) {
		global $I2_SQL;

		$qid = $I2_SQL->query('SELECT qid FROM poll_questions WHERE pid=%d ORDER BY qid DESC', $pid)->fetch_single_value() + 1;

		$I2_SQL->query('INSERT INTO poll_questions SET pid=%d, qid=%d, question=%s, maxvotes=%d', $pid, $qid, $question, $maxvotes);
                $aid = 1;
		foreach ($answers as $answer) {
			$I2_SQL->query('INSERT INTO poll_answers SET pid=%d, qid=%d, aid=%d, answer=%s', $pid, $qid, $aid, $answer);
			$aid++;
		}
	}

	/**
	* Deletes a question in the database
	*
	* This deletes a question from the database; it does NOT remove it from any pre-existing {@link Poll}
	* objects. Usually the {@link Poll} delete_question method, which calls this, should be used instead.
	*
	* @param int $pid The ID number of the poll
	* @param int $qid The ID number of the question
	*/
	public static function delete_question($pid, $qid) {
		global $I2_SQL;

		$I2_SQL->query('DELETE FROM poll_questions WHERE pid=%d AND qid=%d', $pid, $qid);
		$I2_SQL->query('DELETE FROM poll_answers WHERE pid=%d AND qid=%d', $pid, $qid);
		$I2_SQL->query('DELETE FROM poll_votes WHERE pid=%d AND qid=%d', $pid, $qid);
	}
}
?>
