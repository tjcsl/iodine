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

	private $type;

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

	public static function to_qid($pid, $qnum) {
			  return 1000*$pid+$qnum;
	}

	public function __construct($pid, $qnum) {
		global $I2_SQL;
		
		$qid = self::to_qid($pid,$qnum);

		if (! self::question_exists($pid, $qid)) {
			throw new I2Exception("Invalid PollQuestion attempted to be created with pid $pid, qid $qid");
		}

		$info = $I2_SQL->query('SELECT maxvotes, question, answertype FROM poll_questions WHERE qid=%d', $pid, $qid)->fetch_array(Result::ASSOC);

		$this->mypid = $pid;
		$this->myqid = $qid;
		$this->type = $info['answertype'];

		$this->mymaxvotes = $info['maxvotes'];
		$this->myquestion = $info['question'];
		
		$res = self::get_answers_to_question($qid);

		foreach ($res as $row) {
			$this->myanswers[$row['aid']] = $row['answer'];
		}
	}

	/**
	* Gets the maximum value an aid may hold while still belonging to a question, plus 1.
	*/
	private static function upper_bound($qid) {
			  return $qid + 1000;
	}

	/**
	* Gets the answers to a given question, or the answers given by a particular user to a particular question.
	*
	* @param int $qid The questionID in question (heh)
	* @param int $uid An optional userid
	* @return array An array of associative arrays with keys 'aid' and 'answer'.
	* 					 If the user is specified, the value of each 'answer' is (one of) THEIR answers.
	* 					 Otherwise, it is the answer text.
	*/
	private static function get_answers_to_question($qid,$uid = NULL) {
			  global $I2_SQL;
			  if (!$uid) {
						 return $I2_SQL->query(
									'SELECT aid,answer FROM poll_answers WHERE aid > %d AND aid < %d',
									$qid,self::upper_bound($qid))->fetch_arrays(Result::ASSOC);
			  } else {
						 return $I2_SQL->query(
									'SELECT aid,answer FROM poll_votes WHERE uid=%d AND aid > %d AND aid < %d',
									$uid,$qid,self::upper_bound($qid))->fetch_arrays(Result::ASSOC);
			  }
	}

	/**
	* Gets this question's answers.
	*
	* @param int $uid An optional uid: if set, all answers returned are arrays of votes made by the given user.
	* @return array An associative array, keyed by aid.
	*/
	public function get_answers($uid=NULL) {
			  if ($uid == NULL) {
						 return $this->myanswers;
			  } else {
						 $ret = array();
						 $res = self::get_answers_to_question($this->qid,$uid);
						 foreach ($res as $row) {
									if (!isSet($res[$row['aid']])) {
											  $ret[$row['aid']] = array();
									}
									$ret[$row['aid']][] = $row['answer'];
						 }
						 return $ret;
			  }
	}


	/**
	* Set the question
	*
	* @param string $q The question
	*/
	public function set_question($q) {
		global $I2_SQL;

		Poll::check_admin();

		return $I2_SQL->query('UPDATE poll_questions SET question=%s WHERE qid=%d', $q, $this->qid);
	}

	/**
	* Set the max number of votes
	*
	* @param int $maxvotes The maximum number of allowed votes
	*/
	public function set_maxvotes($maxvotes) {
		global $I2_SQL;

		Poll::check_admin();

		return $I2_SQL->query('UPDATE poll_questions SET maxvotes=%d WHERE qid=%d', $maxvotes, $this->qid);
	}

	/**
	* Set the answers
	*
	* @param array $answers The options from which a voter can choose
	*/
	public function set_answers($answers) {
		global $I2_SQL;

		Poll::check_admin();

		return $I2_SQL->query('DELETE FROM poll_answers WHERE qid=%d', $this->qid);
		$this->answers = array();
		
		foreach ($answers as $answer) {
			$I2_SQL->query('INSERT INTO poll_answers SET pid=%d, qid=%d, answer=%s', $this->pid, $this->qid, $answer);
			$this->answers[] = $answer;
		}
	}

	public function change_answer($aid,$newtext) {
			  global $I2_SQL;
			  Poll::check_admin();
			  $I2_SQL->query('UPDATE poll_answers SET answer=%s WHERE aid=%d AND qid=%d AND pid=%d',$newtext,$aid,$this->qid,$this->pid);
	}

	/**
	* Returns whether the given answerid refers to this question
	*
	* @param int $aid The answerid in question
	* @return bool TRUE if the answer pertains to the question
	*/
	public function is_answer($aid) {
			  return $aid >= $this->qid && $aid < self::upper_bound($this->qid);
	}

	/**
	* Vote for the question
	*
	* @param int $answer The answerid (should be pollid+000 for freeresponse/text)
	* @param string $answertext The text of the user's answer (optional)
	* @param User $user The user voting; defaults to the current user
	*/
	public function record_vote($answer, $answertext=NULL, $user=NULL) {
		global $I2_USER, $I2_SQL;

		if (!$user) {
			$user = $I2_USER;
		}

		$user = new User($user);
		if ($user->uid != $I2_USER->uid) {
				  Poll::check_admin();
		}

		if (! is_numeric($answer)) {
			throw new I2Exception("Invalid answer value $answer attempted to be recorded in PollQuestion");
		}

		if ($I2_SQL->query('SELECT COUNT(*) FROM poll_votes WHERE pid=%d AND qid=%d AND uid=%d', $this->mypid, $this->myqid, $user->uid)->fetch_single_value() == 0) {
			// user has not yet voted; create a new row
			$I2_SQL->query('INSERT INTO poll_votes SET answer=%s, pid=%d, qid=%d, uid=%d', $answer, $this->mypid, $this->myqid, $user->uid);
		}
		else {
			// user has voted; change vote
			$I2_SQL->query('UPDATE poll_votes SET answer=%s WHERE pid=%d AND qid=%d AND uid=%d', $answer, $this->mypid, $this->myqid, $user->uid);
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

		$res = $I2_SQL->query('SELECT answer FROM poll_votes WHERE qid=%d AND uid=%d', $this->qid, $user->uid);

		$ret = array();
		while ($res->more_rows()) {
			$ret[] = $res->fetch_array();
		}
		return $ret;
	}
	
	/**
	* Determines what users voted for the question
	*
	* @return array The {@link User}s who voted
	*/
	public function users_who_voted() {
		global $I2_SQL;

		Poll::check_admin();

		$ret = array();

		$res = $I2_SQL->query('SELECT uid FROM poll_votes WHERE qid=%d', $this->qid);
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
	public static function question_exists($qid) {
		global $I2_SQL;

		if ($I2_SQL->query('SELECT COUNT(*) FROM poll_questions WHERE qid=%d', $qid)->fetch_single_value() == 0) {
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
