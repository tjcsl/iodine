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

	private $mytype;

	public function __get($var) {
		global $I2_SQL;
		switch($var) {
			case 'mypid':
			case 'pid':
				return (int)($this->myqid/1000);
			case 'qid':
				return $this->myqid;
			case 'maxvotes':
				return $this->mymaxvotes;
			case 'question':
				return $this->myquestion;
			case 'answers':
				return $this->myanswers;
			case 'type':
				return $this->mytype;
			default:
				throw new I2Exception("Invalid variable $var attempted to be accessed in PollQuestion");
		}
	}

	public static function to_qid($pid, $qnum) {
			  return 1000*$pid+$qnum;
	}

	public function __construct($qid) {
		global $I2_SQL, $I2_LOG;
		
		//$qid = self::to_qid($pid,$qnum);

		if (! self::question_exists($qid)) {
			throw new I2Exception("Invalid PollQuestion attempted to be created with qid $qid");
		}

		$info = $I2_SQL->query('SELECT maxvotes, question, answertype FROM poll_questions WHERE qid=%d', $qid)->fetch_array(Result::ASSOC);

		$this->myqid = $qid;
		$this->mytype = $info['answertype'];

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
			  return self::lower_bound($qid) + 1000;
	}

	/**
	* Gets the minimum value an aid may hold while still belonging to a question, minus 1.
	*/
	private static function lower_bound($qid) {
			  return 1000 * $qid;
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
									self::lower_bound($qid),self::upper_bound($qid))->fetch_all_arrays(Result::ASSOC);
			  } else {
						 return $I2_SQL->query(
									'SELECT aid,answer FROM poll_votes WHERE uid=%d AND aid >= %d AND aid < %d',
									$uid,self::lower_bound($qid),self::upper_bound($qid))->fetch_all_arrays(Result::ASSOC);
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
						 Poll::check_admin();
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
	* Gets the text of an answer
	*
	* @param int $aid The answerid
	* @return string The answer's text
	*/
	public static function get_answer_text($aid) {
			  global $I2_SQL;
			  return $I2_SQL->query('SELECT answer FROM poll_answers WHERE aid=%d',$aid)->fetch_single_value();
	}

	/**
	* Set the answers
	*
	* @param array $answers The options from which a voter can choose
	*/
	public function set_answers($answers) {
		global $I2_SQL;

		Poll::check_admin();

		$I2_SQL->query('DELETE FROM poll_answers WHERE aid > %d AND aid < %d', self::lower_bound($this->qid), self::upper_bound($this->qid));
		$this->answers = array();
		$ct = self::lower_bound($this->qid)+1;
		foreach ($answers as $answer) {
			$I2_SQL->query('INSERT INTO poll_answers SET aid=%d, answer=%s', $ct, $answer);
			$ct++;
			$this->answers[] = $answer;
		}
	}

	public static function first_unused_aid($qid) {
			  global $I2_SQL;
			  $res = $I2_SQL->query('SELECT MAX(aid) FROM poll_answers WHERE aid > %d AND aid < %d',self::lower_bound($qid),self::upper_bound($qid))->fetch_single_value();
			  if ($res >= self::upper_bound($qid)) {
						 throw new I2Exception('Too many answers to question!');
			  }
			  if (!$res) {
						 $res = self::lower_bound($qid)+1;
			  } else {
						 $res += 1;
			  }
			  return $res;
	}

	/**
	* Adds an answer to a question
	*/
	public static function add_answer_to_question($qid, $answertext) {
			  global $I2_SQL;
			  Poll::check_admin();
			  $aid = self::first_unused_aid($qid);
			  $I2_SQL->query('INSERT INTO poll_answers SET aid=%d,answer=%s',$aid,$answertext);
	}

	public function add_answer($answertext) {
			  return self::add_answer_to_question($this->myqid,$answertext);
	}

	/**
	* Changes an answer's text
	*/
	public static function change_answer($aid,$newtext) {
			  global $I2_SQL;
			  Poll::check_admin();
			  $I2_SQL->query('UPDATE poll_answers SET answer=%s WHERE aid=%d',$newtext,$aid);
	}

	/**
	* Returns whether the given answerid refers to this question
	*
	* @param int $aid The answerid in question
	* @return bool TRUE if the answer pertains to the question
	*/
	public function is_answer($aid) {
			  return $aid >= self::lower_bound($this->qid) && $aid < self::upper_bound($this->qid);
	}

	/**
	* Vote for the question
	*
	* @param int $answer The answerid (should be pollid+000 for freeresponse/text)
	* @param string $answertext The text of the user's answer (optional)
	* @param User $user The user voting; defaults to the current user
	*/
	public function record_vote($answer, $answertext=NULL, $user=NULL) {
		global $I2_USER, $I2_SQL, $I2_LOG;

		d('Voting for '.$answer,4);

		if (!$user) {
			$user = $I2_USER;
		}

		$user = new User($user);
		if ($user->uid != $I2_USER->uid) {
				  Poll::check_admin();
		}

		$mypoll = new Poll($this->mypid);
		
		//$I2_LOG->log_file('User '.$I2_USER->uid.' voting: poll '.$this->mypid.' : '.$mypoll->name);
		//d('User '.$I2_USER->uid.' voting: poll '.$this->mypid.' : '.$mypoll->name,4);

		// Invalid poll
		if (!$mypoll->name) {
			return;
		}
		
		if (!$mypoll->user_can_access()) {
				  Poll::check_admin();
		}

		if (! is_numeric($answer)) {
			throw new I2Exception("Invalid answer value $answer attempted to be recorded in PollQuestion");
		}

		$numvotes = $I2_SQL->query('SELECT COUNT(*) FROM poll_votes WHERE aid >= %d AND aid < %d AND uid=%d', self::lower_bound($this->myqid), self::upper_bound($this->myqid), $user->uid)->fetch_single_value();

		if ($this->type != 'approval') {
				  // Delete previous votes
				  $I2_SQL->query('DELETE FROM poll_votes WHERE aid >= %d AND aid < %d AND uid=%d',self::lower_bound($this->myqid),self::upper_bound($this->myqid),$user->uid);
		}

		if ($this->maxvotes == 0 || $numvotes < $this->maxvotes) {
				  // user may still vote here; create a new vote
			$I2_SQL->query('REPLACE INTO poll_votes SET answer=%s, aid=%d, uid=%d', $answertext, $answer, $user->uid);
		}
		else  {
			throw new I2Exception('You have attempted to vote too many times for one question!');
		}
	}

	/**
	* Removes a user's vote.
	*/
	public function delete_vote($aid, $user=NULL) {
			  global $I2_USER,$I2_SQL;
			  if (!$user) {
						 $user = $I2_USER;
			  }
			  $user = new User($user);
			  if ($user->uid != $I2_USER->uid) {
						 Poll::check_admin();
			  }
			  return $I2_SQL->query('DELETE FROM poll_votes WHERE aid=%d AND uid=%d',$aid,$user->uid);
	}

	/**
	* Determine if a user voted for a particular answer
	*
	* @param int $aid The ID number of the answer
	* @param User $user The user (defaults to the current user)
	* @return mixed The user's answer if they voted for the choice (and it's freeresponse/essay), or FALSE
	*/
	public function user_voted_for($aid, $user=NULL) {
		global $I2_USER, $I2_SQL, $I2_LOG;

		if (!$user) {
			$user = $I2_USER;
		}

		if ($user->uid != $I2_USER->uid) {
				  Poll::check_admin();
		}

		if ($this->type == 'approval' || $this->type == 'standard') {
				  // We need to translate the value into TRUE or FALSE
				  $res = $I2_SQL->query('SELECT COUNT(aid) FROM poll_votes WHERE aid=%d AND uid=%d', $aid, $user->uid)->fetch_single_value();
				  if ($res > 1) {
							 $I2_LOG->log_file('VOTING FRAUD!  User: '.$user.' Answer: '.$aid.'.  Detected and logged.');
							 throw new I2Exception('VOTING FRAUD!  User: '.$user.' Answer: '.$aid.'.  Detected and logged.');
				  }
				  return ($res == 0)?FALSE:TRUE;
		}
		$res = $I2_SQL->query('SELECT answer FROM poll_votes WHERE aid=%d AND uid=%d', $aid, $user->uid)->fetch_single_value();
		return $res;
	}


	/**
	* Gets the number of votes for a particular answer
	*/
	public static function get_num_votes($aid) {
			  global $I2_SQL;
			  Poll::check_admin();
			  $num = $I2_SQL->query('SELECT COUNT(uid) FROM poll_votes WHERE aid=%d',$aid)->fetch_single_value();
			  return $num?$num:0;
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

		$res = $I2_SQL->query('SELECT uid FROM poll_votes WHERE aid >= %d AND aid < %d', self::lower_bound($this->qid),self::upper_bound($this->qid));
		foreach ($res->fetch_col('uid') as $uid) {
			$ret[] = new User($uid);
		}

		return $ret;
	}

	/**
	* Gets the first aid for this question which is not already in use.
	*/
	public function first_available_aid() {
			  global $I2_SQL;
			  $max = $I2_SQL->query('SELECT MAX(aid) FROM poll_answers WHERE aid >= %d AND aid < %d',self::lower_bound($this->qid),self::upper_bound($this->qid))->fetch_single_value();
			  if ($max >= self::upper_bound($this->qid)) {
						 throw new I2Exception('Too many questions in one poll!');
			  }
			  if (!$max) {
						 return self::lower_bound($this->qid)+1;
			  }
			  return $max+1;
	}

	/**
	* Determine whether a poll question exists
	*
	* @param int $qid The ID number of the question
	*
	* @return boolean True if the question exists
	*/
	public static function question_exists($qid) {
		global $I2_SQL;

		if ($I2_SQL->query('SELECT COUNT(*) FROM poll_questions WHERE qid=%d', $qid)->fetch_single_value() == 0) {
			return FALSE;
		}

		return TRUE;
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

		Poll::check_admin();

		$qid = $I2_SQL->query('SELECT MAX(qid) FROM poll_questions WHERE qid > %d AND qid < %d ORDER BY qid DESC', Poll::lower_bound($pid), Poll::upper_bound($pid))->fetch_single_value();

		if (!$qid) {
				  $qid = Poll::lower_bound($pid)+1;
		} else {
				  $qid += 1;
		}

		$I2_SQL->query('INSERT INTO poll_questions SET qid=%d, question=%s, maxvotes=%d', $qid, $question, $maxvotes);
      $aid = self::lower_bound($qid);
		foreach ($answers as $answer) {
			$I2_SQL->query('INSERT INTO poll_answers SET aid=%d, answer=%s', $aid, $answer);
			$aid++;
		}
	}

	public static function delete_answer($aid) {
		global $I2_SQL;
		Poll::check_admin();
		$I2_SQL->query('DELETE FROM poll_answers WHERE aid=%d',$aid);
	}

	/**
	* Deletes a question in the database
	*
	* This deletes a question from the database; it does NOT remove it from any pre-existing {@link Poll}
	* objects. Usually the {@link Poll} delete_question method, which calls this, should be used instead.
	*
	* @param int $qid The ID number of the question
	*/
	public static function delete_question($qid) {
		global $I2_SQL;

		Poll::check_admin();

		$I2_SQL->query('DELETE FROM poll_questions WHERE qid=%d', $qid);
		$I2_SQL->query('DELETE FROM poll_answers WHERE aid > %d AND aid < %d', self::lower_bound($qid), self::upper_bound($qid));
		$I2_SQL->query('DELETE FROM poll_votes WHERE aid >= %d AND aid < %d', self::lower_bound($qid), self::upper_bound($qid));
	}
}
?>
