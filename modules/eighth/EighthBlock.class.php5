<?php
/**
* Just contains the definition for the class {@link EighthBlock}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @package modules
* @subpackage Eighth
* @filesource
*/

/**
* The module that holds the definition for an eighth period block.
* @package modules
* @subpackage Eighth
*/

class EighthBlock {

	private $data = array();

	/**
	* The constructor for the {@link EighthBlock} class.
	*
	* @access public
	* @param int $activityid The activity ID.
	*/
	public function __construct($blockid) {
		global $I2_SQL;
		$this->data = $I2_SQL->query('SELECT * FROM eighth_blocks WHERE bid=%d', $blockid)->fetch_array(Result::ASSOC);
	}

	/**
	* Adds a block to the list.  If a block with the given name already exists, its blockid is returned.
	*
	* @access public
	* @param string $date The date of the block.
	* @param string $block The block name (may be an arbitrary string, but equality is case-sensitive.
	*	block 'foo' is different from block 'FOO', and both may be on the same day.
	* @return int The BlockID of the created block, or the BlockID of the already-extant one if present.
	*/
	public static function add_block($date, $block, $schedule_default = TRUE) {
		global $I2_SQL,$I2_LDAP;
		/*
		** No harm in letting someone pretend they've created a block
		*/
		$res = $I2_SQL->query('SELECT bid FROM eighth_blocks WHERE date=%t AND block=%s', $date, $block)->fetch_single_value();
		if($res) {
			return $res;
		}
		Eighth::check_admin();
		Eighth::start_undo_transaction();
		$query = 'INSERT INTO eighth_blocks (date,block) VALUES (%t,%s)';
		$queryarg = array($date,$block);
		$result = $I2_SQL->query_arr($query, $queryarg);
		$invquery = 'DELETE FROM eighth_blocks WHERE date=%t AND block=%s';
		$query = 'INSERT INTO eighth_blocks (date,block,bid) VALUES (%t,%s,%d)';
		$bid = $result->get_insert_id();
		$newarg = array($date,$block,$bid);
		Eighth::push_undoable($query,$newarg,$invquery,$queryarg,'Add Block');
		$default_aid = i2config_get('default_aid', 3, 'eighth');
		$activity = new EighthActivity($default_aid);
		//schedule the default activity

		if ($schedule_default) {
			EighthSchedule::schedule_activity($bid, $default_aid, $activity->sponsors, $activity->rooms);
			//add all students to default activity
			$uids = flatten_values($I2_LDAP->search('ou=people', '(objectClass=tjhsstStudent)', 'iodineUidNumber')->fetch_all_arrays(Result::NUM));
			$activity = new EighthActivity($default_aid, $bid);
			$activity->add_members($uids, TRUE);
		}

		Eighth::end_undo_transaction();
		return $bid;
	}

	/**
	* Removes a block from the list.
	*
	* @access public
	* @param int $blockid The block ID.
	* @todo Cope with the implications of removing a block
	*/
	public static function remove_block($blockid) {
		global $I2_SQL;
		Eighth::check_admin();
		$result = $I2_SQL->query('DELETE FROM eighth_blocks WHERE bid=%d', $blockid);
		//FIXME: Deal with removing a block
	}

	/**
	* Removes this block from the list.
	*
	* @access public
	*/
	public function remove() {
		$this->remove_block($this->data['bid']);
	}

	/**
	* Gets a list of all the blocks.
	*
	* @access public
	* @param string $starting_date The starting date for the list, usually NULL.
	* @param int $number_of_days The number of days to return.
	*/
	public static function get_all_blocks($starting_date = NULL, $number_of_days = 9999) {
		global $I2_SQL;
		if($starting_date == NULL) {
			$starting_date = i2config_get('start_date', date('Y-m-d'), 'eighth');
		}
		return $I2_SQL->query('SELECT * FROM eighth_blocks WHERE date >= %t AND date <= ADDDATE(%t, INTERVAL %d DAY) ORDER BY date,block', $starting_date, $starting_date, $number_of_days)->fetch_all_arrays(Result::ASSOC);
	}

	/**
	* The magic __get function.
	*
	* @access public
	* @param string $name The name of the field to get.
	*/
	public function __get($name) {
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}
	}

	/**
	* The magic __set function.
	*
	* @access public
	* @param string $name The name of the field to set.
	* @param mixed $value The value to assign to the field.
	*/
	public function __set($name, $value) {
		global $I2_SQL;
		Eighth::check_admin();
		if($name == 'date') {
			$result = $I2_SQL->query('UPDATE eighth_blocks SET date=%t WHERE bid=%d', $value, $this->data['bid']);
			$this->data['date'] = $value;
		}
		else if($name == 'block') {
			$result = $I2_SQL->query('UPDATE eighth_blocks SET block=%s WHERE bid=%d', $value, $this->data['bid']);
			$this->data['block'] = $value;
		}
		else if($name == 'locked') {
			$result = $I2_SQL->query('UPDATE eighth_blocks SET locked=%d WHERE bid=%d', (int)$value, $this->data['bid']);
			$this->data['locked'] = (bool)$value;
		}
	}
}

?>
