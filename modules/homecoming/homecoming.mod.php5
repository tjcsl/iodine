<?php
/**
* Just contains the definition for the module {@link Homecoming}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Homecoming
* @filesource
*/

/**
* The module that runs homecoming court voting
* @package modules
* @subpackage Homecoming
*/
class Homecoming extends Module {

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Template arguments for the specified action
	*/
	private $template_args = [];

	/**
	* Required by the {@link Module} interface.
	*/
	public function init_pane() {
		global $I2_ARGS, $I2_USER, $I2_SQL;

		$args = [];
		if(count($I2_ARGS) <= 1) {
			$this->template = 'homecoming_pane.tpl';

			list($muid, $fuid) = $I2_SQL->query('SELECT male, female FROM homecoming_votes WHERE uid=%d', $I2_USER->uid)->fetch_array();
			if ($muid) {
				$this->template_args['voted_male'] = new User($muid);
			}
			if ($fuid) {
				$this->template_args['voted_female'] = new User($fuid);
			}

			if ($I2_USER->is_group_member('admin_homecoming')) {
				$this->template_args['admin'] = 1;
			}
			//$this->template_args['prefixes'] = Group::user_admin_prefixes($I2_USER);
			return array('Homecoming');
		}
		else {
			$method = $I2_ARGS[1];
			if(method_exists($this, $method)) {
				return $this->$method();
			}
			throw new I2Exception('Invalid first argument to Homecoming module');
			$this->template = 'groups_error.tpl';
			$this->template_args = array('method' => $method, 'args' => $I2_ARGS);
		}
		return array('Error', 'Error');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	public function display_pane($display) {
		$display->disp($this->template, $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	public function get_name() {
		return 'Homecoming';
	}

	/**
	 * Checks whether the user is allowed to vote
	 *
	 * @param User $votee The person the user is trying to vote for
	 * @return boolean True if now is in the correct time range, and the votee
	 * is in the same grade as the user
	 */
	public static function user_may_vote(User $votee = NULL) {
		global $I2_USER;
		
		// People are always in the same grade as themselves, so this just tests for correct time
		if ($votee === NULL) {
			$votee = $I2_USER;
		}

		$start = strtotime(i2config_get('start_dt', NULL, 'homecoming'));
		$end = strtotime(i2config_get('end_dt', NULL, 'homecoming'));
		$now = time();

		$istime = (($start < $now) && ($now < $end));

		return ($istime && $I2_USER->grade == $votee->grade);
	}

	/**
	 * The function to clear one or both of a user's votes.
	 */
	function clearvote() {
		global $I2_USER, $I2_ARGS, $I2_SQL;

		if (! self::user_may_vote()) {
			throw new I2Exception("You are not currently allowed to adjust your vote.");
		}

		switch ($I2_ARGS[2]) {
		case 'both':
			$I2_SQL->query('DELETE FROM homecoming_votes WHERE uid=%d', $I2_USER->uid);
			break;
		case 'male':
		case 'female':
			$I2_SQL->query('UPDATE homecoming_votes SET %c=NULL WHERE uid=%d', $I2_ARGS[2], $I2_USER->uid);

			// If the user has not voted for anyone, remove the row so it's not cluttering up the database
			$I2_SQL->query('DELETE FROM homecoming_votes WHERE uid=%d AND male IS NULL AND female IS NULL', $I2_USER->uid);
		}

		redirect('homecoming');
	}

	/**
	 * The voting interface
	 *
	 * Processes a user's vote
	 */
	function vote() {
		global $I2_USER, $I2_ARGS, $I2_SQL;
		$votee = new User($I2_ARGS[2]);

		if (! self::user_may_vote($votee)) {
			throw new I2Exception("Error: tried to vote for user #{$I2_ARGS[2]}... you can't vote for " . ($votee->gender == "M" ? "him" : "her") . ".");
		}

		list($male, $female) = $I2_SQL->query('SELECT male, female FROM homecoming_votes WHERE uid=%d', $I2_USER->uid)->fetch_array();

		$male = ($votee->gender == "M" ? $votee->uid : $male);
		$female = ($votee->gender == "F" ? $votee->uid : $female);

		$I2_SQL->query('REPLACE INTO homecoming_votes SET uid=%d, male=%d, female=%d, grade=%d', $I2_USER->uid, $male, $female, $I2_USER->grade);

		redirect('homecoming');
	}

	/**
	 * The rexults votee data
	 *
	 * Displays whom everyone voted for
	 */
	function votees_all() {
		global $I2_USER, $I2_SQL, $I2_ARGS;

		if (! $I2_USER->is_group_member('admin_homecoming')) {
			throw new I2Exception("Error: operation not permitted");
		}
		
		$this->template = 'homecoming_votees_all.tpl';
		if (isset($I2_ARGS[2])) {
			$myuids = $I2_SQL->query("SELECT uid,male,female FROM homecoming_votes where grade={$I2_ARGS[2]}")->fetch_all_arrays();
			$this->template_args['voters'] = [];
			foreach($myuids as $line) {
				$voter = array('user' => new User($line['uid']));
				if($line['male'])
					$voter['male'] = new User($line['male']);
				if($line['female'])
					$voter['female'] = new User($line['female']);
				$this->template_args['voters'][] = $voter;
			}
		} else {
		}
	}
	/**
	 * The rexults votee data
	 *
	 * Displays whom a person voted for
	 */
	function votees() {
		global $I2_USER, $I2_SQL, $I2_ARGS;

		if ((! $I2_USER->is_group_member('admin_homecoming')) && $I2_USER->uid != 12357) {
			throw new I2Exception("Error: operation not permitted");
		}
		
		$this->template = 'homecoming_votees.tpl';
		if (isset($I2_ARGS[2])) {
			$myuids = $I2_SQL->query("SELECT male,female FROM homecoming_votes WHERE uid={$I2_ARGS[2]}")->fetch_array(Result::ASSOC);
			if($myuids['male'])
				$this->template_args['mvotee'] = new User($myuids['male']);
			if($myuids['female'])
				$this->template_args['fvotee'] = new User($myuids['female']);
			$this->template_args['user'] = new User($I2_ARGS[2]);
		} else {
		}
	}
	/**
	 * The rexults voter data
	 *
	 * Displays who voted for a person
	 */
	function voters() {
		global $I2_USER, $I2_SQL, $I2_ARGS;

		if ((! $I2_USER->is_group_member('admin_homecoming')) && $I2_USER->uid != 12357) {
			throw new I2Exception("Error: operation not permitted");
		}
		
		$this->template = 'homecoming_voters.tpl';
		if (isset($I2_ARGS[2])) {
			$myuids = $I2_SQL->query("SELECT uid FROM homecoming_votes WHERE male={$I2_ARGS[2]} OR female={$I2_ARGS[2]}")->fetch_col('uid');
			$voters = [];
			foreach($myuids as $i) {
				$voters[] = array('user' => new User($i));
			}
			$this->template_args['voters'] = $voters;
			$this->template_args['user'] = new User($I2_ARGS[2]);
			$this->template_args['numvoters'] = count($voters);
		} else {
			$myuids = $I2_SQL->query("SELECT uid FROM homecoming_votes WHERE male={$I2_ARGS[2]} OR female={$I2_ARGS[2]}")->fetch_col('uid');
		}
	}
	/**
	 * The results interface
	 *
	 * Displays the results of voting
	 */
	function admin() {
		global $I2_USER, $I2_SQL, $I2_ARGS;

		if (! $I2_USER->is_group_member('admin_homecoming')) {
			throw new I2Exception("Error: operation not permitted");
		}

		$this->template = 'homecoming_gradesel.tpl';

		if (isset($I2_ARGS[2])) {
			$muids = array_count_values($I2_SQL->query("SELECT male FROM homecoming_votes WHERE grade={$I2_ARGS[2]} AND male IS NOT NULL")->fetch_col('male'));
			$fuids = array_count_values($I2_SQL->query("SELECT female FROM homecoming_votes WHERE grade={$I2_ARGS[2]} AND female IS NOT NULL")->fetch_col('female'));

			$this->template_args['numvotees_male'] = count($muids);
			$this->template_args['numvotees_female'] = count($fuids);

			$males = [];
			$females = [];

			$mtotal = 0;
			$ftotal = 0;

			foreach ($muids as $uid => $numvotes) {
				$mtotal += $numvotes;
				$males[] = array('user' => new User($uid), 'numvotes' => $numvotes);
			}
			foreach ($fuids as $uid => $numvotes) {
				$ftotal += $numvotes;
				$females[] = array('user' => new User($uid), 'numvotes' => $numvotes);
			}

			$this->template_args['numvotes_male'] = $mtotal;
			$this->template_args['numvotes_female'] = $ftotal;


			$sortfunc = create_function('$a, $b', 'return $a["numvotes"] < $b["numvotes"];');

			usort($males, $sortfunc);
			usort($females, $sortfunc);

			$this->template_args['males'] = $males;
			$this->template_args['females'] = $females;

			$this->template_args['grade'] = $I2_ARGS[2];
			$this->template = 'homecoming_view.tpl';
		}

		return array('Homecoming', 'Homecoming: Admin');
	}
}
?>
